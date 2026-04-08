<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class PagoController extends Controller
{
    // Planes disponibles: precio mensual y anual en USD
    private array $planes = [
        'basico'      => ['precio' => 12, 'precio_anual' => 9,  'nombre' => 'Plan Básico',      'proyectos' => 10],
        'profesional' => ['precio' => 29, 'precio_anual' => 24, 'nombre' => 'Plan Profesional', 'proyectos' => 25],
        'enterprise'  => ['precio' => 65, 'precio_anual' => 59, 'nombre' => 'Plan Enterprise',  'proyectos' => 100],
    ];

    // Jerarquía de planes (mayor número = mayor plan)
    private array $jerarquia = [
        'gratis'      => 0,
        'basico'      => 1,
        'profesional' => 2,
        'enterprise'  => 3,
    ];

    private function calcularNuevaExpiracion(string $planActual, string $planNuevo, string $periodo = 'mensual'): \Illuminate\Support\Carbon
    {
        $user = Auth::user();
        $meses = $periodo === 'anual' ? 12 : 1;

        // Mismo plan: sumar meses a la expiración actual (o desde hoy si ya venció)
        if ($planActual === $planNuevo) {
            $base = $user->plan_expires_at && $user->plan_expires_at->isFuture()
                ? $user->plan_expires_at
                : now();
            return $base->addMonths($meses);
        }

        // Subir de plan: sumar meses desde hoy
        return now()->addMonths($meses);
    }

    // ──────────────────────────────────────────────
    //  MOSTRAR PÁGINA DE CHECKOUT
    // ──────────────────────────────────────────────
    public function checkout(string $plan)
    {
        abort_unless(array_key_exists($plan, $this->planes), 404);

        $user = Auth::user();
        $jerarquiaActual = $this->jerarquia[$user->plan] ?? 0;
        $jerarquiaNueva  = $this->jerarquia[$plan];

        if ($jerarquiaNueva < $jerarquiaActual) {
            return redirect()->route('home')->with('error_plan', 'No podés bajar a un plan inferior. Tu plan actual es ' . $user->planLabel() . '.');
        }

        $estaRenovando = $user->plan === $plan;
        $periodo = request('periodo', 'mensual'); // 'mensual' o 'anual'

        $detalle = $this->planes[$plan];
        $detalle['precio_efectivo'] = $periodo === 'anual' ? $detalle['precio_anual'] : $detalle['precio'];
        $detalle['total_cobro'] = $periodo === 'anual'
            ? $detalle['precio_anual'] * 12
            : $detalle['precio'];

        return view('planes.checkout', [
            'plan'          => $plan,
            'detalle'       => $detalle,
            'estaRenovando' => $estaRenovando,
            'periodo'       => $periodo,
        ]);
    }

    // ──────────────────────────────────────────────
    //  INICIAR PAGO CON MERCADOPAGO
    // ──────────────────────────────────────────────
    public function mercadopago(Request $request, string $plan)
    {
        abort_unless(array_key_exists($plan, $this->planes), 404);

        if (empty(config('services.mercadopago.access_token'))) {
            return back()->with('error', 'MercadoPago no está configurado. Contactá al administrador.');
        }

        $periodo = $request->input('periodo', 'mensual');
        $detalle = $this->planes[$plan];
        $precioACobrar = $periodo === 'anual'
            ? $detalle['precio_anual'] * 12
            : $detalle['precio'];
        $descripcion = $detalle['nombre'] . ' — Rubra (' . ($periodo === 'anual' ? '1 año' : '1 mes') . ')';

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        try {
            $client = new PreferenceClient();

            $preferenceData = [
                'items' => [[
                    'title'       => $descripcion,
                    'quantity'    => 1,
                    'unit_price'  => (float) $precioACobrar,
                    'currency_id' => config('services.mercadopago.currency', 'UYU'),
                ]],
                'back_urls' => [
                    'success' => route('pago.success', ['plan' => $plan, 'gateway' => 'mp', 'periodo' => $periodo]),
                    'failure' => route('pago.failure', ['plan' => $plan]),
                    'pending' => route('pago.pending', ['plan' => $plan]),
                ],
                'statement_descriptor' => 'RUBRA',
                'metadata' => [
                    'user_id' => Auth::id(),
                    'plan'    => $plan,
                    'periodo' => $periodo,
                ],
            ];

            // auto_return solo funciona con URLs públicas (producción)
            if (app()->environment('production')) {
                $preferenceData['auto_return'] = 'approved';
            }

            $preference = $client->create($preferenceData);

            $url = app()->environment('production')
                ? $preference->init_point
                : $preference->sandbox_init_point;

            return redirect($url);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $body = $apiResponse ? json_encode($apiResponse->getContent(), JSON_PRETTY_PRINT) : 'sin respuesta';
            \Illuminate\Support\Facades\Log::error('MercadoPago API Error', ['response' => $body]);
            return back()->with('error', 'Error MercadoPago: ' . $body);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con MercadoPago: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    //  INICIAR PAGO CON PAYPAL
    // ──────────────────────────────────────────────
    public function paypal(Request $request, string $plan)
    {
        abort_unless(array_key_exists($plan, $this->planes), 404);

        if (empty(config('services.paypal.client_id')) || empty(config('services.paypal.client_secret'))) {
            return back()->with('error', 'PayPal no está configurado. Contactá al administrador.');
        }

        $periodo = $request->input('periodo', 'mensual');
        $detalle  = $this->planes[$plan];
        $precioACobrar = $periodo === 'anual'
            ? $detalle['precio_anual'] * 12
            : $detalle['precio'];
        $descripcion = $detalle['nombre'] . ' — Rubra (' . ($periodo === 'anual' ? '1 año' : '1 mes') . ')';
        $baseUrl  = config('services.paypal.base_url');
        $clientId = config('services.paypal.client_id');
        $secret   = config('services.paypal.client_secret');

        // 1. Obtener token OAuth
        $tokenResponse = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        if (!$tokenResponse->successful()) {
            return back()->with('error', 'No se pudo conectar con PayPal. Intentá más tarde.');
        }

        $accessToken = $tokenResponse->json('access_token');

        // 2. Crear orden
        $orderResponse = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'description' => $descripcion,
                    'amount'      => [
                        'currency_code' => 'USD',
                        'value'         => number_format($precioACobrar, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url'  => route('pago.success', ['plan' => $plan, 'gateway' => 'paypal', 'periodo' => $periodo]),
                    'cancel_url'  => route('pago.failure', ['plan' => $plan]),
                    'brand_name'  => 'Rubra',
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (!$orderResponse->successful()) {
            return back()->with('error', 'Error al crear la orden de PayPal. Intentá más tarde.');
        }

        // 3. Redirigir al link de aprobación de PayPal
        $approveLink = collect($orderResponse->json('links'))
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approveLink) {
            return back()->with('error', 'No se pudo obtener el link de pago de PayPal.');
        }

        return redirect($approveLink);
    }

    // ──────────────────────────────────────────────
    //  CONFIRMAR MANUAL (solo en local/desarrollo)
    // ──────────────────────────────────────────────
    public function confirmarManual(Request $request, string $plan)
    {
        abort_unless(app()->environment('local', 'development'), 403);
        abort_unless(array_key_exists($plan, $this->planes), 404);

        $user = Auth::user();
        $periodo = $request->input('periodo', 'mensual');

        $jerarquiaActual = $this->jerarquia[$user->plan] ?? 0;
        $jerarquiaNueva  = $this->jerarquia[$plan];

        if ($jerarquiaNueva < $jerarquiaActual) {
            return redirect()->route('configuracion')->with('error_plan', 'No podés bajar a un plan inferior.');
        }

        $nuevaExpiracion = $this->calcularNuevaExpiracion($user->plan, $plan, $periodo);
        $esRenovacion    = $user->plan === $plan;

        $user->update([
            'plan'            => $plan,
            'plan_expires_at' => $nuevaExpiracion,
            'plan_periodo'    => $periodo,
        ]);

        $msg = $esRenovacion
            ? '¡' . $this->planes[$plan]['nombre'] . ' renovado manualmente! Válido hasta el ' . $nuevaExpiracion->format('d/m/Y') . '.'
            : '¡Plan activado manualmente! ' . $this->planes[$plan]['nombre'] . '. Válido hasta el ' . $nuevaExpiracion->format('d/m/Y') . '.';

        return redirect()->route('configuracion')->with('success_plan', $msg);
    }

    // ──────────────────────────────────────────────
    //  ÉXITO — activar plan
    // ──────────────────────────────────────────────
    public function success(Request $request, string $plan)
    {
        abort_unless(array_key_exists($plan, $this->planes), 404);

        $user    = Auth::user();
        $gateway = $request->query('gateway', 'mp');

        // Si es PayPal hay que capturar la orden primero
        if ($gateway === 'paypal') {
            $orderId = $request->query('token');

            \Illuminate\Support\Facades\Log::info('PayPal success callback', [
                'plan'    => $plan,
                'orderId' => $orderId,
                'all'     => $request->all(),
            ]);

            if (!$orderId) {
                return redirect()->route('configuracion')->with('error_plan', 'Pago PayPal no confirmado. No se recibió el token de orden.');
            }

            $baseUrl  = config('services.paypal.base_url');
            $clientId = config('services.paypal.client_id');
            $secret   = config('services.paypal.client_secret');

            $tokenResponse = Http::withBasicAuth($clientId, $secret)
                ->asForm()
                ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            if (!$tokenResponse->successful()) {
                \Illuminate\Support\Facades\Log::error('PayPal OAuth failed', ['response' => $tokenResponse->body()]);
                return redirect()->route('configuracion')->with('error_plan', 'No se pudo verificar el pago con PayPal.');
            }

            $captureResponse = Http::withToken($tokenResponse->json('access_token'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withBody('{}', 'application/json')
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            \Illuminate\Support\Facades\Log::info('PayPal capture response', [
                'status' => $captureResponse->status(),
                'body'   => $captureResponse->json(),
            ]);

            if (!$captureResponse->successful() || $captureResponse->json('status') !== 'COMPLETED') {
                $error = $captureResponse->json('details.0.description') ?? $captureResponse->json('message') ?? 'El pago no fue completado.';
                return redirect()->route('configuracion')->with('error_plan', 'PayPal: ' . $error);
            }
        }

        // Verificar que no sea un downgrade
        $jerarquiaActual = $this->jerarquia[$user->plan] ?? 0;
        $jerarquiaNueva  = $this->jerarquia[$plan];

        if ($jerarquiaNueva < $jerarquiaActual) {
            return redirect()->route('configuracion')->with('error_plan', 'No podés bajar a un plan inferior.');
        }

        $periodo = $request->query('periodo', 'mensual');
        $nuevaExpiracion = $this->calcularNuevaExpiracion($user->plan, $plan, $periodo);
        $esRenovacion    = $user->plan === $plan;

        $user->update([
            'plan'            => $plan,
            'plan_expires_at' => $nuevaExpiracion,
            'plan_periodo'    => $periodo,
        ]);

        $msg = $esRenovacion
            ? '¡' . $this->planes[$plan]['nombre'] . ' renovado! Válido hasta el ' . $nuevaExpiracion->format('d/m/Y') . '.'
            : '¡Plan activado con éxito! Bienvenido al ' . $this->planes[$plan]['nombre'] . '. Válido hasta el ' . $nuevaExpiracion->format('d/m/Y') . '.';

        return redirect()->route('configuracion')->with('success_plan', $msg);
    }

    // ──────────────────────────────────────────────
    //  FALLO
    // ──────────────────────────────────────────────
    public function failure(Request $request, string $plan)
    {
        return redirect()->route('configuracion')->with('error_plan', 'El pago no fue completado. Podés intentarlo nuevamente.');
    }

    // ──────────────────────────────────────────────
    //  PENDIENTE (MP)
    // ──────────────────────────────────────────────
    public function pending(Request $request, string $plan)
    {
        return redirect()->route('configuracion')->with('error_plan', 'Tu pago está pendiente de acreditación. Te avisaremos cuando se confirme.');
    }

    // ──────────────────────────────────────────────
    //  WEBHOOK MERCADOPAGO (IPN)
    // ──────────────────────────────────────────────
    public function webhookMercadopago(Request $request)
    {
        // ── Verificación de firma HMAC (x-signature) ──────────────────────
        // MercadoPago envía: x-signature: ts=<timestamp>,v1=<hmac>
        // La firma se calcula sobre: "ts:{ts};url:{url};"  o
        //                            "ts:{ts};url:{url};key:{data_id};"
        $webhookSecret = config('services.mercadopago.webhook_secret');

        if (!empty($webhookSecret)) {
            $xSignature = $request->header('x-signature', '');
            $xRequestId = $request->header('x-request-id', '');

            $ts      = '';
            $v1      = '';
            foreach (explode(',', $xSignature) as $part) {
                [$key, $val] = array_pad(explode('=', trim($part), 2), 2, '');
                if ($key === 'ts') $ts = $val;
                if ($key === 'v1') $v1 = $val;
            }

            $dataId    = $request->query('id') ?? $request->input('data.id', '');
            $signedUrl = $request->url();
            $manifest  = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
            $expected  = hash_hmac('sha256', $manifest, $webhookSecret);

            if (!hash_equals($expected, $v1)) {
                \Illuminate\Support\Facades\Log::warning('MercadoPago webhook: firma inválida', [
                    'expected' => $expected,
                    'received' => $v1,
                ]);
                return response()->json(['ok' => false], 400);
            }
        }

        // ── Procesamiento del pago ────────────────────────────────────────
        $type = $request->query('type') ?? $request->input('type');

        if ($type !== 'payment') {
            return response()->json(['ok' => true]);
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $paymentId = $request->query('id') ?? $request->input('data.id');

        if (!$paymentId || !is_numeric($paymentId)) {
            return response()->json(['ok' => false], 400);
        }

        $response = Http::withToken(config('services.mercadopago.access_token'))
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$response->successful() || $response->json('status') !== 'approved') {
            return response()->json(['ok' => true]);
        }

        $metadata = $response->json('metadata');
        $userId   = $metadata['user_id'] ?? null;
        $plan     = $metadata['plan'] ?? null;
        $periodo  = $metadata['periodo'] ?? 'mensual';

        if ($userId && $plan && array_key_exists($plan, $this->planes)) {
            $meses = $periodo === 'anual' ? 12 : 1;
            \App\Models\User::where('id', $userId)->update([
                'plan'            => $plan,
                'plan_expires_at' => now()->addMonths($meses),
                'plan_periodo'    => $periodo,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
