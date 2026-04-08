<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - {{ $proyecto->nombre_proyecto }}</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: #1a1a1a; line-height: 1.4; }
        .page { page-break-after: always; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #D1D5DB; padding-bottom: 15px; }
        .header h1 { font-size: 24px; color: #111827; margin-bottom: 5px; }
        .header p { color: #9CA3AF; font-size: 12px; }
        .section { margin-bottom: 25px; }
        .section-title { 
            font-size: 14px; 
            font-weight: bold; 
            color: #111827; 
            background-color: #F3F4F6; 
            padding: 10px; 
            margin-bottom: 10px; 
            border-left: 3px solid #374151;
            border-radius: 0;
        }
        .info-grid { display: grid; grid-template-columns: 50% 50%; gap: 10px; margin-bottom: 10px; }
        .info-row { padding: 8px; border-bottom: 1px solid #E5E7EB; }
        .info-label { font-weight: bold; color: #6B7280; font-size: 11px; text-transform: uppercase; }
        .info-value { font-size: 13px; color: #1F2937; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { 
            background-color: #F3F4F6; 
            color: #374151; 
            padding: 8px; 
            text-align: left; 
            font-size: 11px; 
            font-weight: bold;
            border: 1px solid #E5E7EB;
            text-transform: uppercase;
        }
        td { 
            padding: 8px; 
            border: 1px solid #E5E7EB; 
            font-size: 11px; 
        }
        tr:nth-child(even) { background-color: #FAFBFC; }
        .total-row { background-color: #F3F4F6 !important; color: #111827; font-weight: bold; }
        .total-row td { border-color: #D1D5DB; }
        .footer { 
            margin-top: 30px; 
            padding-top: 15px; 
            border-top: 1px solid #E5E7EB; 
            font-size: 10px; 
            color: #9CA3AF; 
            text-align: center;
        }
        .card {
            background-color: #FFFFFF;
            padding: 12px;
            border: 1px solid #E5E7EB;
            border-left: 3px solid #374151;
            margin-bottom: 10px;
            border-radius: 0;
        }
        .card-label { font-size: 10px; color: #9CA3AF; font-weight: bold; text-transform: uppercase; }
        .card-value { font-size: 16px; color: #111827; font-weight: bold; margin-top: 3px; }
        .text-green { color: #6B7280; }
        .text-red { color: #6B7280; }
    </style>
</head>
<body>
    <div class="page">
        {{-- HEADER --}}
        <div class="header">
            <h1>REPORTE DE ESTADÍSTICAS</h1>
            <p>{{ $proyecto->nombre_proyecto }}</p>
            <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        {{-- INFORMACIÓN DEL PROYECTO --}}
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL PROYECTO</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Proyecto</div>
                    <div class="info-value">{{ $proyecto->nombre_proyecto }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado</div>
                    <div class="info-value">{{ ucfirst($proyecto->estado_obra) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Metros Cuadrados</div>
                    <div class="info-value">{{ number_format($proyecto->metros_cuadrados, 2, ',', '.') }} m²</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Beneficio</div>
                    <div class="info-value">{{ $proyecto->beneficio ?? 0 }}%</div>
                </div>
            </div>
        </div>

        {{-- RESUMEN FINANCIERO --}}
        <div class="section">
            <div class="section-title">RESUMEN FINANCIERO</div>
            <div class="card">
                <div class="card-label">Presupuesto Total</div>
                <div class="card-value">USD {{ number_format($stats['presupuesto'], 2, ',', '.') }}</div>
            </div>
            <div class="card">
                <div class="card-label">Costo Real (Subtotal)</div>
                <div class="card-value">USD {{ number_format($stats['costoRealSubtotal'], 2, ',', '.') }}</div>
            </div>
            <div class="card">
                <div class="card-label">IVA Ejecutado ({{ $proyecto->impuestos ?? 22 }}%)</div>
                <div class="card-value">USD {{ number_format($stats['ivaEjecutado'], 2, ',', '.') }}</div>
            </div>
            <div class="card">
                <div class="card-label">Precio Final (Real)</div>
                <div class="card-value">USD {{ number_format($stats['costoReal'], 2, ',', '.') }}</div>
            </div>
            <div class="card">
                <div class="card-label">Desviación
                @php
                    $desv = $stats['desviacion'];
                    $clase = $desv > 0 ? 'text-red' : 'text-green';
                @endphp
                </div>
                <div class="card-value {{ $clase }}">
                    USD {{ number_format($stats['desviacion'], 2, ',', '.') }}
                </div>
            </div>
            <div class="card">
                <div class="card-label">Avance Financiero</div>
                <div class="card-value">{{ number_format($stats['avanceFinanciero'], 1) }}%</div>
            </div>
        </div>

        {{-- DISTRIBUCIÓN DE COSTOS --}}
        @if($stats['distribucion']->count())
        <div class="section">
            <div class="section-title">DISTRIBUCIÓN DE COSTOS</div>
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tiposNombres = [
                            'material' => 'Materiales',
                            'labor' => 'Mano de Obra',
                            'equipment' => 'Equipos',
                            'composition' => 'Composiciones'
                        ];
                    @endphp
                    @foreach($stats['distribucion'] as $dist)
                    <tr>
                        <td>{{ $tiposNombres[$dist->tipo] ?? $dist->tipo }}</td>
                        <td style="text-align: right;">USD {{ number_format($dist->total, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- TOP PARTIDAS --}}
        @if($stats['topPartidas']->count())
        <div class="section">
            <div class="section-title">TOP 5 PARTIDAS CON MAYOR DESVIACIÓN</div>
            <table>
                <thead>
                    <tr>
                        <th>Partida</th>
                        <th style="text-align: right;">Presupuesto</th>
                        <th style="text-align: right;">Costo Real</th>
                        <th style="text-align: right;">Desviación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['topPartidas'] as $partida)
                    <tr>
                        <td>{{ $partida['nombre'] }}</td>
                        <td style="text-align: right;">USD {{ number_format($partida['presupuesto'], 2, ',', '.') }}</td>
                        <td style="text-align: right;">USD {{ number_format($partida['costo_real'], 2, ',', '.') }}</td>
                        <td style="text-align: right;" class="{{ ($partida['desviacion'] ?? 0) > 0 ? 'text-red' : 'text-green' }}">
                            USD {{ number_format($partida['desviacion'] ?? 0, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- MAYORES MATERIALES CONSUMIDOS --}}
        @if($stats['mayoresMateriales']->count())
        <div class="section">
            <div class="section-title">MAYORES MATERIALES CONSUMIDOS (TOP 10)</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%;">Material</th>
                        <th style="text-align: center;">Cantidad</th>
                        <th style="text-align: center;">Unidad</th>
                        <th style="text-align: right;">Precio Unit.</th>
                        <th style="text-align: right;">Costo Real</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalMateriales = $stats['mayoresMateriales']->sum('costoReal'); @endphp
                    @foreach($stats['mayoresMateriales'] as $material)
                    <tr>
                        <td>{{ $material['nombre'] }}</td>
                        <td style="text-align: center;">{{ number_format($material['cantidad'], 2, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $material['unidad'] }}</td>
                        <td style="text-align: right;">USD {{ number_format($material['precioUnitario'], 2, ',', '.') }}</td>
                        <td style="text-align: right;">USD {{ number_format($material['costoReal'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4">TOTAL MATERIALES</td>
                        <td style="text-align: right;">USD {{ number_format($totalMateriales, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <div class="footer">
            <p>Este reporte fue generado automáticamente por RUBRA - Sistema de Gestión de Proyectos</p>
        </div>
    </div>
</body>
</html>
