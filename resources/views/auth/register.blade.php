<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-3xl font-black uppercase tracking-tighter text-white leading-none">
            Unirse a <span class="text-[#d15330]">RUBRA</span>
        </h2>
        <p class="text-[10px] uppercase tracking-[0.2em] text-white/40 mt-2 font-bold">Crea tu cuenta profesional</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate class="space-y-5">
        @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">
            <input type="hidden" name="proyecto_token" value="{{ $proyectoToken ?? request('proyecto_token') }}">
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" class="text-[10px] uppercase tracking-widest text-white/60 font-bold mb-1" />
            <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" 
                class="block w-full bg-white/5 border-white/10 text-white focus:border-[#d15330] focus:ring-[#d15330] rounded-xl text-sm" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-[10px] uppercase font-bold text-red-500" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Corporativo')" class="text-[10px] uppercase tracking-widest text-white/60 font-bold mb-1" />
            <x-text-input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" 
                class="block w-full bg-white/5 border-white/10 text-white focus:border-[#d15330] focus:ring-[#d15330] rounded-xl text-sm" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-[10px] uppercase font-bold text-red-500" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" :value="__('Contraseña')" class="text-[10px] uppercase tracking-widest text-white/60 font-bold mb-1" />
                <x-text-input id="password" name="password" type="password" required autocomplete="new-password" 
                    class="block w-full bg-white/5 border-white/10 text-white focus:border-[#d15330] focus:ring-[#d15330] rounded-xl text-sm" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar')" class="text-[10px] uppercase tracking-widest text-white/60 font-bold mb-1" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" 
                    class="block w-full bg-white/5 border-white/10 text-white focus:border-[#d15330] focus:ring-[#d15330] rounded-xl text-sm" />
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px] uppercase font-bold text-red-500" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[10px] uppercase font-bold text-red-500" />

        <div class="pt-4 space-y-4">
            <button type="submit" class="w-full bg-[#d15330] text-white py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:brightness-110 transition-all shadow-lg shadow-[#d15330]/10">
                {{ __('Registrarse') }}
            </button>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-white/5"></div>
                <span class="flex-shrink mx-4 text-[9px] uppercase tracking-[0.3em] text-white/20 font-bold">O regístrate con</span>
                <div class="flex-grow border-t border-white/5"></div>
            </div>

            <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center gap-3 bg-white/5 border border-white/10 text-white py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white/10 transition-all">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                Google Account
            </a>
        </div>

        <div class="flex justify-center mt-10">
            <a href="{{ route('login') }}" class="text-[9px] uppercase tracking-widest text-white/30 hover:text-white transition-colors font-bold">
                ¿Ya tienes cuenta? <span class="text-[#d15330]">Inicia Sesión</span>
            </a>
        </div>
    </form>
</x-guest-layout>