<div class="space-y-5 p-6 bg-gray-50 dark:bg-[#090909] text-black dark:text-white">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-black dark:text-white tracking-tight">Recursos Compartidos</h1>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-1">
                Recursos de los proyectos que te compartieron
            </p>
            @if($owners->count())
                <p class="text-[11px] text-blue-400 mt-1">
                    De:
                    @foreach($owners as $owner)
                        <span class="font-bold">{{ $owner->name }}</span>@if(!$loop->last), @endif
                    @endforeach
                </p>
            @endif
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="flex flex-wrap items-center gap-3">
        {{-- Búsqueda --}}
        <div class="relative flex-1 min-w-[200px]">
            <input wire:model.live.debounce.300ms="buscar"
                type="text"
                placeholder="Buscar recurso..."
                class="pl-9 pr-4 py-2 rounded-lg bg-gray-50 dark:bg-[#111] text-black dark:text-white border border-gray-300 dark:border-gray-800 focus:border-blue-500 focus:outline-none text-[13px] w-full shadow-sm transition-all">
            <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        {{-- Filtro tipo --}}
        <select wire:model.live="filtroTipo"
            class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-[#111] text-black dark:text-white border border-gray-300 dark:border-gray-800 focus:border-blue-500 focus:outline-none text-[13px] shadow-sm">
            <option value="">Todos los tipos</option>
            <option value="material">Material</option>
            <option value="labor">Mano de Obra</option>
            <option value="equipment">Equipo</option>
            <option value="composition">Composición</option>
        </select>

        {{-- Vista --}}
        <div class="flex bg-gray-100 dark:bg-[#111] p-1 rounded-lg border border-gray-300 dark:border-gray-800 w-fit shadow-sm">
            <button wire:click="$set('vista','grid')"
                class="px-4 py-1.5 rounded-md text-[11px] font-bold transition-all {{ $vista === 'grid' ? 'bg-gray-200 dark:bg-[#1a1a1a] text-black dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                GRID
            </button>
            <button wire:click="$set('vista','lista')"
                class="px-4 py-1.5 rounded-md text-[11px] font-bold transition-all {{ $vista === 'lista' ? 'bg-gray-200 dark:bg-[#1a1a1a] text-black dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                LISTA
            </button>
        </div>

        <span class="text-[11px] text-gray-500 dark:text-gray-600 font-mono ml-auto">{{ $total }} recurso{{ $total !== 1 ? 's' : '' }}</span>
    </div>

    {{-- CONTENIDO --}}
    <div class="bg-gray-50 dark:bg-[#0f0f0f] border border-gray-200 dark:border-gray-800/60 rounded-2xl overflow-hidden shadow-xl">

        @if($recursos->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <svg class="w-10 h-10 text-gray-400 dark:text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Sin recursos compartidos</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-600 mt-1">Los dueños de los proyectos compartidos no tienen recursos creados todavía.</p>
            </div>

        @elseif($vista === 'grid')
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recursos as $recurso)
                    @php
                        $config = match($recurso->tipo) {
                            'material'    => ['bg'=>'bg-blue-500/20',   'text'=>'text-blue-400',   'icon'=>'M'],
                            'labor'       => ['bg'=>'bg-green-500/20',  'text'=>'text-green-400',  'icon'=>'L'],
                            'equipment'   => ['bg'=>'bg-orange-500/20', 'text'=>'text-orange-400', 'icon'=>'E'],
                            'composition' => ['bg'=>'bg-purple-500/20', 'text'=>'text-purple-400', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>'],
                            default       => ['bg'=>'bg-gray-500/20',   'text'=>'text-gray-400',   'icon'=>'R']
                        };
                    @endphp

                    <div class="bg-white dark:bg-[#141414] border border-gray-300 dark:border-white/5 rounded-[24px] p-6 flex flex-col h-full relative shadow-sm">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-2xl {{ $config['bg'] }} {{ $config['text'] }} flex items-center justify-center">
                                {!! $recurso->tipo === 'composition' ? $config['icon'] : '<span class="font-bold">'.$config['icon'].'</span>' !!}
                            </div>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-black dark:text-white text-[15px] font-bold uppercase tracking-wide leading-tight">{{ $recurso->nombre }}</h3>
                            @if($recurso->codigo)
                                <span class="inline-block mt-1 text-[9px] font-mono font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-1.5 py-0.5 tracking-wider">{{ $recurso->codigo }}</span>
                            @endif
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-gray-500 dark:text-gray-400 text-[11px] uppercase font-bold">{{ $recurso->unidad }}</span>
                            </div>
                        </div>
                        <div class="mt-auto pt-4 border-t border-gray-200 dark:border-white/5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-600 uppercase font-bold mb-0.5">Precio</p>
                                    <p class="text-black dark:text-white font-bold text-[16px]">USD {{ number_format($recurso->precio_usd, 2, ',', '.') }}</p>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded {{ $config['bg'] }} {{ $config['text'] }} uppercase">
                                    {{ str_replace('_', ' ', $recurso->tipo) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            {{-- LISTA --}}
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-[#111] border-b border-gray-200 dark:border-gray-800/50">
                    <tr>
                        <th class="pl-6 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-left">Nombre</th>
                        <th class="px-3 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center">Tipo</th>
                        <th class="px-3 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center">Unidad</th>
                        <th class="pr-6 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-right">Precio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/40">
                    @foreach($recursos as $recurso)
                        <tr class="hover:bg-blue-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="pl-6 py-4">
                                <span class="text-black dark:text-white text-[13px] font-medium">{{ $recurso->nombre }}</span>
                                @if($recurso->codigo)
                                    <span class="ml-2 text-[9px] font-mono text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-1.5 py-0.5 tracking-wider">{{ $recurso->codigo }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-center">
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-gray-200 dark:bg-white/5 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-white/5 uppercase inline-block">
                                    {{ str_replace('_', ' ', $recurso->tipo) }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-gray-600 dark:text-gray-400 text-[12px] text-center font-medium">{{ $recurso->unidad }}</td>
                            <td class="pr-6 py-4 text-right text-black dark:text-white font-mono font-bold">
                                USD {{ number_format($recurso->precio_usd, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($hasMore)
            <div class="p-6 text-center border-t border-gray-200 dark:border-gray-800/40">
                <button wire:click="loadMore"
                    class="px-6 py-2 rounded-xl bg-gray-200 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white border border-gray-300 dark:border-gray-700 text-[12px] font-bold transition-all">
                    Cargar más
                </button>
            </div>
        @endif
    </div>
</div>
