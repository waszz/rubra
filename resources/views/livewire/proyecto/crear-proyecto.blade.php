<div class="max-w-md mx-auto border border-white/10 rounded-2xl p-6 space-y-6 shadow-2xl">

    <h2 class="text-center text-white font-extrabold tracking-widest text-sm">
        CREAR NUEVO PROYECTO
    </h2>

    @php
        $user = auth()->user();
        $limitesPlan = ['gratis' => 3, 'basico' => 10, 'profesional' => 25, 'enterprise' => 100];
        $limitePlan = $limitesPlan[$user->plan] ?? 1;
        $proyectosUsados = \App\Models\Proyecto::where('user_id', $user->id)->count();
        $proyectosRestantes = max(0, $limitePlan - $proyectosUsados);
    @endphp

    {{-- AVISO DE LÍMITE --}}
    @if($proyectosRestantes === 0)
        <div class="flex items-start gap-3 bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3">
            <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="text-xs text-red-400 font-bold">Límite alcanzado</p>
                <p class="text-[11px] text-red-400/80 mt-0.5">Tu plan <strong>{{ $user->planLabel() }}</strong> permite hasta {{ $limitePlan }} proyecto(s).
                    <a href="{{ url('/#precios') }}" class="underline hover:text-red-300">Mejorá tu plan</a> para crear más.
                </p>
            </div>
        </div>
    @else
        <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-4 py-2.5">
            <span class="text-[11px] text-gray-400">{{ $user->planLabel() }}</span>
            <span class="text-[11px] font-bold {{ $proyectosRestantes <= 1 ? 'text-amber-400' : 'text-emerald-400' }}">
                {{ $proyectosUsados }}/{{ $limitePlan }} proyectos usados
            </span>
        </div>
    @endif

    {{-- NOMBRE --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">
            Nombre de la obra
        </label>
        <input 
            type="text" 
            wire:model="nombre_proyecto"
            placeholder="Ej: Edificio Residencial Los Olivos"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
        >
        @error('nombre_proyecto') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    {{-- DESCRIPCIÓN --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Descripción</label>
        <textarea 
            wire:model="descripcion"
            placeholder="Detalles adicionales del proyecto..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 h-20 resize-none focus:border-white/30 focus:outline-none"
        ></textarea>
    </div>

    {{-- NOTAS / RECADOS --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Notas / Recados</label>
        <textarea 
            wire:model="notas"
            placeholder="Anotaciones internas, recordatorios..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 h-20 resize-none focus:border-white/30 focus:outline-none"
        ></textarea>
    </div>

    {{-- MERCADO / REGIÓN --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Mercado / Región</label>
        <select 
            wire:model="mercado"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
        >
            <option value="">Seleccionar Mercado</option>
            <option value="uy">Uruguay</option>
            <option value="ar">Argentina</option>
            <option value="br">Brasil</option>
        </select>
    </div>

    {{-- MONEDA BASE + HORAS POR JORNAL --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Moneda Base</label>
            <select 
                wire:model="moneda_base"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
                <option value="USD">USD - Dólar Estadounidense</option>
                <option value="UYU">UYU - Peso Uruguayo</option>
                <option value="ARS">ARS - Peso Argentino</option>
                <option value="BRL">BRL - Real Brasileño</option>
            </select>
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Horas por Jornal</label>
            <input 
                type="number" 
                wire:model="horas_jornal"
                placeholder="8"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
        </div>
    </div>

    {{-- IMPUESTOS + BENEFICIO --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Impuestos (%)</label>
            <input 
                type="number" 
                wire:model="impuestos"
                placeholder="0"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Beneficio (%)</label>
            <input 
                type="number" 
                wire:model="beneficio"
                placeholder="0"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
        </div>
    </div>

    {{-- FECHA INICIO + M² TOTALES --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Fecha de Inicio</label>
            <input 
                type="date" 
                wire:model="fecha_inicio"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">M² Totales</label>
            <input 
                type="number" 
                wire:model="metros_cuadrados"
                placeholder="0"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
        </div>
    </div>

    {{-- ESTADO --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Estado</label>
        <select 
            wire:model="estado"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
        >
            <option value="en_revision">En Revisión</option>
            <option value="ejecucion">Ejecución</option>
            <option value="activo">Activo</option>
            <option value="pausado">Pausado</option>
            <option value="finalizado">Finalizado</option>
        </select>
    </div>

    {{-- UBICACIÓN + MAPA --}}
    <div>
        <label class="text-[10px] text-gray-500 uppercase flex items-center gap-1 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Haz clic en el mapa para ubicar el proyecto
        </label>

        {{-- Coordenadas ocultas sincronizadas con Livewire --}}
        <input type="hidden" wire:model="ubicacion_lat" id="ubicacion_lat">
        <input type="hidden" wire:model="ubicacion_lng" id="ubicacion_lng">

        {{-- Dirección legible (opcional) --}}
        <input 
            type="text" 
            wire:model="ubicacion"
            id="ubicacion_texto"
            placeholder="Dirección o referencia..."
            class="w-full mb-2 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none text-sm"
        >

        {{-- Mapa Leaflet --}}
       <div 
    id="mapa-proyecto" 
    class="w-full h-[220px] rounded-xl overflow-hidden border border-white/10"
    wire:ignore
></div>
    </div>

    {{-- PLANTILLA BASE --}}
{{-- PLANTILLA BASE --}}
<div>
    <label class="text-[10px] tracking-widest text-gray-500 uppercase">Plantilla de Recursos</label>
    <select 
        wire:model.live="plantilla_base"
        class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
    >
        <option value="en_blanco">PROYECTO EN BLANCO</option>
        <optgroup label="Construcción General" class="bg-[#0f1115]">
            <option value="obra_nueva_vivienda">Obra Nueva (Vivienda)</option>
            <option value="reforma_integral">Reforma Integral</option>
            <option value="steel_frame">Steel Frame</option>
        </optgroup>
        <optgroup label="Especialidades" class="bg-[#0f1115]">
            <option value="instalacion_electrica">Instalación Eléctrica</option>
            <option value="instalacion_sanitaria">Instalación Sanitaria</option>
            <option value="pintura">Pintura y Terminaciones</option>
        </optgroup>
        <optgroup label="Otros" class="bg-[#0f1115]">
            <option value="piscina">Piscina</option>
            <option value="quincho">Quincho / Parrilla</option>
        </optgroup>
    </select>
</div>
{{-- INVITAR USUARIOS --}}
<div>
    <label class="text-[10px] tracking-widest text-gray-500 uppercase">
        Invitar usuarios
    </label>

    <div class="mt-2 max-h-32 overflow-y-auto space-y-1">
        @foreach(\App\Models\User::where('invited_by', auth()->id())->get() as $u)
            <label class="flex items-center gap-2 text-sm text-gray-300">
                <input 
                    type="checkbox"
                    value="{{ $u->id }}"
                    wire:model="usuariosSeleccionados"
                    class="rounded"
                >
                {{ $u->name }}
            </label>
        @endforeach
    </div>
</div>
    {{-- BOTONES --}}
    <div class="flex gap-3 pt-2">
        <button 
            type="button"
            wire:click="$parent.cerrarModal"
            class="w-1/2 py-3 rounded-xl border border-white/10 text-white hover:bg-white/5 transition"
        >
            Cancelar
        </button>
        <button 
            type="button"
            wire:click="guardar"
            class="w-1/2 bg-white text-black py-3 rounded-xl font-bold hover:bg-gray-200 transition"
        >
            Crear Proyecto
        </button>
    </div>
</div>



