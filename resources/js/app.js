// Selly Laundry — PWA bootstrap
// Livewire already bundles Alpine; no separate import needed.

// Register the service worker for offline shell + installability.
// Base path is injected via <meta name="app-base"> so it works in a sub-folder.
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        const base = document.querySelector('meta[name="app-base"]')?.content || '';
        navigator.serviceWorker.register(`${base}/sw.js`, { scope: `${base}/` }).catch((err) => {
            console.warn('SW registration failed:', err);
        });
    });
}

// Capture the install prompt so we can show a custom "Add to Home Screen" button.
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    window.dispatchEvent(new CustomEvent('pwa-installable'));
});

window.sellyInstall = async () => {
    if (!deferredPrompt) return false;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    deferredPrompt = null;
    return outcome === 'accepted';
};
