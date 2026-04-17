<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gantt - {{ $proyecto->nombre_proyecto }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1a1a1a; font-size: 10px; line-height: 1.4; }
        .page { padding: 18px 22px; }

        .header { text-align: center; border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; color: #111827; letter-spacing: 0.04em; }
        .header h2 { font-size: 12px; font-weight: normal; color: #374151; margin-top: 3px; }
        .header p  { font-size: 8px; color: #9CA3AF; margin-top: 4px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr th {
            background: #1F2937;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 5px 6px;
            border: 1px solid #374151;
            text-align: center;
        }
        thead tr th.left { text-align: left; }

        tbody tr td {
            padding: 4px 6px;
            border: 1px solid #E5E7EB;
            font-size: 9px;
            vertical-align: middle;
        }
        tbody tr td.c { text-align: center; }
        tbody tr td.r { text-align: right; }

        tr.categoria td {
            background: #F3F4F6;
            font-weight: bold;
            font-size: 9px;
            color: #111827;
            border-top: 2px solid #9CA3AF;
        }
        tr.subtarea td { background: #fff; }
        tr.subtarea:nth-child(even) td { background: #FAFBFC; }

        tr.subtarea td.nombre { padding-left: 18px; }

        .badge-dep {
            font-size: 8px;
            color: #6B7280;
            font-style: italic;
        }

        .footer { margin-top: 18px; padding-top: 8px; border-top: 1px solid #E5E7EB; font-size: 8px; color: #9CA3AF; text-align: center; }

        .legend { margin-bottom: 12px; font-size: 8px; color: #6B7280; }
        .legend span { display: inline-block; width: 12px; height: 12px; vertical-align: middle; margin-right: 3px; border-radius: 2px; }
        .legend .cat-color { background: #F3F4F6; border: 1px solid #9CA3AF; }
        .legend .sub-color { background: #fff; border: 1px solid #E5E7EB; }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <h1>CRONOGRAMA GANTT</h1>
        <h2>{{ $proyecto->nombre_proyecto }}</h2>
        <p>
            Generado: {{ now()->format('d/m/Y H:i') }}
            @if($proyecto->fecha_inicio) &nbsp;|&nbsp; Inicio del proyecto: {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }} @endif
            &nbsp;|&nbsp; Estado: {{ ucfirst($proyecto->estado_obra ?? '—') }}
        </p>
    </div>

    {{-- LEYENDA --}}
    <div class="legend">
        <span class="cat-color"></span> Rubro / Categoría &nbsp;&nbsp;
        <span class="sub-color"></span> Sub-tarea
    </div>

    {{-- TABLA DE TAREAS --}}
    @php
        $numCat  = 0;
        $numItem = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th style="width:5%">Nº</th>
                <th class="left" style="width:30%">Tarea / Sub-tarea</th>
                <th style="width:11%">Inicio</th>
                <th style="width:11%">Fin</th>
                <th style="width:9%">Días</th>
                <th style="width:8%">Hs. MO</th>
                <th style="width:8%">Trab.</th>
                <th class="left" style="width:18%">Depende de</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rubros as $fila)
                @php
                    if ($fila['es_categoria']) {
                        $numCat++;
                        $numItem = 0;
                        $label = $numCat . '.';
                    } else {
                        $numItem++;
                        $label = $numCat . '.' . $numItem;
                    }
                    $inicio   = $fila['fecha_inicio'] ?? null;
                    $fin      = $fila['fecha_fin'] ?? null;
                    $duracion = ($inicio && $fin)
                        ? \Carbon\Carbon::parse($inicio)->diffInDays(\Carbon\Carbon::parse($fin)) + 1
                        : null;
                @endphp
                <tr class="{{ $fila['es_categoria'] ? 'categoria' : 'subtarea' }}">
                    <td class="c">{{ $label }}</td>
                    <td class="{{ $fila['es_categoria'] ? '' : 'nombre' }}">{{ $fila['nombre'] ?? '' }}</td>
                    <td class="c">{{ $inicio ? \Carbon\Carbon::parse($inicio)->format('d/m/Y') : '—' }}</td>
                    <td class="c">{{ $fin    ? \Carbon\Carbon::parse($fin)->format('d/m/Y')    : '—' }}</td>
                    <td class="c">{{ $duracion ?? '—' }}</td>
                    <td class="c">{{ ($fila['horas_totales'] ?? 0) > 0 ? number_format($fila['horas_totales'], 1) : '—' }}</td>
                    <td class="c">{{ !$fila['es_categoria'] ? ($fila['trabajadores'] ?? 1) : '—' }}</td>
                    <td>
                        @if($fila['depends_on_nombre'])
                            <span class="badge-dep"> {{ $fila['depends_on_nombre'] }}</span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- RESUMEN --}}
    @php
        $totalTareas     = collect($rubros)->where('es_categoria', false)->count();
        $tareasConFecha  = collect($rubros)->where('es_categoria', false)->filter(fn($r) => $r['fecha_inicio'] && $r['fecha_fin'])->count();
        $totalHoras      = collect($rubros)->where('es_categoria', false)->sum('horas_totales');
        $primeraFecha    = collect($rubros)->whereNotNull('fecha_inicio')->min('fecha_inicio');
        $ultimaFecha     = collect($rubros)->whereNotNull('fecha_fin')->max('fecha_fin');
        $duracionTotal   = ($primeraFecha && $ultimaFecha)
            ? \Carbon\Carbon::parse($primeraFecha)->diffInDays(\Carbon\Carbon::parse($ultimaFecha)) + 1
            : null;
    @endphp
    <div style="margin-top: 14px; font-size: 8px; color: #6B7280; border-top: 1px solid #E5E7EB; padding-top: 8px;">
        <strong>Resumen:</strong>
        Total tareas: {{ $totalTareas }}
        &nbsp;|&nbsp; Con fechas asignadas: {{ $tareasConFecha }}
        &nbsp;|&nbsp; Total hs. M.O.: {{ $totalHoras > 0 ? number_format($totalHoras, 1) : '—' }}
        @if($duracionTotal)
            &nbsp;|&nbsp; Duración total del proyecto: {{ $duracionTotal }} días
            ({{ $primeraFecha ? \Carbon\Carbon::parse($primeraFecha)->format('d/m/Y') : '' }}
             → {{ $ultimaFecha ? \Carbon\Carbon::parse($ultimaFecha)->format('d/m/Y') : '' }})
        @endif
    </div>

    <div class="footer">
        {{ config('app.name') }} — Exportado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>
</div>
</body>
</html>
