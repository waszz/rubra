<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        
        .presupuesto-container {
            position: relative;
        }
        
        .presupuesto-content {
            position: relative;
        }

        .company-header {
            width: 100%;
            border-bottom: 3px solid #ff6b35;
            padding-bottom: 14px;
            margin-bottom: 18px;
            display: table;
        }

        /* LEFT cell: logo + doc info stacked */
        .company-left-cell {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .company-logo-wrap {
            display: table;
            width: 100%;
        }

        .company-logo-img-cell {
            display: table-cell;
            width: 110px;
            vertical-align: middle;
            padding-right: 14px;
        }

        .company-logo-img-cell img {
            max-width: 100px;
            max-height: 72px;
        }

        .company-logo-placeholder {
            width: 90px;
            height: 60px;
            background-color: #f0f0f0;
            border: 1px dashed #ccc;
            text-align: center;
            color: #aaa;
            font-size: 9px;
            line-height: 60px;
        }

        .company-info-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 3px 0;
        }

        .company-detail {
            font-size: 9px;
            color: #555;
            margin: 1px 0;
            line-height: 1.45;
        }

        .company-detail span {
            color: #999;
        }

        /* RIGHT cell: document info */
        .doc-title-inline {
            font-size: 15px;
            font-weight: bold;
            color: #ff6b35;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-title-cell {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: right;
            border-left: 2px solid #ff6b35;
            padding-left: 18px;
        }

        .doc-title-cell h1 {
            font-size: 15px;
            font-weight: bold;
            color: #ff6b35;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .doc-info-table td {
            padding: 2px 4px;
            border: none;
            color: #444;
        }

        .doc-info-table td:first-child {
            color: #999;
            width: 110px;
            text-align: left;
        }

        .doc-info-table td:last-child {
            font-weight: bold;
            color: #1a1a1a;
            text-align: right;
        }

        /* RESUMEN DE COSTOS */
        .resumen-section {
            margin-bottom: 22px;
        }

        .resumen-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            background-color: #1a1a1a;
            padding: 5px 10px;
            margin-bottom: 0;
        }

        .resumen-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .resumen-table td {
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        .resumen-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .resumen-table .resumen-label {
            color: #444;
            width: 60%;
        }

        .resumen-table .resumen-value {
            text-align: right;
            font-weight: bold;
            color: #1a1a1a;
        }

        .resumen-table .row-total-obra td {
            background-color: #333;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }

        .resumen-table .row-precio-final td {
            background-color: #ff6b35;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
        }

        .resumen-table .row-carga-social-info td {
            background-color: #f0f0f0;
            color: #888;
            font-style: italic;
            font-size: 9px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #ff6b35;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #1a1a1a;
            font-size: 24px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
        
        .info-section {
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #f5f5f5;
            border-left: 4px solid #ff6b35;
            border-radius: 4px;
        }
        
        .info-section h3 {
            margin: 0 0 5px 0;
            color: #1a1a1a;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .info-section p {
            margin: 5px 0;
            color: #555;
            font-size: 10px;
            line-height: 1.4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        table th {
            background-color: #1a1a1a;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            text-transform: uppercase;
            font-size: 9px;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        table tr:hover {
            background-color: inherit;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .category-row td {
            background-color: #2a2a2a;
            color: #fff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 4px solid #ff6b35;
        }

        .subrubro-row td {
            background-color: #fff4ee;
            font-weight: bold;
            color: #444;
            padding-left: 20px;
            font-style: normal;
            font-size: 9.5px;
        }

        .recurso-row td {
            background-color: #fff;
            color: #444;
            font-size: 9px;
            padding-left: 32px;
        }

        .recurso-row:nth-child(even) td {
            background-color: #fafafa;
        }
        
        .total-row {
            background-color: #1a1a1a;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }
        
        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #ff6b35;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ff6b35;
            text-align: center;
            color: #999;
            font-size: 9px;
        }
        
        .two-columns {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .page-break {
            page-break-after: always;
        }

        /* SECCIONES FINALES (Alcance / Condiciones / Validez) */
        .doc-sections-wrap {
            margin-top: 24px;
        }

        .doc-section {
            margin-bottom: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .doc-section-header {
            background-color: #1a1a1a;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 12px;
            letter-spacing: 1px;
        }

        .doc-section-hint {
            background-color: #f5f5f5;
            color: #888;
            font-size: 8.5px;
            font-style: italic;
            padding: 3px 12px;
            border-bottom: 1px solid #eee;
        }

        .doc-section-body {
            padding: 10px 12px;
            font-size: 10px;
            color: #333;
            min-height: 40px;
            line-height: 1.6;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translateX(-50%) rotate(-45deg);
            font-size: 90px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            color: rgba(180, 180, 180, 0.18);
            white-space: nowrap;
            z-index: 1000;
            pointer-events: none;
            letter-spacing: 8px;
        }

        /* APU breakdown rows */
        .apu-header-row td {
            background-color: #fff4ee;
            font-weight: bold;
            color: #222;
            border-top: 2px solid #ff6b35;
        }

        .apu-badge {
            display: inline-block;
            background-color: #ff6b35;
            color: #fff;
            font-size: 7px;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 2px;
            text-transform: uppercase;
            vertical-align: middle;
            margin-left: 4px;
        }

        .apu-type-row td {
            background-color: #ececec;
            color: #777;
            font-style: italic;
            font-size: 8.5px;
            padding: 3px 8px 3px 28px;
            border: 1px solid #e0e0e0;
        }

        .apu-item-row td {
            background-color: #fafafa;
            color: #555;
            font-size: 9px;
            border: 1px solid #ebebeb;
            padding-left: 28px;
        }

        .apu-item-row td:first-child {
            border-left: 3px solid #ff6b35;
        }

        .apu-carga-social {
            color: #bbb;
            font-style: italic;
            font-size: 8px;
        }
    </style>
</head>
<body>
    @if($userPlan === 'gratis')
    <div class="watermark">RUBRA</div>
    @endif
    {{-- ENCABEZADO: Logo+Info doc (izq) | Datos empresa (der) --}}
    <div class="company-header">

        {{-- IZQUIERDA: logo + info del documento --}}
        <div class="company-left-cell">
            <div class="company-logo-wrap">
                <div class="company-logo-img-cell">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @else
                        <div class="company-logo-placeholder">SIN LOGO</div>
                    @endif
                </div>
                <div class="company-info-cell">
                    <h1 class="doc-title-inline">{{ $titulo }}</h1>
                    <table class="doc-info-table">
                        <tr>
                            <td>Proyecto:</td>
                            <td>{{ $proyecto->nombre_proyecto }}</td>
                        </tr>
                        <tr>
                            <td>Fecha de Emisión:</td>
                            <td>{{ $fechaEmision }}</td>
                        </tr>
                        <tr>
                            <td>Moneda Base:</td>
                            <td>{{ $monedaBase }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- DERECHA: datos empresa --}}
        <div class="doc-title-cell">
            @if($config->nombre_empresa)
                <p class="company-name" style="text-align:right;">{{ $config->nombre_empresa }}</p>
            @endif
            @if($config->rut)
                <p class="company-detail"><span>RUT:</span> {{ $config->rut }}</p>
            @endif
            @if($config->pagina_web)
                <p class="company-detail"><span>Web:</span> {{ $config->pagina_web }}</p>
            @endif
            @if($config->redes_sociales)
                <p class="company-detail"><span>Redes:</span> {{ $config->redes_sociales }}</p>
            @endif
            @if($config->telefonos)
                <p class="company-detail"><span>Tel:</span> {{ $config->telefonos }}</p>
            @endif
            @if($config->correo)
                <p class="company-detail"><span>Email:</span> {{ $config->correo }}</p>
            @endif
        </div>

    </div>

    {{-- RESUMEN DE COSTOS --}}
    <div class="resumen-section">
        <div class="resumen-title">Resumen de Costos</div>
        <table class="resumen-table">
            @if(($resumen['pct_beneficio'] ?? 0) > 0)
            {{-- Fila invisible: solo para detección en importación (texto blanco sobre blanco) --}}
            <tr style="color:#fff;background-color:#fff;line-height:0;height:1px;font-size:1px;">
                <td style="padding:0;border:none;color:#fff;">Beneficio ({{ number_format($resumen['pct_beneficio'], 0) }}%)</td>
                <td style="padding:0;border:none;color:#fff;"></td>
            </tr>
            @endif
            <tr>
                <td class="resumen-label">Subtotal {{ $monedaBase }}</td>
                <td class="resumen-value">$ {{ number_format($resumen['subtotal_con_beneficio'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="resumen-label">Impuestos ({{ number_format($resumen['pct_impuestos'], 0) }}%)</td>
                <td class="resumen-value">$ {{ number_format($resumen['impuestos'], 2, ',', '.') }}</td>
            </tr>
            <tr class="row-total-obra">
                <td class="resumen-label">TOTAL OBRA {{ $monedaBase }}</td>
                <td class="resumen-value">$ {{ number_format($resumen['total_obra'], 2, ',', '.') }}</td>
            </tr>
            <tr class="row-precio-final">
                <td class="resumen-label">PRECIO FINAL</td>
                <td class="resumen-value">$ {{ number_format($resumen['precio_final'], 2, ',', '.') }}</td>
            </tr>
            @if($resumen['carga_social'] > 0)
            <tr class="row-carga-social-info">
                <td class="resumen-label">Carga Social {{ $monedaBase }} (referencial, no incluida)</td>
                <td class="resumen-value">$ {{ number_format($resumen['carga_social'], 2, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- INFORMACIÓN DEL CLIENTE --}}
    @if($emailCliente)
    <div class="info-section">
        <h3>Contacto del Cliente</h3>
        <p>Email: <strong>{{ $emailCliente }}</strong></p>
    </div>
    @endif

    {{-- PRESUPUESTO --}}
    <div class="presupuesto-container">
        <div class="section-title">Tabla de Presupuesto</div>
        <div class="presupuesto-content">
        <table>
        <thead>
            <tr>
                <th style="width: 40%;">Ítem</th>
                @if($opciones['incluirUnidad'])
                    <th style="width: 8%; text-align: center;">Unidad</th>
                @endif
                @if($opciones['incluirCantidad'])
                    <th style="width: 8%; text-align: right;">Cantidad</th>
                @endif
                @if($opciones['incluirCargaSocial'])
                    <th style="width: 14%; text-align: right;">Carga Social</th>
                @endif
                @if($opciones['incluirPrecio'])
                    <th style="width: 15%; text-align: right;">Precio USD</th>
                    <th style="width: 15%; text-align: right;">Subtotal</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
                $categoriaActual = null;
                $totalGeneral    = 0;
                $prevApuTipo     = null;
            @endphp

            @php $factorBeneficio = 1 + ($pctBeneficio / 100); @endphp
            @foreach($datos['items'] as $item)
                @php
                    $precioConBeneficio   = ($item['precio_usd'] ?? 0) * $factorBeneficio;
                    $subtotalConBeneficio = ($item['subtotal'] ?? 0)   * $factorBeneficio;
                    $csItem = $item['carga_social_total'] ?? 0;
                @endphp

                {{-- Fila gris de categoría cuando cambia (no emitir para apu_item) --}}
                @if($item['tipo'] !== 'apu_item' && $item['categoria'] !== '' && $item['categoria'] !== $categoriaActual)
                    @php $categoriaActual = $item['categoria'] @endphp
                    @php
                        $colsExtra = $opciones['incluirUnidad'] + $opciones['incluirCantidad'] + $opciones['incluirCargaSocial'];
                    @endphp
                    <tr class="category-row">
                        @if($opciones['incluirPrecio'])
                            <td colspan="{{ $colsExtra + 2 }}">{{ $item['categoria'] }}</td>
                            <td class="text-right">$ {{ number_format(($datos['cat_subtotales'][$item['categoria']] ?? 0) * $factorBeneficio, 2, ',', '.') }}</td>
                        @else
                            <td colspan="{{ $colsExtra + 1 }}">{{ $item['categoria'] }}</td>
                        @endif
                    </tr>
                @endif

                @if($item['tipo'] === 'subrubro')
                    @php $prevApuTipo = null; @endphp
                    {{-- Subrubro: muestra precio y subtotal sumando todos sus recursos (hijos) --}}
                    @php
                        $cantDisplay       = $item['cantidad_display'] ?? ($item['cantidad'] ?? 0);
                        $precioSubrubro    = ($item['precio_usd'] ?? 0) * $factorBeneficio;
                        $subtotalSubrubro  = $precioSubrubro * $cantDisplay;
                    @endphp
                    <tr class="subrubro-row">
                        <td><strong>{{ $item['nombre'] }}</strong></td>
                        @if($opciones['incluirUnidad'])
                            <td class="text-center">{{ $item['unidad'] ?? '—' }}</td>
                        @endif
                        @if($opciones['incluirCantidad'])
                            <td class="text-right">{{ number_format($cantDisplay, 2, ',', '.') }}</td>
                        @endif
                        @if($opciones['incluirCargaSocial'])
                            <td class="text-right apu-carga-social">{{ $csItem > 0 ? '$ ' . number_format($csItem, 2, ',', '.') : '—' }}</td>
                        @endif
                        @if($opciones['incluirPrecio'])
                            <td class="text-right">$ {{ number_format($precioSubrubro, 2, ',', '.') }}</td>
                            <td class="text-right">$ {{ number_format($subtotalSubrubro, 2, ',', '.') }}</td>
                        @endif
                    </tr>

                @elseif($item['tipo'] === 'apu_header')
                    @php
                        $prevApuTipo = null;
                        $cantDisplay     = $item['cantidad_display'] ?? $item['cantidad'];
                        $subtotalDisplay = ($item['subtotal_display'] ?? $item['subtotal']) * $factorBeneficio;
                    @endphp
                    {{-- APU: se muestra como recurso normal (cantidad por unidad, sin desglose) --}}
                    <tr class="recurso-row">
                        <td>{{ $item['nombre'] }}</td>
                        @if($opciones['incluirUnidad'])
                            <td class="text-center">{{ $item['unidad'] ?? '—' }}</td>
                        @endif
                        @if($opciones['incluirCantidad'])
                            <td class="text-right">{{ number_format($cantDisplay, 4, ',', '.') }}</td>
                        @endif
                        @if($opciones['incluirCargaSocial'])
                            <td class="text-right apu-carga-social">{{ $csItem > 0 ? '$ ' . number_format($csItem, 2, ',', '.') : '—' }}</td>
                        @endif
                        @if($opciones['incluirPrecio'])
                            <td class="text-right">$ {{ number_format($precioConBeneficio, 2, ',', '.') }}</td>
                            <td class="text-right">$ {{ number_format($subtotalDisplay, 2, ',', '.') }}</td>
                        @endif
                    </tr>

                @elseif($item['tipo'] === 'apu_item')
                    {{-- Sub-filas APU: omitidas en el PDF (desglose no incluido) --}}

                @else
                    @php
                        $prevApuTipo = null;
                        $cantDisplay     = $item['cantidad_display'] ?? $item['cantidad'];
                        $subtotalDisplay = ($item['subtotal_display'] ?? $item['subtotal']) * $factorBeneficio;
                    @endphp
                    {{-- Recurso: muestra cantidad por unidad del padre --}}
                    <tr class="recurso-row">
                        <td>{{ $item['nombre'] }}</td>
                        @if($opciones['incluirUnidad'])
                            <td class="text-center">{{ $item['unidad'] ?? '—' }}</td>
                        @endif
                        @if($opciones['incluirCantidad'])
                            <td class="text-right">{{ number_format($cantDisplay, 4, ',', '.') }}</td>
                        @endif
                        @if($opciones['incluirCargaSocial'])
                            <td class="text-right apu-carga-social">{{ $csItem > 0 ? '$ ' . number_format($csItem, 2, ',', '.') : '—' }}</td>
                        @endif
                        @if($opciones['incluirPrecio'])
                            <td class="text-right">$ {{ number_format($precioConBeneficio, 2, ',', '.') }}</td>
                            <td class="text-right">$ {{ number_format($subtotalDisplay, 2, ',', '.') }}</td>
                        @endif
                    </tr>
                @endif
            @endforeach

            @if($opciones['incluirPrecio'])
            <tr class="total-row">
                <td colspan="{{ $opciones['incluirUnidad'] + $opciones['incluirCantidad'] + $opciones['incluirCargaSocial'] + 1 }}" style="text-align: right;">
                    TOTAL PRESUPUESTO:
                </td>
                <td class="text-right">
                    $ {{ number_format($datos['total'] * $factorBeneficio, 2, ',', '.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>
        </div>
    </div>

    {{-- SECCIONES FINALES: Alcance / Condiciones / Validez --}}
    <div class="doc-sections-wrap">

        {{-- ALCANCE --}}
        <div class="doc-section">
            <div class="doc-section-header">Alcance</div>
            <div class="doc-section-hint">Lo que se consideró presupuestar y lo que no</div>
            <div class="doc-section-body">
                @if($alcance)
                    {!! nl2br(e($alcance)) !!}
                @else
                    &nbsp;
                @endif
            </div>
        </div>

        {{-- CONDICIONES --}}
        <div class="doc-section">
            <div class="doc-section-header">Condiciones</div>
            <div class="doc-section-hint">Modo de pago, moneda y condiciones comerciales</div>
            <div class="doc-section-body">
                @if($condiciones)
                    {!! nl2br(e($condiciones)) !!}
                @else
                    &nbsp;
                @endif
            </div>
        </div>

        {{-- VALIDEZ --}}
        <div class="doc-section">
            <div class="doc-section-header">Validez</div>
            <div class="doc-section-hint">Tiempo de vigencia del presupuesto</div>
            <div class="doc-section-body">
                @if($validez)
                    {{ $validez }}
                @else
                    &nbsp;
                @endif
            </div>
        </div>

    </div>

    {{-- PIE DE PÁGINA --}}
    <div class="footer">
        <p>{{ $config->nombre_empresa ?: 'Rubra' }} | {{ $config->pagina_web ?: '' }}</p>
        <p>{{ $fecha }} | Confidencial</p>
    </div>
</body>
</html>
