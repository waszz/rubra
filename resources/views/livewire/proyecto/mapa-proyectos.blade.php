<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">

    <div class="bg-[#0d0d0d] border border-white/5 rounded-[2rem] p-10 mb-10 relative overflow-hidden group">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-orange-500/5 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <h2 class="text-xl font-black text-white uppercase tracking-[0.3em] mb-2">
                Mapa de <span class="text-orange-500">Proyectos</span>
            </h2>
            <p class="text-sm text-gray-500 font-medium tracking-wide">
                Visualiza la ubicación de tus proyectos en el mapa.
            </p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-[#111111] border border-gray-800/50 rounded-[2rem] p-8 mb-8">
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <label class="text-[10px] text-gray-500 uppercase font-black">Estado:</label>
                <select wire:model.live="filtroEstado" class="bg-[#1a1a1a] border border-white/5 text-white text-xs px-3 py-1 rounded-lg">
                    <option value="todos">Todos</option>
                    <option value="en_revision">En Revisión</option>
                    <option value="activo">Activo</option>
                    <option value="pausado">Pausado</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-[10px] text-gray-500 uppercase font-black">Proyecto:</label>
                <input type="text" wire:model.live.debounce.300ms="filtroProyecto" placeholder="Buscar proyecto..."
                    class="bg-[#1a1a1a] border border-white/5 text-white text-xs px-3 py-1 rounded-lg w-48">
            </div>
        </div>
    </div>

    {{-- MAPA --}}
    <div class="bg-[#111111] border border-gray-800/50 rounded-[2rem] p-8" style="isolation: isolate;">
        <div id="mapa-proyectos" class="w-full rounded-2xl overflow-hidden" style="height: 450px; position: relative; z-index: 0;" wire:ignore></div>
        <div class="mt-6 text-center">
            <div class="text-[10px] text-gray-500 uppercase font-bold">
                {{ $proyectosFiltrados->count() }} proyectos mostrados
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
(function () {
    let map = null;

    function initMapa() {
        const el = document.getElementById('mapa-proyectos');
        if (!el || map) return;

        map = L.map('mapa-proyectos', { zoomControl: true }).setView([-32.5, -56.0], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Marcador de la base (rojo)
        const baseIcon = L.divIcon({
            html: '<div style="width:16px;height:16px;border-radius:50%;background:#ef4444;border:3px solid #fff;box-shadow:0 0 0 2px #ef4444;"></div>',
            iconSize: [16, 16], iconAnchor: [8, 8],
        });

        L.marker([-34.9011, -56.1645], { icon: baseIcon })
            .addTo(map)
            .bindPopup('<b>Base Principal</b><br>Montevideo, Uruguay');

        // Icono para proyectos (azul)
        const proyectoIcon = L.divIcon({
            html: '<div style="width:14px;height:14px;border-radius:50%;background:#3b82f6;border:3px solid #fff;box-shadow:0 0 0 2px #3b82f6;"></div>',
            iconSize: [14, 14], iconAnchor: [7, 7],
        });

        // Marcadores de proyectos
        @foreach($proyectosFiltrados as $proyecto)
            L.marker([{{ $proyecto->ubicacion_lat }}, {{ $proyecto->ubicacion_lng }}], {icon: proyectoIcon})
                .addTo(map)
                .bindPopup(`
                    <b>{{ $proyecto->nombre_proyecto }}</b><br>
                    Cliente: {{ $proyecto->cliente }}<br>
                    Estado: {{ ucfirst(str_replace('_', ' ', $proyecto->estado_obra)) }}<br>
                    <a href="{{ route('proyectos.presupuesto', $proyecto->id) }}" style="color:#3b82f6;">Ver Proyecto</a>
                `);
        @endforeach

        setTimeout(() => map && map.invalidateSize(), 300);
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(initMapa, 150));
    } else {
        setTimeout(initMapa, 150);
    }

    // Reinicializar cuando Livewire navega
    document.addEventListener('livewire:navigated', () => {
        map = null;
        setTimeout(initMapa, 150);
    });

    // Reinicializar cuando los filtros cambien
    document.addEventListener('livewire:updated', () => {
        map = null;
        setTimeout(initMapa, 150);
    });
})();
</script>
@endpush

