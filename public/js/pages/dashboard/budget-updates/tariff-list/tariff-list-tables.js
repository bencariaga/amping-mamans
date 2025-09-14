document.addEventListener('DOMContentLoaded', function () {
    const selectorCheckboxes = Array.from(document.querySelectorAll('.selector-checkbox'));
    const carouselEl = document.getElementById('tariffCarousel');
    const effectivityDate = document.getElementById('effectivity-date');
    const applyCheckbox = document.getElementById('apply-version');
    const serviceLabels = Array.from(document.querySelectorAll('.service-label'));
    const bsCarousel = (typeof bootstrap !== 'undefined' && carouselEl) ? new bootstrap.Carousel(carouselEl, { ride: false, interval: false }) : null;

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
                if (applyCheckbox) applyCheckbox.checked = true;
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

    function buildNewRowElement(serviceId) {
        const tr = document.createElement('tr');
        tr.className = 'money-amount-row';

        tr.innerHTML = `
            <td class="money-amount-cell">
                <div class="money-amount-container">
                    <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                    <span class="money-currency fw-bold">₱</span>
                    <input type="number" step="0.01" name="range_min_new[${serviceId}][]" class="form-control form-control-sm range-input range-min-input text-end money-value" value="0.00">
                </div>
            </td>
            <td class="money-amount-cell">
                <div class="money-amount-container">
                    <span class="money-currency fw-bold">₱</span>
                    <input type="number" step="0.01" name="range_max_new[${serviceId}][]" class="form-control form-control-sm range-input range-max-input text-end money-value" value="0.00">
                    <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                </div>
            </td>
            <td class="money-amount-cell">
                <div class="money-amount-container">
                    <span class="money-currency fw-bold">₱</span>
                    <input type="number" step="0.01" name="tariff_amount_new[${serviceId}][]" class="form-control form-control-sm tariff-input text-end money-value" value="0.00">
                </div>
            </td>
        `;

        return tr;
    }

    function handleRowAdd(event) {
        const target = event.target.closest('.row-add-btn');

        if (!target) return;
        if (target.disabled) return;

        const currentRow = target.closest('.money-amount-row');
        const tbody = currentRow.closest('tbody.service-rows');
        const serviceId = tbody ? tbody.getAttribute('data-service-id') : '';
        const newRow = buildNewRowElement(serviceId);

        if (currentRow && currentRow.nextSibling) {
            tbody.insertBefore(newRow, currentRow.nextSibling);
        } else {
            tbody.appendChild(newRow);
        }

        updateAllRowButtons();
    }

    function handleRowRemove(event) {
        const target = event.target.closest('.row-remove-btn');

        if (!target) return;
        if (target.disabled) return;

        const currentRow = target.closest('.money-amount-row');
        const tbody = currentRow.closest('tbody.service-rows');
        const rows = Array.from(tbody.querySelectorAll('.money-amount-row'));

        if (rows.length <= 1) {
            const serviceId = tbody ? tbody.getAttribute('data-service-id') : '';
            const newRow = buildNewRowElement(serviceId);
            currentRow.parentNode.replaceChild(newRow, currentRow);
        } else {
            currentRow.remove();
        }

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
        cb.addEventListener('change', function () {
            updateCarouselState();

            if (!carouselEl || !bsCarousel) return;

            const type = this.getAttribute('data-service-type');
            const allItems = Array.from(carouselEl.querySelectorAll('.carousel-item'));
            const targetItem = allItems.find(i => i.getAttribute('data-service-type') === type);

            if (this.checked && targetItem) {
                const idx = allItems.indexOf(targetItem);
                if (idx >= 0) bsCarousel.to(idx);
            } else if (!this.checked && targetItem) {
                const visibleItems = allItems.filter(i => !i.classList.contains('pale-item'));

                if (visibleItems.length) {
                    const idx = allItems.indexOf(visibleItems[0]);
                    if (idx >= 0) bsCarousel.to(idx);
                }
            }
        });
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

    updateCarouselState();
    updateAllRowButtons();
    updateLabelStates();

    const currentTariffListId = window.tariffListConfig.currentTariffListId;
    const latestEffectiveTariffListId = window.tariffListConfig.latestEffectiveTariffListId;

    if (currentTariffListId && latestEffectiveTariffListId) {
        if (currentTariffListId !== latestEffectiveTariffListId) {
            if (applyCheckbox) {
                applyCheckbox.checked = false;
                applyCheckbox.disabled = true;

                const lbl = applyCheckbox.parentElement.querySelector('label');

                if (lbl) lbl.classList.add('text-muted');

                if (effectivityDate) {
                    effectivityDate.disabled = true;
                }
            }
        } else {
            if (applyCheckbox) {
                applyCheckbox.disabled = false;
                const lbl = applyCheckbox.parentElement.querySelector('label');

                if (lbl) lbl.classList.remove('text-muted');
            }

            if (effectivityDate) {
                effectivityDate.disabled = false;
            }
        }
    }
});
