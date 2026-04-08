<x-app-layout>

<div class="min-h-screen bg-[#0a0a0a] text-white flex items-center justify-center px-4 py-16">

    {{-- FONDO --}}
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden opacity-[0.04]">
        <span class="absolute top-[-5%] left-[-5%] text-[40rem] font-black text-gray-700 leading-none select-none">R</span>
    </div>

    <div class="relative z-10 w-full max-w-lg">

        {{-- HEADER --}}
        <div class="text-center mb-10">
            <a href="{{ url('/#precios') }}" class="inline-flex items-center gap-2 text-[10px] uppercase tracking-widest text-gray-500 hover:text-white transition mb-6">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a planes
            </a>

            <div class="flex items-center justify-center gap-2 mb-3">
                <div class="h-[1px] w-6 bg-[#d15330]"></div>
                <span class="text-[10px] uppercase tracking-[0.4em] text-[#d15330] font-bold">Checkout</span>
                <div class="h-[1px] w-6 bg-[#d15330]"></div>
            </div>

            <h1 class="text-3xl font-black uppercase tracking-tighter">
                {{ $detalle['nombre'] }}
            </h1>

            {{-- SELECTOR MENSUAL / ANUAL --}}
            <div class="flex items-center justify-center gap-1 mt-4 bg-white/5 border border-white/10 rounded-full p-1 w-fit mx-auto">
                <a href="{{ route('pago.checkout', ['plan' => $plan, 'periodo' => 'mensual']) }}"
                   class="px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all
                       {{ $periodo === 'mensual' ? 'bg-white text-black' : 'text-gray-400 hover:text-white' }}">
                    Mensual
                </a>
                <a href="{{ route('pago.checkout', ['plan' => $plan, 'periodo' => 'anual']) }}"
                   class="px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5
                       {{ $periodo === 'anual' ? 'bg-white text-black' : 'text-gray-400 hover:text-white' }}">
                    Anual
                    <span class="text-[9px] font-black bg-[#d15330] text-white px-1.5 py-0.5 rounded-full">-25%</span>
                </a>
            </div>

            <p class="text-gray-500 text-sm mt-4">
                @if($periodo === 'anual')
                    US$ <span class="text-white font-black text-2xl">{{ $detalle['precio_anual'] }}</span> /mes
                    <span class="text-gray-600 text-xs">(US$ {{ $detalle['total_cobro'] }} al año)</span>
                @else
                    US$ <span class="text-white font-black text-2xl">{{ $detalle['precio'] }}</span> /mes
                @endif
                · Hasta <strong class="text-white">{{ $detalle['proyectos'] }}</strong> proyectos
            </p>
            @if($estaRenovando)
            <div class="mt-3 inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-4 py-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">Renovación — se sumará {{ $periodo === 'anual' ? '12 meses' : '1 mes' }} a tu plan actual</span>
            </div>
            @endif
        </div>

        {{-- CARD --}}
        <div class="bg-[#111111] border border-white/5 rounded-2xl overflow-hidden">

            {{-- RESUMEN --}}
            <div class="p-6 border-b border-white/5">
                <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-4">Resumen del pedido</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-300">{{ $detalle['nombre'] }} — {{ $periodo === 'anual' ? '1 año' : ($estaRenovando ? 'Renovación +1 mes' : '1 mes') }}</span>
                    <span class="text-sm font-black text-white">US$ {{ $detalle['total_cobro'] }}</span>
                </div>
                @if($periodo === 'anual')
                <div class="flex justify-between items-center mt-1">
                    <span class="text-[11px] text-gray-600">US$ {{ $detalle['precio_anual'] }}/mes × 12 meses</span>
                    <span class="text-[11px] text-emerald-400 font-bold">Ahorrás US$ {{ ($detalle['precio'] - $detalle['precio_anual']) * 12 }}</span>
                </div>
                @endif
                <div class="mt-3 pt-3 border-t border-white/5 flex justify-between items-center">
                    <span class="text-[11px] uppercase tracking-wider text-gray-500">Total</span>
                    <span class="text-xl font-black text-[#d15330]">US$ {{ $detalle['total_cobro'] }}</span>
                </div>
            </div>

            {{-- MÉTODOS DE PAGO — PaymentBrick style --}}
            <div class="p-6" x-data="{ metodo: 'mercadopago' }">
                <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-4">Medio de pago</p>

                {{-- LISTA DE MÉTODOS --}}
                <div class="rounded-2xl overflow-hidden border border-white/[0.07]">

                    {{-- OPCIÓN: MERCADO PAGO --}}
                    <label for="mp-radio"
                        class="flex items-center gap-4 px-5 py-4 cursor-pointer transition-colors duration-150"
                        :class="metodo === 'mercadopago' ? 'bg-white/[0.06]' : 'bg-white/[0.02] hover:bg-white/[0.04]'">
                        <input type="radio" id="mp-radio" name="metodo_pago" value="mercadopago"
                            x-model="metodo" class="sr-only">
                        {{-- Radio visual --}}
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-150"
                             :class="metodo === 'mercadopago' ? 'border-[#009ee3]' : 'border-white/20'">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#009ee3] transition-all duration-150"
                                 :class="metodo === 'mercadopago' ? 'opacity-100 scale-100' : 'opacity-0 scale-0'"></div>
                        </div>
                        {{-- Logo MP (ícono oficial manitos) --}}
                        <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center bg-white border border-gray-200">
                            <img src="https://cdn.simpleicons.org/mercadopago" alt="Mercado Pago" class="w-6 h-6">
                        </div>
                        {{-- Texto --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white leading-tight">Mercado Pago</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Tarjeta, débito o efectivo</p>
                        </div>
                    </label>

                    {{-- DIVISOR --}}
                    <div class="h-px bg-white/[0.06] mx-5"></div>

                    {{-- OPCIÓN: PAYPAL --}}
                    <label for="paypal-radio"
                        class="flex items-center gap-4 px-5 py-4 cursor-pointer transition-colors duration-150"
                        :class="metodo === 'paypal' ? 'bg-white/[0.06]' : 'bg-white/[0.02] hover:bg-white/[0.04]'">
                        <input type="radio" id="paypal-radio" name="metodo_pago" value="paypal"
                            x-model="metodo" class="sr-only">
                        {{-- Radio visual --}}
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-150"
                             :class="metodo === 'paypal' ? 'border-[#ffc439]' : 'border-white/20'">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#ffc439] transition-all duration-150"
                                 :class="metodo === 'paypal' ? 'opacity-100 scale-100' : 'opacity-0 scale-0'"></div>
                        </div>
                        {{-- Logo PayPal --}}
                        <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center"
                             style="background:#003087;">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 7.5c.3 2-1 4.5-3.5 5H13l-1 6h-3l2.5-14h5.5c1.5 0 2.8.6 3 3z" fill="#009cde"/>
                                <path d="M17 12c.3 2-1 4-3.5 4.5H11l-.8 5H7.5l2-12h5c1.5 0 2.8.5 2.5 2.5z" fill="white"/>
                            </svg>
                        </div>
                        {{-- Texto --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white leading-tight">PayPal</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Cuenta PayPal o tarjeta (USD)</p>
                        </div>
                    </label>
                </div>

                {{-- BOTÓN PAGAR --}}
                <form id="checkout-form-mp" action="{{ route('pago.mercadopago', $plan) }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="periodo" value="{{ $periodo }}">
                </form>
                <form id="checkout-form-paypal" action="{{ route('pago.paypal', $plan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="periodo" value="{{ $periodo }}">
                </form>

                <button type="submit"
                    @click="metodo === 'mercadopago' ? document.getElementById('checkout-form-mp').submit() : document.getElementById('checkout-form-paypal').submit()"
                    class="w-full flex items-center justify-center gap-2.5 rounded-2xl py-4 mt-2 font-bold text-sm tracking-wide transition-all duration-150 hover:opacity-90 active:scale-[0.98]"
                    :style="metodo === 'mercadopago' ? 'background:#009ee3;color:#fff;' : 'background:#ffc439;color:#003087;'">
                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    PAGAR
                </button>

                @if(session('error'))
                    <div class="flex items-center gap-2 bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 mt-2">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                        </svg>
                        <p class="text-xs text-red-400">{{ session('error') }}</p>
                    </div>
                @endif
            </div>

            {{-- FOOTER SEGURIDAD --}}
            <div class="px-6 pb-6 pt-2">
                <div class="flex items-center justify-center gap-3 text-[9px] text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Pago seguro</span>
                    </div>
                    <span class="text-gray-800">·</span>
                    <span>Sin permanencia</span>
                    <span class="text-gray-800">·</span>
                    <span>Cancelá cuando quieras</span>
                </div>
            </div>

        </div>

        {{-- NOTA SANDBOX --}}
        @if(config('services.paypal.mode') === 'sandbox' || !app()->environment('production'))
        <div class="mt-4 text-center text-[10px] text-gray-600 uppercase tracking-wider">
            Modo sandbox — los pagos son de prueba
        </div>
        @endif

        {{-- ACTIVACIÓN MANUAL (solo en local, después de pagar en MP) --}}
        @if(app()->environment('local', 'development'))
        <div class="mt-4 bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 text-center">
            <p class="text-[10px] text-amber-400 uppercase tracking-wider mb-3">Entorno local — MP no puede redirigir automáticamente</p>
            <a href="{{ route('pago.confirmar_manual', ['plan' => $plan, 'periodo' => $periodo]) }}"
               class="inline-block bg-amber-500 hover:bg-amber-400 text-black text-[10px] font-black uppercase tracking-widest px-5 py-2 rounded-lg transition-all">
                ✓ Ya pagué — Activar plan manualmente
            </a>
        </div>
        @endif

    </div>
</div>

</x-app-layout>
