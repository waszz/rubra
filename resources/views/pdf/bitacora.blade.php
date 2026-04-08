<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #0a0a0a; color: #fff; margin: 0; padding: 40px; }
        .header { border-bottom: 2px solid #1a1a1a; padding-bottom: 20px; margin-bottom: 30px; }
        .proyecto-nombre { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #00ff88; }
        .registro-card { background-color: #111; border: 1px solid #222; border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        .fecha { color: #666; font-size: 10px; float: right; }
        .rubro-nombre { color: #4dabf7; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .notas { font-style: italic; color: #ccc; margin-top: 10px; font-size: 13px; }
        .meta { margin-top: 15px; font-size: 11px; color: #888; }
        .badge { background: #1a1a1a; padding: 4px 10px; border-radius: 10px; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <span class="fecha">Exportado el: {{ $fecha_exportacion }}</span>
        <div class="proyecto-nombre">{{ $proyecto->nombre_proyecto }}</div>
        <div style="font-size: 10px; color: #555; text-transform: uppercase;">Bitácora Oficial de Obra</div>
    </div>

    @foreach($registros as $registro)
        <div class="registro-card">
            <span class="fecha">{{ $registro->created_at->format('d/m/Y H:i') }}</span>
            <div class="rubro-nombre">{{ $registro->recurso->nombre }}</div>
            
            <div class="notas">
                "{{ $registro->notas ?? 'Sin observaciones' }}"
            </div>

            <div class="meta">
                <span class="badge">Avance: <strong>{{ $registro->avance_fisico }}%</strong></span>
                <span class="badge">Cantidad: {{ $registro->cantidad_hoy }} (M2)</span>
                <span class="badge">Costo: ${{ number_format($registro->costo_hoy, 2) }}</span>
            </div>
        </div>
    @endforeach
</body>
</html>