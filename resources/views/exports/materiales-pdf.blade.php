<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Materiales - {{ $proyecto->nombre_proyecto }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1a1a1a; line-height: 1.4; font-size: 12px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #D1D5DB; padding-bottom: 14px; }
        .header h1 { font-size: 20px; color: #111827; margin-bottom: 4px; }
        .header h2 { font-size: 14px; color: #374151; font-weight: normal; margin-bottom: 6px; }
        .header p { color: #9CA3AF; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th {
            background-color: #F3F4F6;
            color: #374151;
            padding: 7px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #E5E7EB;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        th.right { text-align: right; }
        th.center { text-align: center; }
        td {
            padding: 6px 10px;
            border: 1px solid #E5E7EB;
            font-size: 10px;
            vertical-align: middle;
        }
        td.right { text-align: right; }
        td.center { text-align: center; }
        td.num { font-family: 'Courier New', monospace; }
        tr:nth-child(even) { background-color: #FAFBFC; }
        .total-row td {
            background-color: #F3F4F6 !important;
            font-weight: bold;
            border-top: 2px solid #D1D5DB;
            color: #111827;
        }
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #E5E7EB;
            font-size: 9px;
            color: #9CA3AF;
            text-align: center;
        }
        .badge {
            display: inline-block;
            background-color: #FFF7ED;
            color: #EA580C;
            border: 1px solid #FED7AA;
            padding: 1px 6px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $proyecto->nombre_proyecto }}</h1>
        <h2>Listado de Materiales</h2>
        <p>Generado el {{ now()->format('d/m/Y H:i') }} &mdash; {{ $materiales->count() }} ítems</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th>Material</th>
                <th class="center" style="width:10%">Cantidad</th>
                <th class="center" style="width:8%">Unidad</th>
                <th class="right" style="width:14%">P. Unitario</th>
                <th class="right" style="width:14%">Total</th>
                <th class="right" style="width:7%">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materiales as $index => $mat)
                @php $pct = $total > 0 ? ($mat['costoReal'] / $total) * 100 : 0; @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ Str::ucfirst($mat['nombre']) }}</td>
                    <td class="center num">{{ number_format($mat['cantidad'], 2) }}</td>
                    <td class="center">{{ $mat['unidad'] ?? '' }}</td>
                    <td class="right num">USD {{ number_format($mat['precioUnitario'], 2, ',', '.') }}</td>
                    <td class="right num">USD {{ number_format($mat['costoReal'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($pct, 1) }}%</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="right">TOTAL MATERIALES</td>
                <td class="right num">USD {{ number_format($total, 0, ',', '.') }}</td>
                <td class="right">100%</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name') }} &mdash; {{ $proyecto->nombre_proyecto }} &mdash; Reporte de Materiales
    </div>
</body>
</html>
