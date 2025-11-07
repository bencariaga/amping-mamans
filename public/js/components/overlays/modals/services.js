(function () {
    if (!window.__lw_queue) window.__lw_queue = [];
    function flushQueue() {
        if (typeof Livewire === 'undefined' || !Livewire || typeof Livewire.emit !== 'function') return;
        while (window.__lw_queue.length) {
            var item = window.__lw_queue.shift();
            try { Livewire.emit.apply(Livewire, [item.name].concat(item.args)); } catch (e) { }
        }
    }
    document.addEventListener('livewire:load', function () { flushQueue(); }, { once: true });
    window.safeLivewireEmit = function (name) {
        var args = Array.prototype.slice.call(arguments, 1);
        if (typeof Livewire !== 'undefined' && Livewire && typeof Livewire.emit === 'function') {
            try { Livewire.emit.apply(Livewire, [name].concat(args)); return; } catch (e) { }
        }
        window.__lw_queue.push({ name: name, args: args });
    };
    window.openServicesModal = function () {
        var el = document.getElementById('services-modal-overlay');
        if (el) el.style.display = 'flex';
        window.safeLivewireEmit('loadServices');
        window.dispatchEvent(new Event('openServicesModal'));
    };
    window.closeServicesModal = function () {
        var el = document.getElementById('services-modal-overlay');
        if (el) el.style.display = 'none';
        window.dispatchEvent(new Event('closeServicesModal'));
    };
})();
