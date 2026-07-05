{{-- Global toast listener. Dispatch with: $this->dispatch('toast', message: '...', type: 'success'|'error'|'info') --}}
<div x-data="{ show: false, message: '', type: 'success' }"
     x-on:toast.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; clearTimeout($refs.t); $refs.t = setTimeout(() => show = false, 3000)"
     x-cloak>
    <div x-show="show" x-transition
         class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium max-w-[90%] text-center"
         :class="{ 'bg-selly-success': type==='success', 'bg-selly-danger': type==='error', 'bg-selly-primary-dark': type==='info' }"
         x-text="message">
    </div>
</div>

<style>[x-cloak]{display:none!important}</style>
