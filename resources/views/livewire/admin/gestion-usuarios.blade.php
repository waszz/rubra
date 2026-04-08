<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase mb-1">Gestión de Usuarios</h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Panel de administración de cuentas</p>
        </div>
        <a href="{{ route('panel') }}"
           class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors border border-[#2a2a2a] px-4 py-2 rounded-lg">
            ← Volver al Panel
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Activos</p>
            <p class="text-3xl font-black text-green-400">{{ $stats['activos'] }}</p>
        </div>
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Eliminados</p>
            <p class="text-3xl font-black text-red-400">{{ $stats['eliminados'] }}</p>
        </div>
        <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Total</p>
            <p class="text-3xl font-black text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-[#111] border border-purple-500/30 rounded-xl p-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-purple-400 mb-1">God</p>
            <p class="text-3xl font-black text-purple-400">{{ $stats['god'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-[#111] border border-[#2a2a2a] rounded-xl p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            {{-- Búsqueda --}}
            <div class="flex-1 min-w-[180px]">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar nombre o email..."
                    class="w-full px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs placeholder-gray-600 focus:border-purple-500/50 focus:outline-none transition-colors">
            </div>

            {{-- Plan --}}
            <select wire:model.live="filterPlan"
                class="px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-purple-500/50 focus:outline-none transition-colors">
                <option value="">Todos los planes</option>
                <option value="gratis">Gratis</option>
                <option value="basico">Básico</option>
                <option value="profesional">Profesional</option>
                <option value="enterprise">Enterprise</option>
            </select>

            {{-- Rol --}}
            <select wire:model.live="filterRol"
                class="px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-purple-500/50 focus:outline-none transition-colors">
                <option value="">Todos los roles</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="god">God</option>
                <option value="supervisor">Supervisor</option>
            </select>

            {{-- Estado --}}
            <div class="flex gap-1 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg p-1">
                @foreach(['todos' => 'Todos', 'activos' => 'Activos', 'eliminados' => 'Eliminados'] as $val => $lbl)
                    <button wire:click="$set('filterEstado', '{{ $val }}')"
                        class="px-3 py-1.5 rounded text-[10px] font-bold uppercase tracking-widest transition-colors {{ $filterEstado === $val ? 'bg-purple-600 text-white' : 'text-gray-500 hover:text-white' }}">
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Flash mensaje --}}
    @if($mensaje)
        <div class="mb-4 px-4 py-3 rounded-lg text-xs font-bold uppercase tracking-widest
            {{ $mensajeTipo === 'error' ? 'bg-red-500/10 border border-red-500/30 text-red-400' : 'bg-green-500/10 border border-green-500/30 text-green-400' }}">
            {{ $mensaje }}
        </div>
    @endif

    {{-- Tabla de usuarios --}}
    <div class="bg-[#111] border border-[#2a2a2a] rounded-xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-[#2a2a2a]">
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-widest text-gray-500">Usuario</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-widest text-gray-500">Plan</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-widest text-gray-500">Rol</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-widest text-gray-500">Trial</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-widest text-gray-500">Registro</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-widest text-gray-500">Estado</th>
                        <th class="px-4 py-3 text-right font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a1a1a]">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-white/[0.02] transition-colors {{ $usuario->trashed() ? 'opacity-50' : '' }}">
                            {{-- Usuario --}}
                            <td class="px-4 py-3">
                                <p class="font-semibold text-white">{{ $usuario->name }}</p>
                                <p class="text-gray-500">{{ $usuario->email }}</p>
                            </td>

                            {{-- Plan --}}
                            <td class="px-4 py-3">
                                @php
                                    $planColors = [
                                        'gratis'       => 'text-gray-400',
                                        'basico'       => 'text-blue-400',
                                        'profesional'  => 'text-purple-400',
                                        'enterprise'   => 'text-orange-400',
                                    ];
                                @endphp
                                <span class="font-bold {{ $planColors[$usuario->plan] ?? 'text-white' }}">
                                    {{ $usuario->planLabel() }}
                                </span>
                            </td>

                            {{-- Rol --}}
                            <td class="px-4 py-3">
                                @php
                                    $rolColors = ['god' => 'bg-purple-500/20 text-purple-300', 'admin' => 'bg-blue-500/20 text-blue-300', 'supervisor' => 'bg-yellow-500/20 text-yellow-300', 'user' => 'bg-gray-500/20 text-gray-300'];
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rolColors[$usuario->role] ?? 'bg-gray-500/20 text-gray-300' }}">
                                    {{ $usuario->role }}
                                </span>
                            </td>

                            {{-- Trial --}}
                            <td class="px-4 py-3 text-gray-400">
                                @if($usuario->isGod())
                                    <span class="text-purple-400 font-bold">∞</span>
                                @elseif($usuario->plan !== 'gratis')
                                    <span class="text-green-400">Pagado</span>
                                @elseif($usuario->trialExpired())
                                    <span class="text-red-400">Expirado</span>
                                @else
                                    {{ $usuario->trialDaysLeft() }}d restantes
                                @endif
                            </td>

                            {{-- Registro --}}
                            <td class="px-4 py-3 text-gray-500">
                                {{ $usuario->created_at->format('d/m/Y') }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-3">
                                @if($usuario->trashed())
                                    <span class="text-red-400 font-bold">Eliminado</span>
                                @else
                                    <span class="text-green-400 font-bold">Activo</span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Editar --}}
                                    <button wire:click="abrirEditar({{ $usuario->id }})"
                                        title="Editar"
                                        class="p-1.5 rounded bg-[#1a1a1a] hover:bg-purple-600/20 text-gray-400 hover:text-purple-300 transition-colors border border-[#2a2a2a] hover:border-purple-500/40">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Proyectos --}}
                                    <button wire:click="abrirProyectos({{ $usuario->id }})"
                                        title="Ver proyectos"
                                        class="p-1.5 rounded bg-[#1a1a1a] hover:bg-blue-600/20 text-gray-400 hover:text-blue-300 transition-colors border border-[#2a2a2a] hover:border-blue-500/40">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                    </button>

                                    {{-- Impersonar --}}
                                    @if(!$usuario->trashed())
                                        <button wire:click="impersonarUsuario({{ $usuario->id }})"
                                            title="Entrar como este usuario"
                                            class="p-1.5 rounded bg-[#1a1a1a] hover:bg-green-600/20 text-gray-400 hover:text-green-300 transition-colors border border-[#2a2a2a] hover:border-green-500/40">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Eliminar / Restaurar --}}
                                    @if($usuario->trashed())
                                        <button wire:click="restaurarUsuario({{ $usuario->id }})"
                                            wire:confirm="¿Restaurar este usuario?"
                                            title="Restaurar"
                                            class="p-1.5 rounded bg-[#1a1a1a] hover:bg-green-600/20 text-gray-400 hover:text-green-300 transition-colors border border-[#2a2a2a] hover:border-green-500/40">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    @elseif(!$usuario->isGod())
                                        <button wire:click="eliminarUsuario({{ $usuario->id }})"
                                            wire:confirm="¿Eliminar la cuenta de {{ $usuario->name }}?"
                                            title="Eliminar"
                                            class="p-1.5 rounded bg-[#1a1a1a] hover:bg-red-600/20 text-gray-400 hover:text-red-300 transition-colors border border-[#2a2a2a] hover:border-red-500/40">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-600 text-xs uppercase tracking-widest font-bold">
                                No se encontraron usuarios con los filtros aplicados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="text-gray-500">
        {{ $usuarios->links() }}
    </div>


    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL EDITAR USUARIO --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4"
             style="background:rgba(0,0,0,0.85)">
            {{-- Click fuera para cerrar --}}
            <div class="absolute inset-0" wire:click="cerrarEditar"></div>

            <div class="relative bg-[#111] border border-[#2a2a2a] rounded-2xl w-full max-w-lg shadow-2xl z-10 overflow-y-auto max-h-[90vh]">

                {{-- Header modal --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-[#2a2a2a]">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-white">Editar Usuario</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">ID #{{ $editId }}</p>
                    </div>
                    <button wire:click="cerrarEditar" class="text-gray-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6 space-y-6">

                    {{-- Flash inside modal --}}
                    @if($mensaje)
                        <div class="px-4 py-3 rounded-lg text-xs font-bold uppercase tracking-widest
                            {{ $mensajeTipo === 'error' ? 'bg-red-500/10 border border-red-500/30 text-red-400' : 'bg-green-500/10 border border-green-500/30 text-green-400' }}">
                            {{ $mensaje }}
                        </div>
                    @endif

                    {{-- ── Datos básicos ── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-purple-400 mb-3">Datos Básicos</p>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold block mb-1">Nombre</label>
                                <input type="text" wire:model="editNombre"
                                    class="w-full px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-purple-500/50 focus:outline-none transition-colors">
                                @error('editNombre') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold block mb-1">Email</label>
                                <input type="email" wire:model="editEmail"
                                    class="w-full px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-purple-500/50 focus:outline-none transition-colors">
                                @error('editEmail') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold block mb-1">Plan</label>
                                    <select wire:model="editPlan"
                                        class="w-full px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-purple-500/50 focus:outline-none transition-colors">
                                        <option value="gratis">Gratis</option>
                                        <option value="basico">Básico</option>
                                        <option value="profesional">Profesional</option>
                                        <option value="enterprise">Enterprise</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold block mb-1">Rol</label>
                                    <select wire:model="editRol"
                                        class="w-full px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-purple-500/50 focus:outline-none transition-colors">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                        <option value="god">God</option>
                                        <option value="supervisor">Supervisor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button wire:click="guardarUsuario"
                                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-colors">
                                Guardar Cambios
                            </button>
                            <button wire:click="forzarPlan"
                                wire:confirm="¿Forzar el plan seleccionado sin pago?"
                                class="px-4 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-orange-500/40 text-orange-400 hover:bg-orange-500/10 transition-colors">
                                Forzar Plan
                            </button>
                        </div>
                    </div>

                    {{-- ── Cambiar contraseña ── --}}
                    <div class="border-t border-[#2a2a2a] pt-5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-3">Cambiar Contraseña</p>
                        <input type="password" wire:model="editPassword"
                            placeholder="Nueva contraseña (mín. 8 caracteres)"
                            class="w-full px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs placeholder-gray-600 focus:border-blue-500/50 focus:outline-none transition-colors mb-3">
                        @error('editPassword') <p class="text-red-400 text-[10px] mb-2">{{ $message }}</p> @enderror
                        <button wire:click="cambiarContrasena"
                            class="w-full bg-blue-600/20 border border-blue-500/30 hover:bg-blue-600/30 text-blue-300 px-4 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-colors">
                            Actualizar Contraseña
                        </button>
                    </div>

                    {{-- ── Extender trial ── --}}
                    <div class="border-t border-[#2a2a2a] pt-5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-green-400 mb-3">Extender Trial</p>
                        <div class="flex gap-3">
                            <div class="flex items-center gap-2 flex-1">
                                <input type="number" wire:model="extensionDias" min="1" max="365"
                                    class="w-24 px-3 py-2 bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg text-white text-xs focus:border-green-500/50 focus:outline-none transition-colors text-center">
                                <span class="text-xs text-gray-500">días</span>
                            </div>
                            <button wire:click="extenderTrial"
                                class="px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest bg-green-600/20 border border-green-500/30 hover:bg-green-600/30 text-green-300 transition-colors">
                                Extender
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL PROYECTOS --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($modalProyectos)
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4"
             style="background:rgba(0,0,0,0.85)">
            <div class="absolute inset-0" wire:click="cerrarProyectos"></div>

            <div class="relative bg-[#111] border border-[#2a2a2a] rounded-2xl w-full max-w-lg shadow-2xl z-10 overflow-y-auto max-h-[80vh]">
                <div class="flex items-center justify-between px-6 py-5 border-b border-[#2a2a2a]">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-white">Proyectos</h2>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $nombreUsuarioProyectos }}</p>
                    </div>
                    <button wire:click="cerrarProyectos" class="text-gray-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4">
                    @if(count($proyectosUsuario) === 0)
                        <p class="text-center text-gray-600 text-xs font-bold uppercase tracking-widest py-8">
                            Sin proyectos registrados
                        </p>
                    @else
                        <div class="space-y-2">
                            @foreach($proyectosUsuario as $p)
                                <a href="{{ route('proyectos.presupuesto', $p['id']) }}"
                                   class="block bg-[#0a0a0a] border border-[#2a2a2a] rounded-lg p-4 hover:border-purple-500/40 hover:bg-purple-500/5 transition-all group">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white text-xs font-bold mb-1 group-hover:text-purple-300 transition-colors truncate">{{ $p['nombre'] }}</p>
                                            <p class="text-gray-500 text-[10px]">Cliente: {{ $p['cliente'] }}</p>
                                            @if($p['fecha'] !== '-')
                                                <p class="text-gray-500 text-[10px]">Inicio: {{ $p['fecha'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded
                                                {{ $p['estado'] === 'en_curso' ? 'bg-green-500/20 text-green-400' : ($p['estado'] === 'finalizado' ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-500/20 text-gray-400') }}">
                                                {{ str_replace('_', ' ', $p['estado']) }}
                                            </span>
                                            <svg class="w-3.5 h-3.5 text-gray-600 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
