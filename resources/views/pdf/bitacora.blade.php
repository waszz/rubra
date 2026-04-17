@php $dark = $darkMode ?? true; @endphp
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: {{ $dark ? '#0a0a0a' : '#ffffff' }}; color: {{ $dark ? '#fff' : '#111827' }}; margin: 0; padding: 40px; }
        .header { border-bottom: 2px solid {{ $dark ? '#1a1a1a' : '#e5e7eb' }}; padding-bottom: 20px; margin-bottom: 30px; }
        .proyecto-nombre { font-size: 24px; font-weight: bold; text-transform: uppercase; color: {{ $dark ? '#00ff88' : '#111827' }}; }
        .registro-card { background-color: {{ $dark ? '#111' : '#f9fafb' }}; border: 1px solid {{ $dark ? '#222' : '#e5e7eb' }}; border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        .fecha { color: {{ $dark ? '#666' : '#9ca3af' }}; font-size: 10px; float: right; }
        .rubro-nombre { color: {{ $dark ? '#4dabf7' : '#1d4ed8' }}; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .autor { font-size: 10px; color: {{ $dark ? '#888' : '#6b7280' }}; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px; font-weight: bold; }
        .autor span { color: #e85d27; }
        .notas { font-style: italic; color: {{ $dark ? '#ccc' : '#374151' }}; margin-top: 10px; font-size: 13px; }
        .meta { margin-top: 15px; font-size: 11px; color: {{ $dark ? '#888' : '#6b7280' }}; }
        .badge { background: {{ $dark ? '#1a1a1a' : '#e5e7eb' }}; color: {{ $dark ? '#aaa' : '#374151' }}; padding: 4px 10px; border-radius: 10px; margin-right: 5px; }
        .badge strong { color: {{ $dark ? '#00ff88' : '#000000' }}; }
        .titulo-sub { font-size: 10px; color: {{ $dark ? '#555' : '#6b7280' }}; text-transform: uppercase; }
        .foto { margin-top: 12px; }
        .foto img { max-width: 100%; max-height: 220px; border-radius: 8px; border: 1px solid {{ $dark ? '#333' : '#d1d5db' }}; display: block; }
    </style>
</head>
<body>
    <div class="header">
        <span class="fecha">Exportado el: {{ $fecha_exportacion }}</span>
        <div class="proyecto-nombre">{{ $proyecto->nombre_proyecto }}</div>
        <div style="font-size: 10px; color: {{ $dark ? '#555' : '#6b7280' }}; text-transform: uppercase;">Bitácora Oficial de Obra</div>
    </div>

    @foreach($registros as $registro)
        <div class="registro-card">
            <span class="fecha">{{ $registro->created_at->format('d/m/Y H:i') }}</span>
            <div class="rubro-nombre">{{ $registro->recurso?->nombre ?? 'Sin rubro' }}</div>
            <div class="autor">
                @if($registro->user)
                    <span>{{ ucfirst($registro->user->name) }}</span>
                    @if($registro->user->role)
                        · <span>{{ ucfirst($registro->user->role) }}</span>
                    @endif
                    ·
                @endif
                Reporte: {{ $registro->recurso?->nombre ?? 'Sin recurso' }}
            </div>
            
            <div class="notas">
                "{{ $registro->notas ?? 'Sin observaciones' }}"
            </div>

            <div class="meta">
                <span class="badge">Avance: <strong>{{ $registro->avance_fisico }}%</strong></span>
                <span class="badge">Cantidad: {{ $registro->cantidad_hoy }} (M2)</span>
                <span class="badge">Costo: ${{ number_format($registro->costo_hoy, 2) }}</span>
            </div>
            @if(!empty($registro->foto_base64))
                <div class="foto">
                    <img src="{{ $registro->foto_base64 }}" alt="Foto de obra">
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>