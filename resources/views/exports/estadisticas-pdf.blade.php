<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>EstadÃ­sticas - {{ $proyecto->nombre_proyecto }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1a1a1a; font-size: 11px; line-height: 1.45; }

        /* â”€â”€ Layout â”€â”€ */
        .page { padding: 22px 26px; }

        /* â”€â”€ Header â”€â”€ */
        .header { text-align: center; border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #111827; letter-spacing: 0.04em; }
        .header h2 { font-size: 13px; font-weight: normal; color: #374151; margin-top: 3px; }
        .header p  { font-size: 9px; color: #9CA3AF; margin-top: 4px; }

        /* â”€â”€ Section â”€â”€ */
        .section { margin-bottom: 18px; }
        .section-title {
            font-size: 10px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.08em; color: #fff;
            background-color: #1f2937; padding: 5px 9px; margin-bottom: 8px;
        }

        /* â”€â”€ Cards row (table-based for DomPDF) â”€â”€ */
        .cards-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .card-cell {
            width: 20%; padding: 8px 10px; vertical-align: top;
            border: 1px solid #E5E7EB; border-left: 3px solid #374151;
            background: #F9FAFB;
        }
        .card-cell.accent-blue  { border-left-color: #3b82f6; }
        .card-cell.accent-green { border-left-color: #22c55e; }
        .card-cell.accent-red   { border-left-color: #ef4444; }
        .card-cell.accent-ora   { border-left-color: #f97316; }
        .card-cell.accent-vio   { border-left-color: #a855f7; }
        .card-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #9CA3AF; letter-spacing: 0.06em; }
        .card-value { font-size: 13px; font-weight: bold; color: #111827; margin-top: 3px; }
        .card-sub   { font-size: 8px; color: #9CA3AF; margin-top: 2px; }

        /* â”€â”€ Tables â”€â”€ */
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.data th {
            background: #F3F4F6; color: #374151; padding: 6px 8px;
            font-size: 9px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.05em; border: 1px solid #E5E7EB; text-align: left;
        }
        table.data th.r { text-align: right; }
        table.data th.c { text-align: center; }
        table.data td {
            padding: 5px 8px; border: 1px solid #E5E7EB;
            font-size: 10px; vertical-align: middle;
        }
        table.data td.r  { text-align: right; }
        table.data td.c  { text-align: center; }
        table.data td.num { font-family: 'Courier New', monospace; }
        table.data tr:nth-child(even) { background: #FAFBFC; }
        .tr-total td { background: #F3F4F6 !important; font-weight: bold; border-top: 2px solid #D1D5DB; }

        /* â”€â”€ Distribution bar â”€â”€ */
        .bar-bg  { background: #E5E7EB; border-radius: 3px; height: 7px; }
        .bar-fill { height: 7px; border-radius: 3px; }

        /* â”€â”€ Deviation colors â”€â”€ */
        .pos { color: #dc2626; }
        .neg { color: #16a34a; }

        /* â”€â”€ Footer â”€â”€ */
        .footer { margin-top: 22px; padding-top: 10px; border-top: 1px solid #E5E7EB; font-size: 9px; color: #9CA3AF; text-align: center; }
    </style>
</head>
<body>
<div class="page">

    {{-- â•â• HEADER â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="header">
        <h1>REPORTE DE ESTADÃSTICAS</h1>
        <h2>{{ $proyecto->nombre_proyecto }}</h2>
        <p>
            Generado: {{ now()->format('d/m/Y H:i') }}
            @if($proyecto->metros_cuadrados) &nbsp;|&nbsp; {{ number_format($proyecto->metros_cuadrados, 2, ',', '.') }} mÂ² @endif
            &nbsp;|&nbsp; Estado: {{ ucfirst($proyecto->estado_obra ?? 'â€”') }}
            &nbsp;|&nbsp; Beneficio: {{ $proyecto->beneficio ?? 0 }}%
            &nbsp;|&nbsp; IVA: {{ $proyecto->impuestos ?? 22 }}%
        </p>
    </div>

    {{-- â•â• RESUMEN FINANCIERO â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="section">
        <div class="section-title">Resumen Financiero</div>
        <table class="cards-table">
            <tr>
                <td class="card-cell accent-blue">
                    <div class="card-label">Presupuesto Total</div>
                    <div class="card-value">USD {{ number_format($stats['presupuesto'], 0, ',', '.') }}</div>
                    <div class="card-sub">Precio final con IVA</div>
                </td>
                <td class="card-cell">
                    <div class="card-label">Subtotal Base</div>
                    <div class="card-value">USD {{ number_format($stats['subtotal'], 0, ',', '.') }}</div>
                    <div class="card-sub">Sin beneficio ni IVA</div>
                </td>
                <td class="card-cell">
                    <div class="card-label">Beneficio ({{ $proyecto->beneficio ?? 0 }}%)</div>
                    <div class="card-value">USD {{ number_format($stats['beneficio'], 0, ',', '.') }}</div>
                </td>
                <td class="card-cell accent-ora">
                    <div class="card-label">Costo Real Ejecutado</div>
                    <div class="card-value">USD {{ number_format($stats['costoReal'], 0, ',', '.') }}</div>
                    <div class="card-sub">Incl. IVA {{ $proyecto->impuestos ?? 22 }}%</div>
                </td>
                <td class="card-cell {{ $stats['desviacion'] > 0 ? 'accent-red' : 'accent-green' }}">
                    <div class="card-label">DesviaciÃ³n</div>
                    <div class="card-value {{ $stats['desviacion'] > 0 ? 'pos' : 'neg' }}">
                        {{ $stats['desviacion'] > 0 ? '+' : '' }}USD {{ number_format($stats['desviacion'], 0, ',', '.') }}
                    </div>
                    <div class="card-sub">Avance: {{ number_format($stats['avanceFinanciero'], 1) }}%</div>
                </td>
            </tr>
        </table>
        @if($proyecto->metros_cuadrados && $stats['subtotal'] > 0)
        @php $costoM2 = $stats['subtotal'] / $proyecto->metros_cuadrados; @endphp
        <table class="cards-table">
            <tr>
                <td class="card-cell" style="width:25%">
                    <div class="card-label">Costo / mÂ²</div>
                    <div class="card-value">USD {{ number_format($costoM2, 0, ',', '.') }}</div>
                    <div class="card-sub">{{ number_format($proyecto->metros_cuadrados, 0) }} mÂ² totales</div>
                </td>
                <td class="card-cell" style="width:25%">
                    <div class="card-label">Costo Real (sin IVA)</div>
                    <div class="card-value">USD {{ number_format($stats['costoRealSubtotal'], 0, ',', '.') }}</div>
                </td>
                <td class="card-cell" style="width:25%">
                    <div class="card-label">IVA Ejecutado ({{ $proyecto->impuestos ?? 22 }}%)</div>
                    <div class="card-value">USD {{ number_format($stats['ivaEjecutado'], 0, ',', '.') }}</div>
                </td>
                <td style="width:25%"></td>
            </tr>
        </table>
        @endif
    </div>

    {{-- â•â• DISTRIBUCIÃ“N DE COSTOS â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    @if($stats['distribucion']->count())
    @php
        $tiposNombres = [
            'material'      => 'Materiales',
            'labor'         => 'Mano de Obra',
            'equipment'     => 'Equipos',
            'composition'   => 'Composiciones',
            'sin_clasificar'=> 'Sin Clasificar',
        ];
        $tiposColores = [
            'material'      => '#3b82f6',
            'labor'         => '#22c55e',
            'equipment'     => '#f97316',
            'composition'   => '#a855f7',
            'sin_clasificar'=> '#6b7280',
        ];
        $totalDist = $stats['distribucion']->sum('total');
    @endphp
    <div class="section">
        <div class="section-title">DistribuciÃ³n de Costos</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:30%">Tipo de Recurso</th>
                    <th class="r" style="width:22%">Total (USD)</th>
                    <th class="r" style="width:10%">%</th>
                    <th style="width:38%">Barra</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['distribucion']->sortByDesc('total') as $dist)
                @php
                    $pctDist = $totalDist > 0 ? ($dist->total / $totalDist) * 100 : 0;
                    $color   = $tiposColores[$dist->tipo] ?? '#6b7280';
                @endphp
                <tr>
                    <td>{{ $tiposNombres[$dist->tipo] ?? $dist->tipo }}</td>
                    <td class="r num">{{ number_format($dist->total, 0, ',', '.') }}</td>
                    <td class="r"><b>{{ number_format($pctDist, 1) }}%</b></td>
                    <td>
                        <div class="bar-bg">
                            <div class="bar-fill" style="width:{{ min($pctDist,100) }}%; background:{{ $color }};"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="tr-total">
                    <td>TOTAL</td>
                    <td class="r num">{{ number_format($totalDist, 0, ',', '.') }}</td>
                    <td class="r">100%</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- ══ PRESUPUESTO POR RUBRO ═════════════════════════════════════════════════ --}}
    @if(isset($stats['rubros']) && $stats['rubros']->count())
    @php $totalRubros = $stats['rubros']->sum('presupuesto'); @endphp
    <div class="section">
        <div class="section-title">Presupuesto por Rubro</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th>Rubro</th>
                    <th class="r" style="width:20%">Total (USD)</th>
                    <th class="r" style="width:8%">%</th>
                    <th style="width:30%">Participación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['rubros'] as $idx => $rubro)
                <tr>
                    <td class="c">{{ $idx + 1 }}</td>
                    <td>{{ $rubro['nombre'] }}</td>
                    <td class="r num"><b>{{ number_format($rubro['presupuesto'], 0, ',', '.') }}</b></td>
                    <td class="r"><b>{{ $rubro['pct'] }}%</b></td>
                    <td>
                        <div class="bar-bg">
                            <div class="bar-fill" style="width:{{ min($rubro['pct'],100) }}%; background:#3b82f6;"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="tr-total">
                    <td colspan="2">TOTAL</td>
                    <td class="r num">{{ number_format($totalRubros, 0, ',', '.') }}</td>
                    <td class="r">100%</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- ══ TOP 5 PARTIDAS CON MAYOR DESVIACIÓN ═══════════════════════════════════ --}}
    @if($stats['topPartidas']->count())
    <div class="section">
        <div class="section-title">Top 5 Rubros con Mayor DesviaciÃ³n</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:34%">Rubro</th>
                    <th class="r">Presupuesto</th>
                    <th class="r">Costo Real</th>
                    <th class="r">DesviaciÃ³n</th>
                    <th class="r" style="width:10%">Var %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['topPartidas'] as $partida)
                @php $varPct = $partida['presupuesto'] > 0 ? (($partida['desviacion'] ?? 0) / $partida['presupuesto']) * 100 : 0; @endphp
                <tr>
                    <td>{{ $partida['nombre'] }}</td>
                    <td class="r num">{{ number_format($partida['presupuesto'], 0, ',', '.') }}</td>
                    <td class="r num">{{ number_format($partida['costo_real'], 0, ',', '.') }}</td>
                    <td class="r num {{ ($partida['desviacion'] ?? 0) > 0 ? 'pos' : 'neg' }}">
                        {{ ($partida['desviacion'] ?? 0) > 0 ? '+' : '' }}{{ number_format($partida['desviacion'] ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="r {{ $varPct > 0 ? 'pos' : 'neg' }}">{{ number_format($varPct, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- â•â• MANO DE OBRA â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    @if(isset($stats['manoDeObra']) && $stats['manoDeObra']->count())
    @php
        $totalMO   = $stats['manoDeObra']->sum('totalCosto');
        $totalCS   = $stats['manoDeObra']->sum('cargaSocial');
        $totalMOCS = $stats['manoDeObra']->sum('totalConCS');
    @endphp
    <div class="section">
        <div class="section-title">Mano de Obra por Cargo / Especialidad{{ isset($stats['pctCS']) && $stats['pctCS'] > 0 ? ' (CS: '.$stats['pctCS'].'%)' : '' }}</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:38%">Cargo / Especialidad</th>
                    <th class="r">Costo Base</th>
                    <th class="r">Carga Social</th>
                    <th class="r">Total c/CS</th>
                    <th class="r" style="width:9%">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['manoDeObra'] as $mo)
                @php $pctMO = $totalMOCS > 0 ? ($mo['totalConCS'] / $totalMOCS) * 100 : 0; @endphp
                <tr>
                    <td>{{ Str::ucfirst($mo['nombre']) }}</td>
                    <td class="r num">{{ number_format($mo['totalCosto'], 0, ',', '.') }}</td>
                    <td class="r num">{{ number_format($mo['cargaSocial'], 0, ',', '.') }}</td>
                    <td class="r num"><b>{{ number_format($mo['totalConCS'], 0, ',', '.') }}</b></td>
                    <td class="r">{{ number_format($pctMO, 1) }}%</td>
                </tr>
                @endforeach
                <tr class="tr-total">
                    <td>TOTAL MANO DE OBRA</td>
                    <td class="r num">{{ number_format($totalMO, 0, ',', '.') }}</td>
                    <td class="r num">{{ number_format($totalCS, 0, ',', '.') }}</td>
                    <td class="r num">{{ number_format($totalMOCS, 0, ',', '.') }}</td>
                    <td class="r">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- â•â• MAYORES MATERIALES (TOP 10) â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    @if($stats['mayoresMateriales']->count())
    @php $totalMat10 = $stats['mayoresMateriales']->sum('costoReal'); @endphp
    <div class="section">
        <div class="section-title">Mayores Materiales Consumidos (Top 10)</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:3%">#</th>
                    <th style="width:35%">Material</th>
                    <th class="c" style="width:11%">Cantidad</th>
                    <th class="c" style="width:7%">Ud.</th>
                    <th class="r" style="width:15%">P. Unitario</th>
                    <th class="r" style="width:15%">Total</th>
                    <th class="r" style="width:9%">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['mayoresMateriales'] as $idx => $mat)
                @php
                    $allTotal = isset($stats['todosLosMateriales']) ? $stats['todosLosMateriales']->sum('costoReal') : $totalMat10;
                    $pctMat = $allTotal > 0 ? ($mat['costoReal'] / $allTotal) * 100 : 0;
                @endphp
                <tr>
                    <td class="c">{{ $idx + 1 }}</td>
                    <td>{{ Str::ucfirst($mat['nombre']) }}</td>
                    <td class="c num">{{ number_format($mat['cantidad'], 2, ',', '.') }}</td>
                    <td class="c">{{ $mat['unidad'] ?? '' }}</td>
                    <td class="r num">{{ number_format($mat['precioUnitario'], 2, ',', '.') }}</td>
                    <td class="r num"><b>{{ number_format($mat['costoReal'], 0, ',', '.') }}</b></td>
                    <td class="r">{{ number_format($pctMat, 1) }}%</td>
                </tr>
                @endforeach
                <tr class="tr-total">
                    <td colspan="5">TOTAL TOP 10</td>
                    <td class="r num">{{ number_format($totalMat10, 0, ',', '.') }}</td>
                    <td class="r">
                        @php $allTotal2 = isset($stats['todosLosMateriales']) ? $stats['todosLosMateriales']->sum('costoReal') : $totalMat10; @endphp
                        {{ $allTotal2 > 0 ? number_format(($totalMat10/$allTotal2)*100, 1).'%' : '100%' }}
                    </td>
                </tr>
            </tbody>
        </table>
        @if(isset($stats['todosLosMateriales']) && $stats['todosLosMateriales']->count() > 10)
        <p style="font-size:9px; color:#9CA3AF; margin-top:4px;">
            * Mostrando los 10 materiales de mayor costo. Total de materiales en el proyecto: {{ $stats['todosLosMateriales']->count() }}.
            Descargue el reporte de materiales completo para ver el listado Ã­ntegro.
        </p>
        @endif
    </div>
    @endif

    <div class="footer">
        {{ config('app.name') }} â€” {{ $proyecto->nombre_proyecto }} â€” Reporte de EstadÃ­sticas â€” {{ now()->format('d/m/Y') }}
    </div>

</div>
</body>
</html>
