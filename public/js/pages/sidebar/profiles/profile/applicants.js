document.addEventListener('DOMContentLoaded', () => {
    function setupDropdownArrowRotation(buttonId) {
        const btn = document.getElementById(buttonId);
        if (btn) {
            btn.addEventListener('show.bs.dropdown', () => {
                btn.classList.add('rotated');
            });
            btn.addEventListener('hide.bs.dropdown', () => {
                btn.classList.remove('rotated');
            });
        }
    }

    [
        'applicantSuffixDropdownBtn',
        'applicantSexDropdownBtn',
        'applicantBarangayDropdownBtn',
        'applicantCivilStatusDropdownBtn',
        'applicantOccupationDropdownBtn',
        'applicantPhicAffiliationDropdownBtn',
        'applicantPhicCategoryDropdownBtn',
        'applicantJobStatusDropdownBtn',
        'applicantHouseStatusDropdownBtn',
        'applicantLotStatusDropdownBtn',
        'applicantRepresentingPatientDropdownBtn',
        'applicantPatientCountDropdownBtn',
        'patientSuffixDropdownBtn-1',
        'patientSuffixDropdownBtn-2',
        'patientSuffixDropdownBtn-3',
        'occupationDropdownBtn',
        'customOccupationInput',
        'phicAffiliationDropdownBtn',
        'phicCategoryDropdownBtn'
    ].forEach(setupDropdownArrowRotation);

    Livewire.on('scrollToElement', elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.focus();
        }
    });

    const monthlyIncomeDisplayInput = document.getElementById('monthlyIncomeDisplayInput');
    const monthlyIncomeHiddenInput = document.getElementById('monthlyIncomeHiddenInput');

    function sanitizeNumericInput(raw) {
        if (raw === null || raw === undefined) return '';
        let s = String(raw);
        s = s.replace(/,/g, '');
        s = s.replace(/\D/g, '');
        s = s.replace(/^0+(?=\d)/, '');
        return s;
    }

    function formatForDisplay(sanitized) {
        if (sanitized === '' || sanitized === null || sanitized === undefined) return '';
        const n = parseInt(sanitized, 10);
        if (isNaN(n)) return '';
        return n.toLocaleString();
    }

    function setHiddenValueAndNotify(value) {
        if (!monthlyIncomeHiddenInput) return;
        monthlyIncomeHiddenInput.value = value;
        const ev = new Event('input', { bubbles: true });
        monthlyIncomeHiddenInput.dispatchEvent(ev);
    }

    function updateDisplayFromHidden() {
        if (!monthlyIncomeDisplayInput || !monthlyIncomeHiddenInput) return;
        const sanitized = sanitizeNumericInput(monthlyIncomeHiddenInput.value);
        monthlyIncomeDisplayInput.value = formatForDisplay(sanitized);
    }

    if (monthlyIncomeDisplayInput && monthlyIncomeHiddenInput) {
        monthlyIncomeDisplayInput.addEventListener('input', (event) => {
            const raw = event.target.value;
            const sanitized = sanitizeNumericInput(raw);
            setHiddenValueAndNotify(sanitized === '' ? '' : sanitized);
            event.target.value = formatForDisplay(sanitized);
            try {
                event.target.selectionStart = event.target.selectionEnd = event.target.value.length;
            } catch (e) { }
        });

        monthlyIncomeDisplayInput.addEventListener('paste', (event) => {
            event.preventDefault();
            const paste = (event.clipboardData || window.clipboardData).getData('text');
            const sanitized = sanitizeNumericInput(paste);
            setHiddenValueAndNotify(sanitized === '' ? '' : sanitized);
            monthlyIncomeDisplayInput.value = formatForDisplay(sanitized);
            try {
                monthlyIncomeDisplayInput.selectionStart = monthlyIncomeDisplayInput.selectionEnd = monthlyIncomeDisplayInput.value.length;
            } catch (e) { }
        });

        monthlyIncomeDisplayInput.addEventListener('focus', (event) => {
            const sanitized = sanitizeNumericInput(monthlyIncomeHiddenInput.value);
            event.target.value = formatForDisplay(sanitized);
            try {
                event.target.selectionStart = event.target.selectionEnd = event.target.value.length;
            } catch (e) { }
        });

        monthlyIncomeDisplayInput.addEventListener('blur', () => {
            updateDisplayFromHidden();
        });

        updateDisplayFromHidden();

        if (window.Livewire) {
            Livewire.hook('message.processed', () => {
                updateDisplayFromHidden();
            });
            document.addEventListener('livewire:init', () => {
                updateDisplayFromHidden();
            });
        } else {
            document.addEventListener('livewire:init', () => {
                updateDisplayFromHidden();
            });
        }
    }

    const phoneInput = document.getElementById('applicantPhoneNumberInput');

    function formatPhoneForDisplay(raw) {
        if (!raw) return '';
        let s = raw.toString().trim();
        s = s.replace(/[^\d+]/g, '');
        if (s.startsWith('+')) {
            s = s.slice(1);
        }
        s = s.replace(/[^\d]/g, '');
        if (s.startsWith('63')) {
            s = '0' + s.slice(2);
        } else if (s.startsWith('9')) {
            s = '0' + s;
        } else if (!s.startsWith('0')) {
            s = '0' + s;
        }
        const parts = [];
        if (s.length <= 4) {
            return s;
        }
        parts.push(s.slice(0, 4));
        if (s.length > 4) {
            parts.push(s.slice(4, Math.min(7, s.length)));
        }
        if (s.length > 7) {
            parts.push(s.slice(7, Math.min(11, s.length)));
        }
        return parts.join('-');
    }

    function dispatchInputEvent(el) {
        const ev = new Event('input', { bubbles: true });
        el.dispatchEvent(ev);
    }

    if (phoneInput) {
        phoneInput.setAttribute('inputmode', 'tel');
        phoneInput.setAttribute('maxlength', '15');

        phoneInput.addEventListener('input', (e) => {
            const raw = e.target.value;
            const formatted = formatPhoneForDisplay(raw);
            e.target.value = formatted;
            dispatchInputEvent(e.target);
            if (document.activeElement === e.target) {
                try {
                    e.target.selectionStart = e.target.selectionEnd = formatted.length;
                } catch (err) { }
            }
        });

        phoneInput.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const formatted = formatPhoneForDisplay(paste);
            phoneInput.value = formatted;
            dispatchInputEvent(phoneInput);
        });

        if (window.Livewire && typeof Livewire.hook === 'function') {
            Livewire.hook('message.processed', () => {
                const current = phoneInput.value;
                const formatted = formatPhoneForDisplay(current);
                phoneInput.value = formatted;
            });
        }
    }

    function handleUpdateUIElements() {
        const occupationDropdownBtn = document.getElementById('applicantOccupationDropdownBtn') || document.getElementById('occupationDropdownBtn');
        const customOccupationInput = document.getElementById('applicantCustomOccupationInput') || document.getElementById('customOccupationInput');

        if (customOccupationInput && occupationDropdownBtn) {
            if (customOccupationInput.value.trim() !== '') {
                occupationDropdownBtn.disabled = true;
            } else {
                occupationDropdownBtn.disabled = false;
            }

            customOccupationInput.readOnly = (occupationDropdownBtn.textContent.trim() !== '— Select —' && occupationDropdownBtn.textContent.trim() !== '');
        }

        const phicAffiliationDropdownBtn = document.getElementById('applicantPhicAffiliationDropdownBtn') || document.getElementById('phicAffiliationDropdownBtn');
        const phicCategoryDropdownBtn = document.getElementById('applicantPhicCategoryDropdownBtn') || document.getElementById('phicCategoryDropdownBtn');

        if (phicAffiliationDropdownBtn && phicCategoryDropdownBtn) {
            if (phicAffiliationDropdownBtn.textContent.trim() === 'Unaffiliated' || phicAffiliationDropdownBtn.textContent.trim() === '— Select —') {
                phicCategoryDropdownBtn.disabled = true;
                phicCategoryDropdownBtn.textContent = '— Select —';
            } else {
                phicCategoryDropdownBtn.disabled = false;
            }
        }
    }

    window.addEventListener('update-ui-elements', handleUpdateUIElements);
});

document.addEventListener('livewire:load', function () {
    window.dispatchEvent(new CustomEvent('update-ui-elements'));
});
