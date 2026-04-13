<div class="max-w-md mx-auto border border-white/10 rounded-2xl p-6 space-y-6 shadow-2xl">

    <h2 class="text-center text-white font-extrabold tracking-widest text-sm uppercase">
        Nuevo Recurso
    </h2>

    {{-- NOMBRE --}}
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Nombre</label>
        <input
            type="text"
            wire:model="nombre"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
        >
        @error('nombre') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- CÓDIGO DE VENTA --}}
    @if ($tipo !== 'labor')
    <div>
        <label class="text-[10px] tracking-widest text-gray-500 uppercase">Código de Venta</label>
        <input
            type="text"
            wire:model="codigo"
            placeholder="Ej: MAT-001, SKU-123..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
        >
    </div>
    @endif

    {{-- TIPO + UNIDAD --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Tipo</label>
            <select wire:model.live="tipo" class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
                <option value="material">Material</option>
                <option value="labor">Mano de Obra</option>
                <option value="equipment">Equipo/Herramienta</option>
            </select>
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Unidad</label>
            <select wire:model="unidad" class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
                <option value="un">und (Unidad)</option>
                <option value="m">m (Metro)</option>
                <option value="m2">m² (Metro cuadrado)</option>
                <option value="m3">m³ (Metro cúbico)</option>
                <option value="kg">kg (Kilogramo)</option>
                <option value="l">l (Litro)</option>
                <option value="h">h (Hora)</option>
                <option value="d">d (Día)</option>
                <option value="p2">p² (Pie cuadrado)</option>
                <option value="ml">ml (Metro lineal)</option>
                <option value="mes">mes</option>
            </select>
        </div>
    </div>

    {{-- PRECIO + MONEDA --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Precio Unitario</label>
            <input
                type="number"
                wire:model="precio_usd"
                step="0.001"
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
            @error('precio_usd') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Moneda</label>
            <select wire:model="moneda" class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
                <option value="USD">USD - Dólar Estadounidense</option>
                <option value="UYU">UYU - Peso Uruguayo</option>
                <option value="ARS">ARS - Peso Argentino</option>
                <option value="BRL">BRL - Real Brasileño</option>
            </select>
        </div>
    </div>

    {{-- CARGA SOCIAL (Solo para mano de obra) --}}
    @if ($tipo === 'labor')
    <div>
        <label class="text-[10px] text-gray-500 uppercase">Carga Social (%)</label>
        <input
            type="number"
            wire:model="social_charges_percentage"
            min="0"
            max="100"
            step="0.01"
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            placeholder="Ej: 72"
        >
        <p class="text-[9px] text-gray-400 mt-1">Porcentaje de carga social sobre el costo de mano de obra (ej: 72%)</p>
        @error('social_charges_percentage') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>
    @endif

    {{-- REGIÓN + VENDEDOR --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Región de Referencia</label>
            <select wire:model="region" class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none">
                <option value="Uruguay">Uruguay</option>
                <option value="Argentina">Argentina</option>
                <option value="Brasil">Brasil</option>
                <option value="Paraguay">Paraguay</option>
            </select>
        </div>
        @if ($tipo !== 'labor')
        <div>
            <label class="text-[10px] text-gray-500 uppercase">Vendedor Referente</label>
            <input
                type="text"
                wire:model="vendedor"
                placeholder="Ej: Home Depot, Sodimac..."
                class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
            >
        </div>
        @endif
    </div>

    {{-- PRECIO ESTIMATIVO --}}
    <div class="flex items-center gap-3">
        <input
            type="checkbox"
            wire:model="precio_estimativo"
            id="precio_estimativo"
            class="w-4 h-4 rounded bg-[#0f1115] border border-white/20 accent-orange-500"
        >
        <label for="precio_estimativo" class="text-sm text-gray-400 cursor-pointer">Precio Estimativo</label>
    </div>

    {{-- MARCA / MODELO --}}
    @if ($tipo !== 'labor')
    <div>
        <label class="text-[10px] text-gray-500 uppercase">Marca / Modelo</label>
        <input
            type="text"
            wire:model="marca_modelo"
            placeholder="Ej: Makita, Bosch, etc..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 focus:border-white/30 focus:outline-none"
        >
    </div>
    @endif

    {{-- OBSERVACIONES --}}
    <div>
        <label class="text-[10px] text-gray-500 uppercase">Observaciones</label>
        <textarea
            wire:model="observaciones"
            placeholder="Detalles adicionales..."
            class="w-full mt-1 p-3 rounded-xl bg-[#0f1115] text-white border border-white/10 h-24 resize-none focus:border-white/30 focus:outline-none"
        ></textarea>
    </div>

    {{-- BOTONES --}}
    <div class="flex gap-3 pt-2">
        <button
    type="button"
    wire:click="cancelar"
    class="w-1/2 py-3 rounded-xl border border-white/10 text-white hover:bg-white/5 transition"
>
    Cancelar
</button>
        <button
            type="button"
            wire:click="guardar"
            class="w-1/2 bg-white text-black py-3 rounded-xl font-bold hover:bg-gray-200 transition"
        >
            Crear Recurso
        </button>
    </div>

</div>