<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

        <div class="bg-gradient-to-br from-[#1a1a1a] to-[#0f0f0f] border border-[#2a2a2a] rounded-2xl p-8 shadow-2xl">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#d15330]/20 border border-[#d15330]/40 rounded-full mb-4">
                    <svg class="w-8 h-8 text-[#d15330]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    Selecciona tu rol
                </h1>
                <p class="text-gray-400">
                    Para acceder al proyecto <strong>{{ $proyecto->nombre_proyecto }}</strong>
                </p>
            </div>

            {{-- Roles --}}
            <form method="POST" action="{{ route('invitacion.proyecto.confirmar', $token) }}" class="space-y-4">
                @csrf

                <div class="grid gap-4">

                    {{-- SUPERVISOR --}}
                    <label class="group relative">
                        <input 
                            type="radio" 
                            name="rol" 
                            value="supervisor" 
                            class="absolute opacity-0 peer"
                            required
                        />
                        <div class="p-4 rounded-xl border-2 border-gray-800 bg-[#111] cursor-pointer transition-all peer-checked:border-[#d15330] peer-checked:bg-[#d15330]/5 hover:border-[#d15330]/50">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 pt-1">
                                    <div class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-700 peer-checked:border-[#d15330] peer-checked:bg-[#d15330]"></div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-white text-left">Supervisor</h3>
                                    <p class="text-sm text-gray-400 text-left mt-1">
                                        Acceso completo: gestión de usuarios, configuración, estadísticas, reportes y control total del proyecto.
                                    </p>
                                    <div class="text-xs text-gray-500 mt-2 space-y-1 text-left">
                                        <p>✓ Invitar usuarios</p>
                                        <p>✓ Configurar proyecto</p>
                                        <p>✓ Ver estadísticas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

                    {{-- PRESUPUESTADOR --}}
                    <label class="group relative">
                        <input 
                            type="radio" 
                            name="rol" 
                            value="presupuestador" 
                            class="absolute opacity-0 peer"
                        />
                        <div class="p-4 rounded-xl border-2 border-gray-800 bg-[#111] cursor-pointer transition-all peer-checked:border-[#d15330] peer-checked:bg-[#d15330]/5 hover:border-[#d15330]/50">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 pt-1">
                                    <div class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-700 peer-checked:border-[#d15330] peer-checked:bg-[#d15330]"></div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-white text-left">Presupuestador</h3>
                                    <p class="text-sm text-gray-400 text-left mt-1">
                                        Acceso a presupuestación y recursos. Puedes crear y modificar el presupuesto del proyecto.
                                    </p>
                                    <div class="text-xs text-gray-500 mt-2 space-y-1 text-left">
                                        <p>✓ Crear rubros</p>
                                        <p>✓ Gestionar recursos</p>
                                        <p>✗ Configuración limitada</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

                    {{-- JEFE DE OBRA --}}
                    <label class="group relative">
                        <input 
                            type="radio" 
                            name="rol" 
                            value="jefe_obra" 
                            class="absolute opacity-0 peer"
                        />
                        <div class="p-4 rounded-xl border-2 border-gray-800 bg-[#111] cursor-pointer transition-all peer-checked:border-[#d15330] peer-checked:bg-[#d15330]/5 hover:border-[#d15330]/50">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 pt-1">
                                    <div class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-700 peer-checked:border-[#d15330] peer-checked:bg-[#d15330]"></div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-white text-left">Jefe de Obra</h3>
                                    <p class="text-sm text-gray-400 text-left mt-1">
                                        Acceso a seguimiento y control de ejecución. Registra avances y reportes de obra.
                                    </p>
                                    <div class="text-xs text-gray-500 mt-2 space-y-1 text-left">
                                        <p>✓ Ver estadísticas</p>
                                        <p>✓ Registrar avance</p>
                                        <p>✓ Reportes de bitácora</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

                </div>

                {{-- Botón --}}
                <div class="flex gap-3 mt-8 pt-4 border-t border-gray-800">
                    <a 
                        href="{{ route('dashboard') }}"
                        class="flex-1 px-4 py-3 rounded-lg bg-gray-800 hover:bg-gray-700 text-white text-sm font-bold transition-colors text-center">
                        Cancelar
                    </a>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-3 rounded-lg bg-[#d15330] hover:bg-[#c24820] text-white text-sm font-bold transition-colors">
                        Confirmar y Acceder
                    </button>
                </div>

            </form>

        </div>

    </div>
</x-app-layout>
