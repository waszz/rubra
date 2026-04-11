{{-- resources/views/livewire/proyecto/gestion-usuarios.blade.php --}}
<div class="min-h-screen bg-white dark:bg-[#0d0d0d] text-black dark:text-white font-sans">

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="px-4 sm:px-10 pt-6 sm:pt-8 pb-0 flex flex-col sm:flex-row sm:items-start gap-4 sm:justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-gray-400 dark:text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-5M9 20H4v-2a4 4 0 015-5m4-4a4 4 0 100-8 4 4 0 000 8z"/>
            </svg>
            <div>
                <h1 class="text-xl font-semibold tracking-widest uppercase text-black dark:text-neutral-100">Gestión de Usuarios</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-500 mt-0.5">Administra los roles y categorías de los miembros de tu equipo.</p>
            </div>
        </div>

        {{-- Tabs + Botón --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex bg-gray-100 dark:bg-[#1a1a1a] border border-gray-200 dark:border-[#2a2a2a] rounded-xl p-1">
                <button
                    wire:click="$set('tab', 'usuarios')"
                    class="px-4 py-1.5 text-sm rounded-lg transition-colors {{ $tab === 'usuarios' ? 'bg-white dark:bg-white text-black dark:text-black font-medium' : 'text-gray-500 dark:text-neutral-400 hover:text-black dark:hover:text-white' }}"
                >
                    Usuarios
                </button>
                <button
                    wire:click="$set('tab', 'permisos')"
                    class="px-4 py-1.5 text-sm rounded-lg transition-colors {{ $tab === 'permisos' ? 'bg-white dark:bg-white text-black dark:text-black font-medium' : 'text-gray-500 dark:text-neutral-400 hover:text-black dark:hover:text-white' }}"
                >
                    Permisos
                </button>
            </div>

            @if($tab === 'usuarios')
            <div class="flex flex-col items-start gap-2">
                <button
                    wire:click="abrirModalInvitar"
                    class="flex items-center gap-2 transition-all text-sm font-medium rounded-xl px-4 py-2 bg-orange-500 hover:bg-orange-600 dark:bg-[#e85d27] dark:hover:bg-[#d04e1f] active:scale-95 text-white"
                    title="Invitar un nuevo usuario"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Invitar Usuario
                </button>
                <p class="text-[10px] text-gray-500 dark:text-neutral-500 font-bold">
                    Límites por rol:
                    @foreach($planLimits as $r => $limit)
                        <span class="text-[#e85d27] font-black">{{ $limit }}</span> {{ $roles[$r] ?? ucfirst($r) }}
                        <span class="text-gray-400 dark:text-neutral-600">({{ $roleCounts[$r] ?? 0 }}/{{ $limit }})</span>
                        @if(!$loop->last)<span class="mx-1">•</span>@endif
                    @endforeach
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── FEEDBACK ────────────────────────────────────────────────────────── --}}
    @if($successMsg)
        <div class="mx-4 sm:mx-10 mt-4 flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-400/10 border border-emerald-200 dark:border-emerald-400/20 rounded-lg px-4 py-2.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $successMsg }}
        </div>
    @endif
    @if($errorMsg)
        <div class="mx-4 sm:mx-10 mt-4 flex items-center gap-2 text-sm text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-400/10 border border-red-200 dark:border-red-400/20 rounded-lg px-4 py-2.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ $errorMsg }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: USUARIOS                                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($tab === 'usuarios')
    <div class="mx-4 sm:mx-10 my-5 sm:my-7 bg-white dark:bg-[#141414] border border-gray-200 dark:border-[#222] rounded-xl overflow-hidden">

        {{-- Buscador + contador --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-[#222]">
            <div class="relative w-full sm:w-80">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="busqueda"
                    type="text"
                    placeholder="Buscar usuarios por nombre o email..."
                    class="w-full bg-white dark:bg-[#111] border border-gray-200 dark:border-[#2a2a2a] rounded-lg pl-9 pr-4 py-2 text-sm text-black dark:text-neutral-200 placeholder-gray-400 dark:placeholder-neutral-600 focus:outline-none focus:border-orange-500 dark:focus:border-[#e85d27] transition-colors"
                />
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-500 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zm3 4h12M6 12h12M6 16h12"/>
                </svg>
                {{ $totalActivos }} usuario{{ $totalActivos !== 1 ? 's' : '' }} activo{{ $totalActivos !== 1 ? 's' : '' }}
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
        <table class="w-full min-w-[480px]">
            <thead>
                <tr class="border-b border-gray-200 dark:border-[#1e1e1e]">
                    <th class="text-left px-6 py-3 text-[11px] font-medium tracking-widest uppercase text-gray-500 dark:text-neutral-600">Usuario</th>
                    <th class="text-left px-6 py-3 text-[11px] font-medium tracking-widest uppercase text-gray-500 dark:text-neutral-600">Rol Actual</th>
                    <th class="text-left px-6 py-3 text-[11px] font-medium tracking-widest uppercase text-gray-500 dark:text-neutral-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-[#1a1a1a]">
                @forelse($usuarios as $usuario)
                <tr class="hover:bg-gray-100 dark:hover:bg-[#1a1a1a] transition-colors">
                    {{-- Avatar + info --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-orange-100 dark:bg-[#e85d27]/20 border border-orange-200 dark:border-[#e85d27]/30 flex items-center justify-center text-sm font-semibold text-orange-500 dark:text-[#e85d27] shrink-0">
                                {{ strtoupper(substr($usuario->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm text-black dark:text-neutral-200 font-medium">{{ $usuario->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-500">{{ $usuario->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Badge rol --}}
                    {{-- Badge rol --}}
<td class="px-6 py-4">
    @if($usuario->id === auth()->id())
        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-[#e85d27]/20 text-orange-500 dark:text-[#e85d27] border border-orange-200 dark:border-[#e85d27]/30">
            {{ strtoupper($usuario->role ?? 'SIN ROL') }}
        </span>
    @else
        @php
            $rolEnPivot = \DB::table('proyecto_user')
                ->where('user_id', $usuario->id)
                ->whereIn('proyecto_id', \App\Models\Proyecto::where('user_id', auth()->id())->pluck('id'))
                ->value('rol') ?? $usuario->role;
        @endphp
        <select
            wire:change="cambiarRol({{ $usuario->id }}, $event.target.value)"
            class="bg-gray-100 dark:bg-[#1e1e1e] border border-gray-200 dark:border-[#2a2a2a] rounded-lg px-2 py-1 text-xs text-black dark:text-neutral-300 focus:outline-none focus:border-orange-500 dark:focus:border-[#e85d27] transition-colors cursor-pointer"
        >
                @foreach($roles as $val => $label)
                    <option value="{{ $val }}" {{ $rolEnPivot === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
        </select>
    @endif
</td>
                    {{-- Acciones --}}
                    <td class="px-6 py-4">
                       @if($usuario->id === auth()->id())
    <span class="text-xs text-gray-400 dark:text-neutral-600 italic">Tú</span>
@else
    <button
    wire:click="confirmarEliminarUsuario({{ $usuario->id }})"
    class="text-xs text-red-600 dark:text-red-400 hover:text-red-400 dark:hover:text-red-300 transition-colors"
>
    Eliminar
</button>
@endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400 dark:text-neutral-600">
                        No se encontraron usuarios.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>{{-- /overflow-x-auto --}}

        {{-- Paginación --}}
        @if($usuarios->hasPages())
        <div class="px-6 py-4 border-t border-[#1e1e1e]">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>

    {{-- Cards de roles --}}
                    <div class="mx-4 sm:mx-10 mb-7 sm:mb-10 grid grid-cols-1 sm:grid-cols-4 gap-4">
        @foreach([
            ['supervisor',     'Supervisor',     '#a78bfa', 'Acceso total al sistema. Puede gestionar usuarios, categorías, ajustes globales y todos los proyectos.'],
            ['presupuestador', 'Presupuestador', '#60a5fa', 'Especializado en el control de recursos y armado de presupuestos. Acceso a catálogos y plantillas.'],
            ['jefe_obra',      'Jefe de Obra',   '#34d399', 'Enfocado en la ejecución. Acceso a presupuestos aprobados, estadísticas, bitácoras y mapas de obra.'],
            ['administrativo', 'Administrativo', '#f59e0b', 'Gestiona tareas administrativas, reportes y soporte interno. Acceso a reportes y usuarios.'],
        ] as [$key, $label, $color, $desc])
        <div class="bg-white dark:bg-[#141414] border border-gray-200 dark:border-[#222] rounded-xl p-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: {{ $color }}22;">
                    <svg class="w-4 h-4" style="color: {{ $color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-5M9 20H4v-2a4 4 0 015-5m4-4a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: {{ $color }};">{{ $label }}</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-500 leading-relaxed">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: PERMISOS                                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($tab === 'permisos')
    <div class="mx-4 sm:mx-10 my-5 sm:my-7 bg-white dark:bg-[#141414] border border-gray-200 dark:border-[#222] rounded-xl overflow-hidden">

        {{-- Header tabla --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200 dark:border-[#222]">
            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-black dark:text-neutral-100 uppercase tracking-wider">Matriz de Permisos por Rol</h2>
                <p class="text-xs text-gray-500 dark:text-neutral-500 mt-0.5">Define qué secciones de la aplicación puede ver cada rol.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full min-w-[480px]">
            <thead>
                <tr class="border-b border-gray-200 dark:border-[#1e1e1e]">
                    <th class="text-left px-6 py-3 text-[11px] font-medium tracking-widest uppercase text-gray-500 dark:text-neutral-600 w-1/3">
                        Sección / Tab
                    </th>
                    @foreach($roles as $rolKey => $rolLabel)
                    <th class="text-center px-6 py-3 text-[11px] font-medium tracking-widest uppercase text-gray-500 dark:text-neutral-600">
                        {{ $rolLabel }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-[#1a1a1a]">
                @foreach($secciones as $secKey => $secLabel)
                <tr class="hover:bg-gray-100 dark:hover:bg-[#1a1a1a] transition-colors">
                    <td class="px-6 py-4 text-sm text-black dark:text-neutral-300">{{ $secLabel }}</td>
                    @foreach($roles as $rolKey => $_)
                    <td class="px-6 py-4 text-center">
                        <button
                            wire:click="togglePermiso('{{ $rolKey }}', '{{ $secKey }}')"
                            class="transition-transform hover:scale-110 active:scale-95"
                        >
                            @if($matriz[$rolKey][$secKey] ?? false)
                                {{-- Check verde --}}
                                <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l3 3 5-5"/>
                                </svg>
                            @else
                                {{-- X gris --}}
                                <svg class="w-5 h-5 text-gray-400 dark:text-neutral-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                        </button>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>{{-- /overflow-x-auto --}}
    </div>
    @endif

    {{-- ── MODAL INVITAR ───────────────────────────────────────────────────── --}}
    @if($modalInvitar)
    <div class="fixed inset-0 bg-black/70 flex items-end sm:items-center justify-center z-50 px-4 pb-4 sm:pb-0">
        <div class="bg-[#141414] border border-[#222] rounded-2xl p-6 w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-neutral-100">Invitar Usuario</h3>
                <button wire:click="cerrarModal" class="text-neutral-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($errorMsg)
                <div class="mb-4 text-sm text-red-400 bg-red-400/10 border border-red-400/20 rounded-lg px-4 py-2.5">
                    {{ $errorMsg }}
                </div>
            @endif

            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[13px] text-neutral-400">Correo Electrónico</label>
                    <input
                        wire:model="invitar_email"
                        type="email"
                        placeholder="usuario@empresa.com"
                        class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200 placeholder-neutral-600
                               focus:outline-none focus:border-[#e85d27] transition-colors"
                    />
                    @error('invitar_email') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[13px] text-neutral-400">Rol</label>
                    <select
                        wire:model="invitar_rol"
                        class="bg-[#111] border border-[#2a2a2a] rounded-lg px-3.5 py-2.5 text-sm text-neutral-200
                               focus:outline-none focus:border-[#e85d27] transition-colors cursor-pointer"
                    >
                        @foreach($roles as $val => $label)
                            @php
                                $allowed = $planLimits[$val] ?? 0;
                                $count = $roleCounts[$val] ?? 0;
                            @endphp
                            <option value="{{ $val }}" @if($count >= $allowed) disabled @endif>
                                {{ $label }}@if($allowed > 0) ({{ $count }}/{{ $allowed }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('invitar_rol') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button
                    wire:click="cerrarModal"
                    class="px-5 py-2 text-sm text-neutral-400 hover:text-white border border-[#2a2a2a] hover:border-[#444] rounded-lg transition-colors"
                >
                    Cancelar
                </button>
                <button
                    wire:click="invitar"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    class="flex items-center gap-2 bg-[#e85d27] hover:bg-[#d04e1f] active:scale-95 transition-all
                           text-white text-sm font-medium rounded-lg px-6 py-2"
                >
                    <span wire:loading.remove wire:target="invitar">Enviar Invitación</span>
                    <span wire:loading wire:target="invitar" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        Enviando...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
@if($mostrarModalEliminar)
<div class="fixed inset-0 bg-black/70 flex items-end sm:items-center justify-center z-50 px-4 pb-4 sm:pb-0">

    <div class="bg-[#141414] border border-[#222] rounded-2xl p-6 w-full max-w-sm">

        <h2 class="text-white font-semibold text-sm mb-2">
            Confirmar eliminación
        </h2>

        <p class="text-sm text-neutral-400 mb-5">
            ¿Seguro que quieres eliminar este usuario del proyecto?
        </p>

        <div class="flex justify-end gap-3">

            <button
                wire:click="cerrarModalEliminar"
                class="px-4 py-2 text-xs text-neutral-400 hover:text-white border border-[#2a2a2a] rounded-lg"
            >
                Cancelar
            </button>

            <button
                wire:click="eliminarUsuarioConfirmado"
                class="px-4 py-2 text-xs bg-red-600 hover:bg-red-500 text-white rounded-lg"
            >
                Eliminar
            </button>

        </div>

    </div>

</div>
@endif
</div>