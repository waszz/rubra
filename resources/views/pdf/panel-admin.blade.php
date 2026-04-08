<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; background: #fff; color: #111; padding: 30px; font-size: 12px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #d15330; padding-bottom: 16px; margin-bottom: 24px; }
        .logo-text { font-size: 22px; font-weight: bold; color: #d15330; letter-spacing: 2px; text-transform: uppercase; }
        .subtitle { font-size: 10px; color: #888; margin-top: 2px; text-transform: uppercase; letter-spacing: 1px; }
        .fecha-gen { font-size: 10px; color: #888; text-align: right; }

        h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #888; font-weight: bold; margin: 20px 0 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; }

        /* Cards métricas */
        .kpis { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 8px; }
        .kpi { display: table-cell; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 6px; padding: 12px; text-align: center; }
        .kpi-val { font-size: 22px; font-weight: bold; color: #d15330; }
        .kpi-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

        /* Tabla planes */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #111; color: #fff; text-transform: uppercase; font-size: 9px; letter-spacing: 1px; padding: 8px 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        tr:nth-child(even) td { background: #fafafa; }
        .total-row td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #111; }
        .ingreso-total td { font-size: 13px; font-weight: bold; color: #d15330; background: #fff5f2; border-top: 2px solid #d15330; }

        /* Historial */
        .hist-table th { background: #1a1a1a; }
        .mes-actual td { background: #e6f9f0; font-weight: bold; color: #009955; }
        .variacion-up { color: #009955; font-weight: bold; }
        .variacion-down { color: #cc0000; font-weight: bold; }
        .variacion-eq { color: #aaa; }

        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 9px; color: #bbb; text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="logo-text">Rubra</div>
            <div class="subtitle">Panel Administrativo — Reporte de Gestión</div>
        </div>
        <div class="fecha-gen">
            Generado el {{ now()->format('d/m/Y H:i') }}<br>
            Período: últimos 12 meses
        </div>
    </div>

    {{-- KPIs --}}
    <h2>Métricas Generales</h2>
    <div class="kpis">
        <div class="kpi">
            <div class="kpi-val">{{ $stats['usuariosEnTrial'] }}</div>
            <div class="kpi-label">En Trial</div>
        </div>
        <div class="kpi">
            <div class="kpi-val">{{ $stats['usuariosNuevos'] }}</div>
            <div class="kpi-label">Nuevos (7d)</div>
        </div>
        <div class="kpi">
            <div class="kpi-val">{{ $stats['bajas'] }}</div>
            <div class="kpi-label">Bajas</div>
        </div>
        <div class="kpi">
            <div class="kpi-val">{{ $stats['totalActivos'] }}</div>
            <div class="kpi-label">Total Activos</div>
        </div>
        <div class="kpi">
            @php
                $conv = $stats['totalActivos'] > 0
                    ? round((($stats['porPlan']['basico'] + $stats['porPlan']['profesional'] + $stats['porPlan']['enterprise']) / $stats['totalActivos']) * 100, 1)
                    : 0;
            @endphp
            <div class="kpi-val">{{ $conv }}%</div>
            <div class="kpi-label">Conversión</div>
        </div>
    </div>

    {{-- Usuarios y ingresos por plan --}}
    <h2>Usuarios e Ingresos por Plan</h2>
    <table>
        <thead>
            <tr>
                <th>Plan</th>
                <th>Usuarios</th>
                <th>Precio/mes</th>
                <th>Subtotal Mensual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gratis (Trial)</td>
                <td>{{ $stats['porPlan']['gratis'] }}</td>
                <td>$0</td>
                <td>$0</td>
            </tr>
            <tr>
                <td>Básico</td>
                <td>{{ $stats['porPlan']['basico'] }}</td>
                <td>${{ $stats['precios']['basico'] }}</td>
                <td>${{ number_format($stats['ingresos']['basico'], 0) }}</td>
            </tr>
            <tr>
                <td>Profesional</td>
                <td>{{ $stats['porPlan']['profesional'] }}</td>
                <td>${{ $stats['precios']['profesional'] }}</td>
                <td>${{ number_format($stats['ingresos']['profesional'], 0) }}</td>
            </tr>
            <tr>
                <td>Enterprise</td>
                <td>{{ $stats['porPlan']['enterprise'] }}</td>
                <td>${{ $stats['precios']['enterprise'] }}</td>
                <td>${{ number_format($stats['ingresos']['enterprise'], 0) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="ingreso-total">
                <td colspan="3">INGRESOS MENSUALES TOTALES</td>
                <td>${{ number_format($stats['ingresoTotal'], 0) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Historial mensual --}}
    <h2>Historial Mensual — Últimos 12 Meses</h2>
    <table class="hist-table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Suscriptores</th>
                <th>Básico</th>
                <th>Profesional</th>
                <th>Enterprise</th>
                <th>Ingresos</th>
                <th>Variación</th>
            </tr>
        </thead>
        <tbody>
            @php
                $precios = ['basico' => 12, 'profesional' => 24, 'enterprise' => 59];
                $ingresoAnterior = null;
                $total = count($historial['labels']);
            @endphp
            @foreach($historial['labels'] as $i => $mes)
                @php
                    $subs = $historial['suscriptores'][$i];
                    $ing  = $historial['ingresos'][$i];
                    $esCurrent = $i === $total - 1;

                    $variacion = '';
                    $varClass = 'variacion-eq';
                    if ($ingresoAnterior !== null) {
                        $diff = $ing - $ingresoAnterior;
                        if ($diff > 0)     { $variacion = '+$' . number_format($diff, 0); $varClass = 'variacion-up'; }
                        elseif ($diff < 0) { $variacion = '-$' . number_format(abs($diff), 0); $varClass = 'variacion-down'; }
                        else               { $variacion = '—'; }
                    }
                    $ingresoAnterior = $ing;
                @endphp
                <tr class="{{ $esCurrent ? 'mes-actual' : '' }}">
                    <td>{{ $mes }}{{ $esCurrent ? ' ★' : '' }}</td>
                    <td>{{ $subs }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>${{ number_format($ing, 0) }}</td>
                    <td class="{{ $varClass }}">{{ $variacion }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Gráfico de Suscriptores --}}
    @php
        $maxSubs = max(array_merge([1], $historial['suscriptores']));
        $maxIng  = max(array_merge([1], $historial['ingresos']));
        $chartH  = 80;
    @endphp

    <h2>Evolución de Suscriptores (últimos 12 meses)</h2>
    <div style="border: 1px solid #e5e5e5; border-radius: 6px; padding: 16px; margin-bottom: 16px; background: #f9f9f9;">
        <table style="width:100%; border-collapse:collapse; height:{{ $chartH + 30 }}px;">
            <tr>
                @foreach($historial['suscriptores'] as $i => $val)
                @php $h = $maxSubs > 0 ? max(2, round(($val / $maxSubs) * $chartH)) : 2; @endphp
                <td style="vertical-align:bottom; text-align:center; padding:0 2px; width:{{ 100/count($historial['suscriptores']) }}%;">
                    <div style="font-size:7px; color:#555; margin-bottom:2px;">{{ $val }}</div>
                    <div style="background:#3b82f6; height:{{ $h }}px; border-radius:2px 2px 0 0;"></div>
                </td>
                @endforeach
            </tr>
            <tr>
                @foreach($historial['labels'] as $mes)
                <td style="text-align:center; font-size:7px; color:#888; padding-top:4px; border-top:1px solid #ddd;">
                    {{ \Carbon\Carbon::createFromFormat('M Y', $mes)->format('M') }}
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    {{-- Gráfico de Ingresos --}}
    <h2>Evolución de Ingresos Mensuales (USD)</h2>
    <div style="border: 1px solid #e5e5e5; border-radius: 6px; padding: 16px; margin-bottom: 16px; background: #f9f9f9;">
        <table style="width:100%; border-collapse:collapse; height:{{ $chartH + 30 }}px;">
            <tr>
                @foreach($historial['ingresos'] as $i => $val)
                @php $h = $maxIng > 0 ? max(2, round(($val / $maxIng) * $chartH)) : 2; @endphp
                <td style="vertical-align:bottom; text-align:center; padding:0 2px; width:{{ 100/count($historial['ingresos']) }}%;">
                    <div style="font-size:7px; color:#555; margin-bottom:2px;">${{ $val }}</div>
                    <div style="background:#10b981; height:{{ $h }}px; border-radius:2px 2px 0 0;"></div>
                </td>
                @endforeach
            </tr>
            <tr>
                @foreach($historial['labels'] as $mes)
                <td style="text-align:center; font-size:7px; color:#888; padding-top:4px; border-top:1px solid #ddd;">
                    {{ \Carbon\Carbon::createFromFormat('M Y', $mes)->format('M') }}
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="footer">
        Rubra — Panel Administrativo — Reporte confidencial — {{ now()->format('d/m/Y') }}
    </div>

</body>
</html>
