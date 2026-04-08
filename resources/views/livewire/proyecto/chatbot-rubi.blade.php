{{-- resources/views/livewire/proyecto/chatbot-rubi.blade.php --}}
<div>
    {{-- ── BOTÓN FLOTANTE ──────────────────────────────────────────────────── --}}
    <button
        wire:click="toggle"
        class="fixed bottom-6 right-6 z-[200] flex items-center gap-2 px-4 py-2.5
               bg-[#0d0d0d] border border-[#2a2a2a] hover:border-[#e85d27] rounded-full
               text-sm text-white shadow-2xl transition-all duration-200 group"
    >
        <div class="relative">
            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
        </div>
        <span class="font-semibold text-xs tracking-wide">Asistente Rubí</span>
        @if(!$abierto)
        <svg class="w-3.5 h-3.5 text-neutral-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
        @else
        <svg class="w-3.5 h-3.5 text-neutral-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        @endif
    </button>

    {{-- ── PANEL DEL CHAT ───────────────────────────────────────────────────── --}}
    @if($abierto)
    <div class="fixed bottom-20 right-6 z-[200] w-[380px] bg-[#0f0f0f] border border-[#222] rounded-2xl
                shadow-2xl flex flex-col overflow-hidden"
         style="height: 520px;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#1e1e1e] bg-[#111]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-[#e85d27]/20 border border-[#e85d27]/40
                            flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#e85d27]" viewBox="0 0 24 24">
                        <text x="12" y="18" font-size="18" font-weight="bold" text-anchor="middle" fill="currentColor">R</text>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-white">RUBÍ — ASISTENTE</p>
                    <p class="text-[10px] text-emerald-400">● EN LÍNEA</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                {{-- Limpiar chat --}}
                <button
                    wire:click="$set('mensajes', [{'role': 'assistant', 'content': '¡Hola! Soy Rubí, tu asistente de RUBRA. ¿En qué puedo ayudarte con tu proyecto hoy?'}])"
                    class="p-1.5 text-neutral-600 hover:text-white rounded-lg hover:bg-white/5 transition-colors"
                    title="Limpiar chat">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                {{-- Cerrar --}}
                <button
                    wire:click="toggle"
                    class="p-1.5 text-neutral-600 hover:text-white rounded-lg hover:bg-white/5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mensajes --}}
        <div
            id="chat-messages"
            class="flex-1 overflow-y-auto px-4 py-3 space-y-3"
            x-data
            x-on:scroll-chat.window="$el.scrollTop = $el.scrollHeight"
        >
            @foreach($mensajes as $msg)
                @if($msg['role'] === 'user')
                    {{-- Mensaje del usuario --}}
                    <div class="flex justify-end">
                        <div class="max-w-[75%] bg-[#e85d27] text-white text-xs rounded-2xl rounded-tr-sm px-3.5 py-2.5 leading-relaxed">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @else
                    {{-- Mensaje del asistente --}}
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-[#e85d27]/20 border border-[#e85d27]/30
                                    flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-[#e85d27]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="max-w-[80%] bg-[#1a1a1a] border border-[#2a2a2a] text-neutral-300
                                    text-xs rounded-2xl rounded-tl-sm px-3.5 py-2.5 leading-relaxed">
                            {!! nl2br(e($msg['content'])) !!}
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Typing indicator --}}
            @if($cargando)
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full bg-[#e85d27]/20 border border-[#e85d27]/30
                            flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-[#e85d27]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-2xl rounded-tl-sm px-4 py-3">
                    <div class="flex gap-1 items-center">
                        <div class="w-1.5 h-1.5 rounded-full bg-neutral-500 animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-neutral-500 animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-neutral-500 animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sugerencias rápidas --}}
        @if(count($mensajes) <= 1)
        <div class="px-4 pb-2 flex flex-wrap gap-1.5">
            @foreach([
                'Presupuestar obra nueva',
                'Ver resumen del proyecto',
                'Comparar variantes constructivas',
                'Listar materiales para cotizar',
            ] as $sugerencia)
            <button
                wire:click="enviar('{{ $sugerencia }}')"
                class="text-[10px] px-3 py-1.5 bg-[#1a1a1a] border border-[#2a2a2a] hover:border-[#e85d27]
                       text-neutral-400 hover:text-white rounded-full transition-colors">
                {{ $sugerencia }}
            </button>
            @endforeach
        </div>
        @endif

        {{-- Input --}}
        <div class="px-3 pb-3 pt-2 border-t border-[#1e1e1e]">
            <div class="flex items-end gap-2 bg-[#1a1a1a] border border-[#2a2a2a] focus-within:border-[#e85d27]
                        rounded-xl px-3 py-2 transition-colors">
                <textarea
                    wire:model="input"
                    wire:keydown.enter.prevent="enviar"
                    placeholder="Escribe tu consulta..."
                    rows="1"
                    class="flex-1 bg-transparent text-xs text-neutral-200 placeholder-neutral-600
                           outline-none resize-none leading-relaxed max-h-24"
                    style="min-height: 20px;"
                    x-data
                    x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                ></textarea>
                <button
                    wire:click="enviar"
                    wire:loading.attr="disabled"
                    class="shrink-0 w-7 h-7 bg-[#e85d27] hover:bg-[#d04e1f] disabled:opacity-50
                           rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l-3 9 18-9-18-9-3 9zm0 0h8"/>
                    </svg>
                </button>
            </div>
            <p class="text-[9px] text-neutral-700 text-center mt-1.5">Potenciado por Groq AI</p>
        </div>

    </div>
    @endif
</div>