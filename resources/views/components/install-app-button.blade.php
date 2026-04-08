{{-- Botón para instalar PWA - Solo visible para planes profesional y enterprise --}}
@auth
@if(auth()->user()->canDownloadApp())
<button 
    id="installAppBtn"
    class="hidden px-3 py-2 rounded-lg bg-[#d15330] hover:bg-[#c24820] text-white text-xs font-semibold transition-colors flex items-center gap-2 w-full justify-center"
    title="Instalar RUBRA como aplicación">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
    </svg>
    <span>Instalar App</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deferredPrompt;
    const installBtn = document.getElementById('installAppBtn');

    if (!installBtn) return;

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing
        e.preventDefault();
        // Stash the event for later use.
        deferredPrompt = e;
        // Update UI to show the install button.
        installBtn.classList.remove('hidden');
    });

    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) {
            return;
        }
        // Show the install prompt.
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response to the install prompt: ${outcome}`);
        // We've used the prompt, and can't use it again, throw it away
        deferredPrompt = null;
        // Hide the install button.
        installBtn.classList.add('hidden');
    });

    window.addEventListener('appinstalled', () => {
        console.log('RUBRA app instalada exitosamente');
        installBtn.classList.add('hidden');
        deferredPrompt = null;
    });
});
</script>
@endif
@endauth
