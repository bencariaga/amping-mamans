(function () {
    function init() {
        window.openCreateModal = function () {
            if (window.Livewire && typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('openCreateModal');
            }
        };
        window.openEditModal = function (tariffListId) {
            if (window.Livewire && typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('openEditModal', { tariffListId: tariffListId });
            }
        };
        window.openDeleteModal = function (tariffListId) {
            if (window.Livewire && typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('openDeleteModal', { tariffListId: tariffListId });
            }
        };
        window.openApplyModal = function (tariffListId) {
            if (window.Livewire && typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('openApplyModal', { tariffListId: tariffListId });
            }
        };
        function initServiceCheckboxes() {
            const checkboxes = document.querySelectorAll('.selector-checkbox');
            checkboxes.forEach(function (cb) {
                cb.addEventListener('change', function (e) {
                    const checkedIds = Array.from(document.querySelectorAll('.selector-checkbox:checked')).map(function (el) {
                        return el.value;
                    });
                    if (window.Livewire && typeof Livewire.emit === 'function') {
                        Livewire.emit('syncSelectedServices', checkedIds);
                    }
                    updateCarouselSlidesState();
                });
            });
            const serviceLabels = document.querySelectorAll('.service-label');
            serviceLabels.forEach(function (label) {
                label.addEventListener('click', function (e) {
                    e.preventDefault();
                    const sid = label.getAttribute('data-service-id');
                    if (!sid) return;
                    const carouselEl = document.getElementById('edit-tariffCarousel');
                    if (!carouselEl) return;
                    const items = Array.from(carouselEl.querySelectorAll('.carousel-item'));
                    const targetIndex = items.findIndex(function (itm) {
                        return itm.getAttribute('data-service-id') === sid;
                    });
                    if (targetIndex === -1) return;
                    const carousel = bootstrap.Carousel.getOrCreateInstance(carouselEl);
                    carousel.to(targetIndex);
                    carouselEl.focus();
                });
            });
        }
        function updateCarouselSlidesState() {
            const carouselEl = document.getElementById('edit-tariffCarousel');
            if (!carouselEl) return;
            const items = Array.from(carouselEl.querySelectorAll('.carousel-item'));
            items.forEach(function (item) {
                const sid = item.getAttribute('data-service-id');
                const checkbox = document.querySelector('.selector-checkbox[value="' + sid + '"]');
                const disabled = !(checkbox && checkbox.checked);
                if (disabled) {
                    item.classList.add('disabled-slide');
                    const inputs = item.querySelectorAll('input, button');
                    inputs.forEach(function (el) {
                        if (el.classList.contains('nav-arrow')) return;
                        el.setAttribute('disabled', 'disabled');
                    });
                    item.style.opacity = '0.5';
                    item.style.pointerEvents = 'none';
                    const navPrev = item.querySelector('.carousel-control-prev');
                    const navNext = item.querySelector('.carousel-control-next');
                    if (navPrev) navPrev.style.pointerEvents = 'auto';
                    if (navNext) navNext.style.pointerEvents = 'auto';
                } else {
                    item.classList.remove('disabled-slide');
                    const inputs2 = item.querySelectorAll('input, button');
                    inputs2.forEach(function (el) {
                        if (el.classList.contains('nav-arrow')) return;
                        el.removeAttribute('disabled');
                    });
                    item.style.opacity = '';
                    item.style.pointerEvents = '';
                }
            });
        }
        function initAddRemoveButtons(scope) {
            scope = scope || document;
            const addBtns = scope.querySelectorAll('.row-add-btn');
            addBtns.forEach(function (btn) {
                btn.removeEventListener('click', handleAddClick);
                btn.addEventListener('click', handleAddClick);
            });
            const removeBtns = scope.querySelectorAll('.row-remove-btn');
            removeBtns.forEach(function (btn) {
                btn.removeEventListener('click', handleRemoveClick);
                btn.addEventListener('click', handleRemoveClick);
            });
        }
        function handleAddClick(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const row = btn.closest('.money-amount-row');
            if (!row) return;
            const indexAttr = row.getAttribute('data-range-index');
            const index = parseInt(indexAttr, 10);
            const serviceId = btn.closest('.carousel-item')?.getAttribute('data-service-id') || null;
            if (isNaN(index) || !serviceId) return;
            if (window.Livewire && typeof Livewire.emit === 'function') {
                Livewire.emit('addRangeAt', index, serviceId);
            }
        }
        function handleRemoveClick(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const row = btn.closest('.money-amount-row');
            if (!row) return;
            const indexAttr = row.getAttribute('data-range-index');
            const index = parseInt(indexAttr, 10);
            const serviceId = btn.closest('.carousel-item')?.getAttribute('data-service-id') || null;
            if (isNaN(index) || !serviceId) return;
            if (window.Livewire && typeof Livewire.emit === 'function') {
                Livewire.emit('removeRange', index, serviceId);
            }
        }
        function initTariffEditModalFunctions(scope) {
            scope = scope || document;
            initServiceCheckboxes();
            initAddRemoveButtons(scope);
            updateCarouselSlidesState();
        }
        document.addEventListener('livewire:load', function () {
            initTariffEditModalFunctions(document);
        });
        if (window.Livewire && Livewire.hook) {
            Livewire.hook('message.processed', function () {
                initTariffEditModalFunctions(document);
            });
        }
        window.addEventListener('tariff-edit-opened', function () {
            initTariffEditModalFunctions(document);
            setTimeout(function () {
                updateCarouselSlidesState();
            }, 50);
        });
        window.addEventListener('tariff-ranges-updated', function () {
            initTariffEditModalFunctions(document);
        });
        window.addEventListener('tariff-edit-closed', function () {
            initTariffEditModalFunctions(document);
        });
        initTariffEditModalFunctions(document);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
