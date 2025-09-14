document.addEventListener('DOMContentLoaded', function () {
    const selectorCheckboxes = Array.from(document.querySelectorAll('.selector-checkbox'));
    const carouselEl = document.getElementById('tariffCarousel');
    const effectivityDate = document.getElementById('effectivity-date');
    const applyCheckbox = document.getElementById('apply-version');
    const serviceLabels = Array.from(document.querySelectorAll('.service-label'));
    const bsCarousel = carouselEl ? new bootstrap.Carousel(carouselEl, { ride: false, interval: false }) : null;

    function getTodayString() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    if (effectivityDate) {
        const todayStr = getTodayString();
        effectivityDate.setAttribute('min', todayStr);

        effectivityDate.addEventListener('change', function () {
            if (this.value === todayStr) {
                applyCheckbox.checked = true;
            }
        });
    }

    if (applyCheckbox && effectivityDate) {
        applyCheckbox.addEventListener('change', function () {
            if (this.checked) {
                effectivityDate.value = getTodayString();
            } else {
                effectivityDate.value = '';
            }
        });
    }

    function updateLabelStates() {
        selectorCheckboxes.forEach(cb => {
            const type = cb.getAttribute('data-service-type');
            const label = document.querySelector(`.service-label[data-service-type="${CSS.escape(type)}"]`);
            if (label) {
                if (cb.checked) {
                    label.classList.add('checked');
                } else {
                    label.classList.remove('checked');
                }
            }
        });
    }

    function setItemPaleState(item, isPale) {
        item.classList.toggle('pale-item', isPale);
        const controls = Array.from(item.querySelectorAll('input, select, textarea, button, a'));
        controls.forEach(el => {
            if (el.classList && el.classList.contains('nav-arrow')) return;
            const tag = el.tagName.toLowerCase();
            if (tag === 'input' || tag === 'select' || tag === 'textarea' || tag === 'button') {
                el.disabled = isPale;
                if (isPale) {
                    el.setAttribute('aria-disabled', 'true');
                    el.setAttribute('data-prev-tabindex', el.tabIndex);
                    el.tabIndex = -1;
                } else {
                    el.removeAttribute('aria-disabled');
                    if (el.hasAttribute('data-prev-tabindex')) {
                        el.tabIndex = parseInt(el.getAttribute('data-prev-tabindex'), 10);
                        el.removeAttribute('data-prev-tabindex');
                    } else {
                        el.tabIndex = 0;
                    }
                }
            } else if (tag === 'a') {
                if (isPale) {
                    el.setAttribute('aria-disabled', 'true');
                    el.setAttribute('data-prev-tabindex', el.tabIndex);
                    el.tabIndex = -1;
                    el.style.pointerEvents = 'none';
                } else {
                    el.removeAttribute('aria-disabled');
                    if (el.hasAttribute('data-prev-tabindex')) {
                        el.tabIndex = parseInt(el.getAttribute('data-prev-tabindex'), 10);
                        el.removeAttribute('data-prev-tabindex');
                    } else {
                        el.tabIndex = 0;
                    }
                    el.style.pointerEvents = '';
                }
            }
        });
    }

    function updateCarouselState() {
        if (!carouselEl) return;
        const items = Array.from(carouselEl.querySelectorAll('.carousel-item'));
        const checkedTypes = selectorCheckboxes.filter(cb => cb.checked).map(cb => cb.getAttribute('data-service-type'));
        items.forEach(item => {
            const type = item.getAttribute('data-service-type');
            const isChecked = checkedTypes.includes(type);
            setItemPaleState(item, !isChecked);
        });
        updateAllRowButtons();
        updateLabelStates();
    }

    function updateRowButtons() {
        const tables = document.querySelectorAll('.service-rows');
        tables.forEach(tbl => {
            const rows = Array.from(tbl.querySelectorAll('.money-amount-row'));
            const count = rows.length;
            rows.forEach(r => {
                const removeBtn = r.querySelector('.row-remove-btn');
                const addBtn = r.querySelector('.row-add-btn');
                if (removeBtn) removeBtn.style.visibility = count > 1 ? 'visible' : 'hidden';
                if (addBtn) addBtn.style.visibility = 'visible';
            });
        });
    }

    function updateAllRowButtons() {
        updateRowButtons();
    }

    function generateTempId() {
        return 'TEMP-' + Date.now().toString(36) + '-' + Math.random().toString(36).substring(2);
    }

    function handleRowAdd(event) {
        const target = event.target.closest('.row-add-btn');
        if (!target) return;
        if (target.disabled) return;
        const currentRow = target.closest('.money-amount-row');
        const tbody = currentRow.closest('tbody.service-rows');
        const clone = currentRow.cloneNode(true);
        const newId = generateTempId();
        clone.setAttribute('data-expid', newId);
        clone.querySelectorAll('input').forEach(inp => {
            inp.value = '0.00';
            inp.disabled = false;
        });
        tbody.insertBefore(clone, currentRow.nextSibling);
        updateAllRowButtons();
    }

    function handleRowRemove(event) {
        const target = event.target.closest('.row-remove-btn');
        if (!target) return;
        if (target.disabled) return;
        const currentRow = target.closest('.money-amount-row');
        currentRow.remove();
        updateAllRowButtons();
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.row-add-btn')) {
            handleRowAdd(e);
        } else if (e.target.closest('.row-remove-btn')) {
            handleRowRemove(e);
        }
    });

    selectorCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCarouselState);
    });

    serviceLabels.forEach(lbl => {
        lbl.addEventListener('click', function (ev) {
            ev.preventDefault();
            const type = this.getAttribute('data-service-type');
            const cb = selectorCheckboxes.find(x => x.getAttribute('data-service-type') === type);
            if (!cb || !cb.checked) return;
            if (!carouselEl || !bsCarousel) return;
            const allItems = Array.from(carouselEl.querySelectorAll('.carousel-item'));
            const targetItem = allItems.find(i => i.getAttribute('data-service-type') === type);
            if (!targetItem) return;
            const idx = allItems.indexOf(targetItem);
            if (idx >= 0) {
                bsCarousel.to(idx);
            }
        });
    });

    if (applyCheckbox) applyCheckbox.checked = true;
    updateCarouselState();
    updateAllRowButtons();
    updateLabelStates();
});
