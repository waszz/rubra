<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-500/10 mb-4 border border-orange-500/20">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Recuperar Acceso</h2>
        <p class="mt-2 text-xs text-gray-500 font-bold uppercase tracking-widest">
            {{ __('Ingresa tu email para restablecer tu cuenta') }}
        </p>
    </div>

    <x-auth-session-status class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-xl text-xs font-bold text-center" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2 ml-1">Email de Registro</label>
            <input id="email" 
                   class="block w-full bg-[#111111] border-gray-800 text-white rounded-2xl py-4 px-5 focus:border-orange-500 focus:ring-orange-500/20 transition-all placeholder:text-gray-700" 
                   type="email" 
                   name="email" 
                   placeholder="ejemplo@rubra.com"
                   :value="old('email')" 
                   required 
                   autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-[10px] font-bold uppercase tracking-wider" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-white text-black py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-200 transition-all shadow-xl shadow-white/5 active:scale-[0.98]">
                {{ __('Enviar Instrucciones') }}
            </button>
        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-800/50">
            <a href="{{ route('login') }}" class="text-[10px] font-black text-gray-500 hover:text-white uppercase tracking-widest transition-colors">
                ← Volver al Login
            </a>

            <a href="{{ route('register') }}" class="text-[10px] font-black text-orange-500 hover:text-orange-400 uppercase tracking-widest transition-colors">
                Crear Cuenta
            </a>
        </div>
    </form>
</x-guest-layout>