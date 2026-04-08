<div class="max-w-md mx-auto border border-white/10 rounded-2xl p-6 space-y-6 shadow-2xl">

    <h2 class="text-center text-white font-extrabold tracking-widest text-sm">
        EDITAR PROYECTO
    </h2>

    {{-- NOMBRE --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Nombre de la obra</label>
        <input type="text" wire:model="nombre_proyecto"
            placeholder="Ej: Edificio Residencial Los Olivos"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        @error('nombre_proyecto') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    {{-- DESCRIPCIÓN --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Descripción</label>
        <textarea wire:model="descripcion"
            placeholder="Detalles adicionales del proyecto..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 h-20 resize-none focus:border-white/30 focus:outline-none"></textarea>
    </div>

    {{-- NOTAS --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Notas / Recados</label>
        <textarea wire:model="notas"
            placeholder="Anotaciones internas, recordatorios..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 h-20 resize-none focus:border-white/30 focus:outline-none"></textarea>
    </div>

    {{-- MERCADO --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Mercado / Región</label>
        <select wire:model="mercado"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
            <option value="">Seleccionar Mercado</option>
            <option value="uy">Uruguay</option>
            <option value="ar">Argentina</option>
            <option value="br">Brasil</option>
        </select>
    </div>

    {{-- MONEDA + HORAS --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Moneda Base</label>
            <select wire:model="moneda_base"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
                <option value="USD">USD - Dólar Estadounidense</option>
                <option value="UYU">UYU - Peso Uruguayo</option>
                <option value="ARS">ARS - Peso Argentino</option>
                <option value="BRL">BRL - Real Brasileño</option>
            </select>
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Horas por Jornal</label>
            <input type="number" wire:model="horas_jornal" placeholder="8"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        </div>
    </div>

    {{-- IMPUESTOS + BENEFICIO --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Impuestos (%)</label>
            <input type="number" wire:model="impuestos" placeholder="0"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Beneficio (%)</label>
            <input type="number" wire:model="beneficio" placeholder="0"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        </div>
    </div>

    {{-- FECHA + M² --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Fecha de Inicio</label>
            <input type="date" wire:model="fecha_inicio"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">M² Totales</label>
            <input type="number" wire:model="metros_cuadrados" placeholder="0"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
        </div>
    </div>

    {{-- ESTADO --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Estado</label>
        <select wire:model="estado"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
            <option value="en_revision">En Revisión</option>
            <option value="ejecucion">Ejecución</option>
            <option value="activo">Activo</option>
            <option value="pausado">Pausado</option>
            <option value="finalizado">Finalizado</option>
        </select>
    </div>

    {{-- UBICACIÓN --}}
    <div>
        <label class="text-[10px] text-gray-500 uppercase flex items-center gap-1 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Ubicación del proyecto
        </label>
        <input type="text" wire:model="ubicacion"
            placeholder="Dirección o referencia..."
            class="w-full p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none text-sm">
    </div>

    {{-- CLIENTE --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Cliente</label>
        <input type="text" wire:model="cliente"
            placeholder="Nombre del cliente..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
    </div>

    {{-- BOTONES --}}
    <div class="flex gap-3 pt-2">
        <button type="button" wire:click="$parent.cerrarModalEditar"
            class="w-1/2 py-3 rounded-xl border border-white/10 text-white hover:bg-white/5 transition">
            Cancelar
        </button>
        <button type="button" wire:click="guardar"
            class="w-1/2 bg-white text-black py-3 rounded-xl font-bold hover:bg-gray-200 transition">
            Guardar Cambios
        </button>
    </div>

</div>