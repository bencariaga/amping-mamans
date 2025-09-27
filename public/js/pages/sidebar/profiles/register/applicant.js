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

    function setHiddenValueAndNotify(value) {
        monthlyIncomeHiddenInput.value = value;
        const ev = new Event('input', { bubbles: true });
        monthlyIncomeHiddenInput.dispatchEvent(ev);
    }

    if (monthlyIncomeDisplayInput && monthlyIncomeHiddenInput) {
        monthlyIncomeDisplayInput.addEventListener('input', (event) => {
            let value = event.target.value.replace(/[^0-9]/g, '');
            setHiddenValueAndNotify(value);
            event.target.value = value === '' ? '' : Number(value).toLocaleString();
        });

        monthlyIncomeDisplayInput.addEventListener('paste', (event) => {
            event.preventDefault();
            const paste = (event.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^0-9]/g, '');
            setHiddenValueAndNotify(cleanPaste);
            monthlyIncomeDisplayInput.value = cleanPaste === '' ? '' : Number(cleanPaste).toLocaleString();
        });

        if (monthlyIncomeHiddenInput.value !== '') {
            const val = monthlyIncomeHiddenInput.value.toString().replace(/[^0-9]/g, '');
            monthlyIncomeDisplayInput.value = val === '' ? '' : Number(val).toLocaleString();
        }

        if (window.Livewire && typeof Livewire.hook === 'function') {
            Livewire.hook('message.processed', () => {
                const val = monthlyIncomeHiddenInput.value.toString().replace(/[^0-9]/g, '');
                monthlyIncomeDisplayInput.value = val === '' ? '' : Number(val).toLocaleString();
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
});
