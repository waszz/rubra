{{-- resources/views/livewire/proyecto/configuracion-general.blade.php --}}
<div class="min-h-screen bg-[#0d0d0d] text-white font-sans flex justify-center">
    <div class="w-full max-w-6xl">

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="px-10 pt-8 pb-0">
        <h1 class="text-xl font-semibold tracking-widest uppercase text-neutral-100">
            Ajustes Generales
        </h1>
        <p class="mt-1.5 text-sm text-neutral-500">
            Configura las preferencias globales de la aplicación.
        </p>
    </div>

    {{-- ── CARD PRINCIPAL ─────────────────────────────────────────────────── --}}
    <div class="mx-10 my-7 bg-[#141414] border border-[#222] rounded-xl p-8 max-w-4xl">

        {{-- Flash guardado --}}
        @if($saved)
            <div
                x-data x-init="setTimeout(() => $el.remove(), 2400)"
                class="mb-6 flex items-center gap-2 text-sm text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 rounded-lg px-4 py-2.5"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Configuración guardada correctamente.
            </div>
        @endif

        {{-- Errores de validación --}}
        @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-3">
                <ul class="list-disc list-inside text-sm text-red-400 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── SECCIÓN: INFO EMPRESA ───────────────────────────────────────── --}}
        <p class="text-[11px] text-neutral-600 font-medium tracking-[0.12em] uppercase mb-5">
            Información de la Empresa
        </p>

        <div class="grid grid-cols-2 gap-4">

            {{-- Nombre de la Empresa --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Nombre de la Empresa</label>
                <input
                    wire:model.lazy="nombre_empresa"
                    type="text"
                    placeholder="Ej: Empresa S.A."
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('nombre_empresa') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- Logo --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Logo (URL o Archivo Local)</label>
                <input
                    wire:model.lazy="logo_url"
                    type="text"
                    placeholder="https://ejemplo.com/logo.png"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('logo_url') <span class="text-xs text-red-400">{{ $message }}</span> @enderror

                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xs text-neutral-600">O sube una imagen:</span>
                    <label class="cursor-pointer bg-[#1e1e1e] border border-[#333] hover:border-[#e85d27] transition-colors rounded-md px-3 py-1.5 text-xs text-neutral-400">
                        Seleccionar archivo
                        <input wire:model="logo_file" type="file" accept="image/*" class="hidden" />
                    </label>

                    @if($logo_file)
                        <span class="text-xs text-neutral-500 truncate max-w-[120px]">{{ $logo_file->getClientOriginalName() }}</span>
                    @else
                        <span class="text-xs text-neutral-600">Ningún archivo seleccionado</span>
                    @endif

                    @if($logo_preview_url)
                        <img src="{{ $logo_preview_url }}" alt="Preview" class="w-8 h-8 object-cover rounded border border-[#333]" />
                    @endif
                </div>
                @error('logo_file') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- Página Web --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Página Web</label>
                <input
                    wire:model.lazy="pagina_web"
                    type="text"
                    placeholder="https://www.empresa.com"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('pagina_web') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- Redes Sociales --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Redes Sociales</label>
                <input
                    wire:model.lazy="redes_sociales"
                    type="text"
                    placeholder="@empresa / linkedin.com/in/..."
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
            </div>

            {{-- Teléfonos --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Teléfonos de Contacto</label>
                <input
                    wire:model.lazy="telefonos"
                    type="text"
                    placeholder="+598 99 000 000"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
            </div>

            {{-- Correo --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Correo Electrónico</label>
                <input
                    wire:model.lazy="correo"
                    type="email"
                    placeholder="contacto@empresa.com"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('correo') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- Latitud --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Latitud Sede</label>
                <input
                    wire:model.lazy="latitud"
                    type="text"
                    id="input-latitud"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('latitud') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- Longitud --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">Longitud Sede</label>
                <input
                    wire:model.lazy="longitud"
                    type="text"
                    id="input-longitud"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('longitud') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- ── MAPA ──────────────────────────────────────────────────────── --}}
            <div class="col-span-2 flex flex-col gap-2.5">
                <label class="text-[13px] text-neutral-400">
                    Ubicación de la Sede (Seleccionar en el Mapa)
                </label>

                {{-- Contenedor del mapa — wire:ignore evita que Livewire lo re-renderice --}}
                <div
                    id="rubra-map"
                    wire:ignore
                    class="w-full rounded-xl overflow-hidden border border-[#2a2a2a]"
                    style="height: 450px;"
                ></div>

                {{-- Tooltip inferior --}}
                <div class="flex items-center gap-2 bg-[#1e1e1e] border border-[#2a2a2a] rounded-lg px-3.5 py-2">
                    <div class="w-2 h-2 rounded-full bg-[#e85d27] shrink-0"></div>
                    <span class="text-xs text-neutral-500">Haz clic en el mapa para ubicar el proyecto</span>
                </div>
            </div>

        </div>{{-- /grid --}}

        {{-- ── DIVIDER + BOTÓN ─────────────────────────────────────────────── --}}
        <div class="border-t border-[#1e1e1e] mt-8 pt-6 flex justify-end">
            <button
                wire:click="guardar"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                type="button"
                class="bg-[#e85d27] hover:bg-[#d04e1f] active:scale-95 transition-all duration-150
                       text-white text-sm font-medium rounded-lg px-7 py-2.5 flex items-center gap-2"
            >
                <span wire:loading.remove wire:target="guardar">Guardar Configuración</span>
                <span wire:loading wire:target="guardar" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>

    </div>{{-- /card --}}

    {{-- ── CARD PERFIL DE USUARIO ─────────────────────────────────────────── --}}
<div class="mx-10 mb-7 bg-[#141414] border border-[#222] rounded-xl p-8 max-w-4xl">

    <p class="text-[11px] text-neutral-600 font-medium tracking-[0.12em] uppercase mb-5">
        Perfil de Usuario
    </p>

    {{-- Feedback --}}
    @if($successPerfil)
        <div class="mb-4 flex items-center gap-2 text-sm text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 rounded-lg px-4 py-2.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $successPerfil }}
        </div>
    @endif
    @if($errorPerfil)
        <div class="mb-4 flex items-center gap-2 text-sm text-red-400 bg-red-400/10 border border-red-400/20 rounded-lg px-4 py-2.5">
            {{ $errorPerfil }}
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4">

        <div class="flex flex-col gap-1.5">
            <label class="text-[13px] text-neutral-400">Nombre</label>
            <input wire:model.lazy="nombre_usuario" type="text"
                class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                       focus:outline-none focus:border-[#e85d27] transition-colors duration-200"/>
            @error('nombre_usuario') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-[13px] text-neutral-400">Correo Electrónico</label>
            <input wire:model.lazy="email_usuario" type="email"
                class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                       focus:outline-none focus:border-[#e85d27] transition-colors duration-200"/>
            @error('email_usuario') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1.5 col-span-2">
            <label class="text-[13px] text-neutral-400">Nueva Contraseña <span class="text-neutral-600">(dejar vacío para no cambiar)</span></label>
            <input wire:model.lazy="password_nuevo" type="password" placeholder="••••••••"
                class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                       focus:outline-none focus:border-[#e85d27] transition-colors duration-200"/>
            @error('password_nuevo') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
        </div>

    </div>

    <div class="border-t border-[#1e1e1e] mt-8 pt-6 flex items-center justify-between">
        {{-- Eliminar cuenta --}}
        <button
            wire:click="$set('modalEliminarCuenta', true)"
            type="button"
            class="text-xs text-red-500 hover:text-red-400 border border-red-500/30 hover:border-red-400/50
                   px-4 py-2 rounded-lg transition-colors">
            Eliminar mi cuenta
        </button>

        {{-- Guardar perfil --}}
        <button
            wire:click="guardarPerfil"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-not-allowed"
            type="button"
            class="bg-[#e85d27] hover:bg-[#d04e1f] active:scale-95 transition-all
                   text-white text-sm font-medium rounded-lg px-7 py-2.5">
            Guardar Perfil
        </button>
    </div>

</div>

    {{-- ── CARD PLAN Y SUSCRIPCIÓN ────────────────────────────────────────── --}}
    @php
        $u = auth()->user();
        $planColors = [
            'gratis'      => 'bg-amber-500/20 text-amber-400 border border-amber-500/30',
            'basico'      => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
            'profesional' => 'bg-purple-500/20 text-purple-400 border border-purple-500/30',
            'enterprise'  => 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
        ];
        $planBadge = $planColors[$u->plan] ?? $planColors['gratis'];
    @endphp
    <div class="mx-10 mb-7 bg-[#141414] border border-[#222] rounded-xl p-8 max-w-4xl">

        <p class="text-[11px] text-neutral-600 font-medium tracking-[0.12em] uppercase mb-5">
            Plan y Suscripción
        </p>

        <div class="flex items-center justify-between gap-6 flex-wrap">

            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="{{ $planBadge }} text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $u->planLabel() }}
                    </span>

                    @if($u->isOnTrial())
                        <span class="bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-semibold px-3 py-1 rounded-full">
                            {{ $u->trialDaysLeft() }} días restantes
                        </span>
                    @elseif($u->trialExpired())
                        <span class="bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-semibold px-3 py-1 rounded-full">
                            Trial vencido
                        </span>
                    @elseif($u->plan !== 'gratis' && $u->plan_expires_at)
                        @if($u->plan_expires_at->isFuture())
                            <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-semibold px-3 py-1 rounded-full">
                                Activo · vence {{ $u->plan_expires_at->format('d/m/Y') }}
                            </span>
                            @php
                                $diasHastaVenc = (int) now()->diffInDays($u->plan_expires_at, false);
                            @endphp
                            @if($diasHastaVenc <= 30)
                                <span class="bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-semibold px-3 py-1 rounded-full">
                                    {{ $diasHastaVenc }} días restantes
                                </span>
                            @endif
                        @else
                            <span class="bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-semibold px-3 py-1 rounded-full">
                                Vencido
                            </span>
                        @endif
                    @else
                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-semibold px-3 py-1 rounded-full">
                            Activo
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-5 text-xs text-neutral-500">
                    <span>Proyectos disponibles:
                        <span class="text-neutral-300 font-medium">
                            {{ $u->proyectosLimite() >= 999999 ? 'Ilimitados' : $u->proyectosLimite() }}
                        </span>
                    </span>
                    @if($u->plan !== 'gratis')
                        <span>Período:
                            <span class="text-neutral-300 font-medium">{{ ucfirst($u->plan_periodo ?? 'mensual') }}</span>
                        </span>
                    @endif
                </div>
            </div>

            @if(!$u->isGod() && $u->plan !== 'enterprise')
                <a href="{{ url('/#precios') }}"
                   class="shrink-0 bg-[#e85d27] hover:bg-[#d04e1f] active:scale-95 transition-all duration-150
                          text-white text-xs font-medium rounded-lg px-5 py-2.5">
                    {{ $u->plan === 'gratis' ? 'Contratar plan' : 'Mejorar plan' }}
                </a>
            @endif

        </div>

    </div>{{-- /card plan --}}

{{-- ── MODAL ELIMINAR CUENTA ───────────────────────────────────────────── --}}
@if($modalEliminarCuenta)
<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-[#141414] border border-[#222] rounded-2xl p-7 w-full max-w-sm shadow-2xl">

        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-red-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-neutral-100">Eliminar cuenta</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Esta acción es irreversible.</p>
            </div>
        </div>

        <p class="text-xs text-neutral-400 mb-4">
            Ingresá tu contraseña actual para confirmar que querés eliminar tu cuenta permanentemente.
        </p>

        <input
            wire:model="confirmar_password"
            type="password"
            placeholder="Tu contraseña actual"
            class="w-full bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 mb-4
                   focus:outline-none focus:border-red-500 transition-colors"
        />

        @if($errorPerfil)
            <p class="text-xs text-red-400 mb-4">{{ $errorPerfil }}</p>
        @endif

        <div class="flex justify-end gap-3">
            <button
                wire:click="$set('modalEliminarCuenta', false)"
                class="px-4 py-2 text-xs text-neutral-400 hover:text-white border border-[#2a2a2a] rounded-lg transition-colors">
                Cancelar
            </button>
            <button
                wire:click="eliminarCuenta"
                class="px-4 py-2 text-xs bg-red-600 hover:bg-red-500 text-white rounded-lg transition-colors">
                Eliminar permanentemente
            </button>
        </div>

    </div>
</div>
@endif

    @push('scripts')
<script>
(function () {
    const LAT_INIT = {{ (float) $latitud }};
    const LNG_INIT = {{ (float) $longitud }};

    const PROYECTOS = @json($proyectos);

    let map        = null;
    let sedeMarker = null;
    let proyMarkers = [];

    function orangeIcon() {
        return L.divIcon({
            className: '',
            html: '<div style="width:16px;height:16px;border-radius:50%;background:#e85d27;border:3px solid #fff;box-shadow:0 0 0 2px #e85d27;"></div>',
            iconSize: [16, 16], iconAnchor: [8, 8],
        });
    }

    function blueIcon() {
        return L.divIcon({
            className: '',
            html: '<div style="width:14px;height:14px;border-radius:50%;background:#3b82f6;border:3px solid #fff;box-shadow:0 0 0 2px #3b82f6;"></div>',
            iconSize: [14, 14], iconAnchor: [7, 7],
        });
    }

    function initMap() {
        const el = document.getElementById('rubra-map');
        if (!el || map) return;

        map = L.map('rubra-map', { zoomControl: true }).setView([LAT_INIT, LNG_INIT], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Marcador sede (naranja)
        sedeMarker = L.marker([LAT_INIT, LNG_INIT], { icon: orangeIcon() })
            .addTo(map)
            .bindPopup('<b>Sede</b>');

        // Marcadores proyectos (azul)
        PROYECTOS.forEach(p => {
            if (!p.ubicacion_lat || !p.ubicacion_lng) return;
            const m = L.marker([p.ubicacion_lat, p.ubicacion_lng], { icon: blueIcon() })
                .addTo(map)
                .bindPopup(`<b>${p.nombre_proyecto}</b>`);
            proyMarkers.push({ id: p.id, marker: m });
        });

        // Clic en mapa → mueve sede
        map.on('click', function (e) {
            const { lat, lng } = e.latlng;
            sedeMarker.setLatLng([lat, lng]);
            document.getElementById('input-latitud').value = lat.toFixed(14);
            document.getElementById('input-longitud').value = lng.toFixed(14);
            @this.call('actualizarUbicacion', lat, lng);
        });

        setTimeout(() => map && map.invalidateSize(), 300);
    }

    // Llamado desde el onchange del select
    window.filtrarProyecto = function(id) {
        if (!map) return;
        if (!id) {
            // Mostrar todos
            proyMarkers.forEach(p => p.marker.addTo(map));
            map.setView([LAT_INIT, LNG_INIT], 12);
            return;
        }
        const found = proyMarkers.find(p => p.id == id);
        if (found) {
            proyMarkers.forEach(p => map.removeLayer(p.marker));
            found.marker.addTo(map);
            map.setView(found.marker.getLatLng(), 15);
            found.marker.openPopup();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(initMap, 150));
    } else {
        setTimeout(initMap, 150);
    }

    document.addEventListener('livewire:navigated', () => {
        map = null; sedeMarker = null; proyMarkers = [];
        setTimeout(initMap, 150);
    });

    Livewire.on('config-guardada', () => {
        const lat = parseFloat(document.getElementById('input-latitud')?.value);
        const lng = parseFloat(document.getElementById('input-longitud')?.value);
        if (map && sedeMarker && !isNaN(lat) && !isNaN(lng)) {
            sedeMarker.setLatLng([lat, lng]);
            map.setView([lat, lng]);
        }
    });
})();
</script>
 @endpush

</div>
</div>