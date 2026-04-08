{{-- resources/views/livewire/proyecto/configuracion-general.blade.php --}}
<div class="min-h-screen bg-[#0d0d0d] text-white font-sans flex justify-center">
    <div class="w-full max-w-6xl">

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="px-4 sm:px-10 pt-6 sm:pt-8 pb-0">
        <h1 class="text-xl font-semibold tracking-widest uppercase text-neutral-100">
            Ajustes Generales
        </h1>
        <p class="mt-1.5 text-sm text-neutral-500">
            Configura las preferencias globales de la aplicación.
        </p>
    </div>

    {{-- ── CARD PRINCIPAL ─────────────────────────────────────────────────── --}}

    {{-- Flash de pago --}}
    @if(session('success_plan'))
        <div x-data x-init="setTimeout(() => $el.remove(), 5000)"
             class="mx-4 sm:mx-10 mt-4 flex items-center gap-2 text-sm text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 rounded-xl px-4 py-3 max-w-4xl">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success_plan') }}
        </div>
    @endif
    @if(session('error_plan'))
        <div x-data x-init="setTimeout(() => $el.remove(), 5000)"
             class="mx-4 sm:mx-10 mt-4 flex items-center gap-2 text-sm text-red-400 bg-red-400/10 border border-red-400/20 rounded-xl px-4 py-3 max-w-4xl">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error_plan') }}
        </div>
    @endif

    <div class="mx-4 sm:mx-10 my-5 sm:my-7 bg-[#141414] border border-[#222] rounded-xl p-4 sm:p-8 max-w-4xl">

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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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

            {{-- RUT --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] text-neutral-400">RUT</label>
                <input
                    wire:model.lazy="rut"
                    type="text"
                    placeholder="Ej: 21.234.567-8"
                    class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                           focus:outline-none focus:border-[#e85d27] transition-colors duration-200"
                />
                @error('rut') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>

            {{-- Logo --}}
            <div class="flex flex-col gap-1.5 sm:col-span-2">
                <label class="text-[13px] text-neutral-400">Logo</label>

                <div class="flex items-center gap-4">
                    <label class="cursor-pointer bg-[#1e1e1e] border border-[#333] hover:border-[#e85d27] transition-colors rounded-md px-4 py-2 text-xs text-neutral-400">
                        Seleccionar archivo
                        <input wire:model="logo_file" type="file" accept="image/*" class="hidden" />
                    </label>

                    @if($logo_file)
                        <span class="text-xs text-neutral-400 truncate max-w-[160px]">{{ $logo_file->getClientOriginalName() }}</span>
                    @elseif(!$logo_preview_url)
                        <span class="text-xs text-neutral-600">Ningún archivo seleccionado</span>
                    @endif

                    @if($logo_preview_url)
                        <img src="{{ $logo_preview_url }}" alt="Preview" class="h-12 object-contain rounded border border-[#2a2a2a] bg-[#111] p-1" />
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
            <div class="col-span-1 sm:col-span-2 flex flex-col gap-2.5">
                <label class="text-[13px] text-neutral-400">
                    Ubicación de la Sede (Seleccionar en el Mapa)
                </label>

          {{-- Selector de Proyecto --}}
<select
    wire:model="proyecto_activo"
    onchange="filtrarProyecto(this.value)"
    class="bg-[#1a1a1a] border border-[#2e2e2e] hover:border-[#e85d27] focus:border-[#e85d27] focus:outline-none transition-colors rounded-lg px-3.5 py-2 text-sm text-neutral-300 cursor-pointer"
>
    <option value="">TODOS LOS PROYECTOS</option>
    @foreach($proyectos as $p)
        <option value="{{ $p['id'] }}">{{ $p['nombre_proyecto'] }}</option>
    @endforeach
</select>

                {{-- Contenedor del mapa — wire:ignore evita que Livewire lo re-renderice --}}
                <div style="isolation: isolate;">
                    <div
                        id="rubra-map"
                        wire:ignore
                        class="w-full rounded-xl overflow-hidden border border-[#2a2a2a]"
                        style="height: 450px; position: relative; z-index: 0;"
                    ></div>
                </div>

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
                class="w-full sm:w-auto bg-[#e85d27] hover:bg-[#d04e1f] active:scale-95 transition-all duration-150
                       text-white text-sm font-medium rounded-lg px-7 py-2.5 flex items-center justify-center gap-2"
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
<div class="mx-4 sm:mx-10 mb-5 sm:mb-7 bg-[#141414] border border-[#222] rounded-xl p-4 sm:p-8 max-w-4xl">

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

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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

        <div class="flex flex-col gap-1.5 col-span-1 sm:col-span-2">
            <label class="text-[13px] text-neutral-400">Nueva Contraseña <span class="text-neutral-600">(dejar vacío para no cambiar)</span></label>
            <input wire:model.lazy="password_nuevo" type="password" placeholder="••••••••"
                class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                       focus:outline-none focus:border-[#e85d27] transition-colors duration-200"/>
            @error('password_nuevo') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
        </div>

    </div>

    <div class="border-t border-[#1e1e1e] mt-8 pt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        {{-- Eliminar cuenta --}}
        <button
            wire:click="$set('modalEliminarCuenta', true)"
            type="button"
            class="text-xs text-red-500 hover:text-red-400 border border-red-500/30 hover:border-red-400/50
                   px-4 py-2.5 rounded-lg transition-colors text-center">
            Eliminar mi cuenta
        </button>

        {{-- Guardar perfil --}}
        <button
            wire:click="guardarPerfil"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-not-allowed"
            type="button"
            class="bg-[#e85d27] hover:bg-[#d04e1f] active:scale-95 transition-all
                   text-white text-sm font-medium rounded-lg px-7 py-2.5 text-center">
            Guardar Perfil
        </button>
    </div>

</div>

{{-- ── CARD PLAN Y SUSCRIPCIÓN ────────────────────────────────────────── --}}
@php
    $user = auth()->user();
    $diasRestantes = $user->trialDaysLeft();
    $totalDias = 30;
    $porcentaje = $user->isOnTrial() ? max(0, min(100, round(($diasRestantes / $totalDias) * 100))) : 0;
@endphp

<div class="mx-4 sm:mx-10 mb-5 sm:mb-7 bg-[#141414] border border-[#222] rounded-xl p-4 sm:p-8 max-w-4xl">

    <p class="text-[11px] text-neutral-600 font-medium tracking-[0.12em] uppercase mb-5">
        Plan y Suscripción
    </p>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">

        {{-- Plan actual --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#e85d27]/15 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#e85d27]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-neutral-100">{{ $user->planLabel() }}</p>
                <p class="text-xs text-neutral-500 mt-0.5">
                    @if($user->isOnTrial())
                        Prueba activa · vence el {{ $user->trial_ends_at->format('d/m/Y') }}
                    @elseif($user->trialExpired())
                        <span class="text-red-400">Período de prueba vencido</span>
                    @elseif($user->plan_expires_at)
                        @php $diasRestantes = (int) now()->diffInDays($user->plan_expires_at, false); @endphp
                        Activo hasta el <span class="text-neutral-300 font-semibold">{{ $user->plan_expires_at->format('d/m/Y') }}</span>
                        @if($diasRestantes > 0)
                            · <span class="text-[#e85d27]">{{ $diasRestantes }} día{{ $diasRestantes !== 1 ? 's' : '' }} restantes</span>
                        @endif
                    @else
                        Plan activo
                    @endif
                </p>
            </div>
        </div>

        {{-- Badge estado --}}
        @if($user->isOnTrial())
            <span class="text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-[#e85d27]/15 text-[#e85d27] border border-[#e85d27]/30">
                En prueba
            </span>
        @elseif($user->trialExpired())
            <span class="text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-red-500/15 text-red-400 border border-red-500/30">
                Vencido
            </span>
        @else
            <span class="text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                Activo
            </span>
        @endif

    </div>

    {{-- Barra de tiempo restante (solo en prueba) --}}
    @if($user->isOnTrial())
    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-xs text-neutral-500">Días restantes de prueba</span>
            <span class="text-xs font-black text-[#e85d27]">{{ $diasRestantes }} día{{ $diasRestantes !== 1 ? 's' : '' }}</span>
        </div>
        <div class="w-full h-1.5 bg-[#222] rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700"
                 style="width: {{ $porcentaje }}%; background: @if($porcentaje > 50) #e85d27 @elseif($porcentaje > 20) #f59e0b @else #ef4444 @endif;">
            </div>
        </div>
        <p class="text-[11px] text-neutral-600 mt-2">
            Tu prueba incluye 3 proyectos y acceso completo a todas las funcionalidades.
        </p>
    </div>
    @elseif($user->trialExpired())
    <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-3">
        <p class="text-xs text-red-400">
            Tu período de prueba terminó el {{ $user->trial_ends_at->format('d/m/Y') }}. Elegí un plan para seguir usando la plataforma.
        </p>
    </div>
    @endif

    {{-- Límites del plan --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
        @php
            $limites = match($user->plan) {
                'gratis'      => ['proyectos' => '3',   'acceso' => 'Completo', 'vigencia' => '30 días'],
                'basico'      => ['proyectos' => '10',  'acceso' => 'Completo', 'vigencia' => ucfirst($user->plan_periodo ?? 'Mensual')],
                'profesional' => ['proyectos' => '25',  'acceso' => 'Completo', 'vigencia' => ucfirst($user->plan_periodo ?? 'Mensual')],
                'enterprise'  => ['proyectos' => '100', 'acceso' => 'Completo', 'vigencia' => ucfirst($user->plan_periodo ?? 'Mensual')],
                default       => ['proyectos' => '—',   'acceso' => '—',        'vigencia' => '—'],
            };
        @endphp
        <div class="bg-[#111] border border-[#2a2a2a] rounded-lg p-3 text-center">
            <p class="text-lg font-black text-[#e85d27]">{{ $limites['proyectos'] }}</p>
            <p class="text-[10px] uppercase tracking-wider text-neutral-500 mt-0.5">Proyectos</p>
        </div>
        <div class="bg-[#111] border border-[#2a2a2a] rounded-lg p-3 text-center">
            <p class="text-lg font-black text-neutral-100">{{ $limites['acceso'] }}</p>
            <p class="text-[10px] uppercase tracking-wider text-neutral-500 mt-0.5">Acceso</p>
        </div>
        <div class="bg-[#111] border border-[#2a2a2a] rounded-lg p-3 text-center">
            <p class="text-lg font-black text-neutral-100">{{ $limites['vigencia'] }}</p>
            <p class="text-[10px] uppercase tracking-wider text-neutral-500 mt-0.5">Vigencia</p>
        </div>
    </div>

    {{-- CTA upgrade --}}
    @if($user->plan !== 'enterprise')
    <div class="border-t border-[#1e1e1e] pt-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <p class="text-xs text-neutral-500">
            @if($user->plan === 'profesional')
                ¿Necesitás más proyectos? Subí al plan Enterprise.
            @else
                ¿Querés más proyectos? Mejorá tu plan.
            @endif
        </p>
        <a href="{{ url('/#precios') }}"
           class="bg-[#e85d27] hover:bg-[#d04e1f] text-white text-xs font-black uppercase tracking-widest px-5 py-2.5 rounded-lg transition-all text-center whitespace-nowrap">
            {{ $user->plan === 'profesional' ? 'Ver Enterprise' : 'Ver planes' }}
        </a>
    </div>
    @endif

</div>

{{-- ── MODAL ELIMINAR CUENTA ───────────────────────────────────────────── --}}
@if($modalEliminarCuenta)
<div class="fixed inset-0 bg-black/70 flex items-end sm:items-center justify-center z-50 px-4 pb-4 sm:pb-0">
    <div class="bg-[#141414] border border-[#222] rounded-2xl p-6 w-full max-w-sm shadow-2xl">

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