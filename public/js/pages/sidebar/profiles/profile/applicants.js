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
    ].forEach(setupDropdownArrowRotation);

    function setupDynamicDropdownsRotation(prefix) {
        document.querySelectorAll(`[id^="${prefix}"]`).forEach(btn => {
            btn.addEventListener('show.bs.dropdown', () => {
                btn.classList.add('rotated');
            });

            btn.addEventListener('hide.bs.dropdown', () => {
                btn.classList.remove('rotated');
            });
        });
    }

    [
        'patientSuffixDropdownBtn-',
        'patientSexDropdownBtn-',
        'patientCategoryDropdownBtn-',
    ].forEach(setupDynamicDropdownsRotation);

    Livewire.on('scrollToElement', elementId => {
        const element = document.getElementById(elementId);

        if (element) {
            const scrollTarget = element.closest('.form-group') || element;
            scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });

            if (element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                element.focus();
            } else if (element.classList.contains('dropdown-toggle')) {
                element.focus();
            }
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
            const paste = event.clipboardData.getData('text');
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

            if (document.activeElement === e.target) {
                try {
                    e.target.selectionStart = e.target.selectionEnd = formatted.length;
                } catch (err) { }
            }
        });

        phoneInput.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = e.clipboardData.getData('text');
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

    const applicantBirthdateInput = document.getElementById('applicantBirthdateInput');
    const applicantAgeInput = document.getElementById('applicantAgeInput');
    const applicantAgeHidden = document.getElementById('applicantAgeHidden');

    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        return age;
    }

    function updateApplicantAge() {
        if (applicantBirthdateInput.value) {
            const age = calculateAge(applicantBirthdateInput.value);
            applicantAgeInput.value = age;
            applicantAgeHidden.value = age;

            const event = new Event('input', { bubbles: true });
            applicantAgeHidden.dispatchEvent(event);

            if (checkbox.checked) {
                copyApplicantToPatient1();
            }
        } else {
            applicantAgeInput.value = '';
            applicantAgeHidden.value = '';
        }
    }

    if (applicantBirthdateInput && applicantAgeInput && applicantAgeHidden) {
        applicantBirthdateInput.addEventListener('change', updateApplicantAge);
        applicantBirthdateInput.addEventListener('input', updateApplicantAge);

        if (applicantBirthdateInput.value) {
            updateApplicantAge();
        }
    }

    const checkbox = document.getElementById('checkbox');
    const patientNumberInput = document.getElementById('patientNumberInput');

    function copyApplicantToPatient1() {
        const applicantLastName = document.getElementById('applicantLastNameInput').value;
        const applicantFirstName = document.getElementById('applicantFirstNameInput').value;
        const applicantMiddleName = document.getElementById('applicantMiddleNameInput').value;
        const applicantSuffix = document.getElementById('applicantSuffixDropdownBtn').textContent.trim();
        const applicantSex = document.getElementById('applicantSexDropdownBtn').textContent.trim();
        const applicantAge = document.getElementById('applicantAgeInput').value;

        document.getElementById('patientLastNameInput-1').value = applicantLastName;
        document.getElementById('patientFirstNameInput-1').value = applicantFirstName;
        document.getElementById('patientMiddleNameInput-1').value = applicantMiddleName;
        document.getElementById('patientSuffixDropdownBtn-1').textContent = applicantSuffix;
        document.getElementById('patientSexDropdownBtn-1').textContent = applicantSex;
        document.getElementById('patientAgeInput-1').value = applicantAge;

        document.querySelectorAll('#patientLastNameInput-1, #patientFirstNameInput-1, #patientMiddleNameInput-1, #patientAgeInput-1').forEach(field => {
            field.disabled = true;
        });

        document.querySelectorAll('#patientSuffixDropdownBtn-1, #patientSexDropdownBtn-1').forEach(btn => {
            btn.disabled = true;
        });
    }

    function clearPatient1() {
        document.getElementById('patientLastNameInput-1').value = '';
        document.getElementById('patientFirstNameInput-1').value = '';
        document.getElementById('patientMiddleNameInput-1').value = '';
        document.getElementById('patientSuffixDropdownBtn-1').textContent = '— Select —';
        document.getElementById('patientSexDropdownBtn-1').textContent = '— Select —';
        document.getElementById('patientAgeInput-1').value = '';

        document.querySelectorAll('#patientLastNameInput-1, #patientFirstNameInput-1, #patientMiddleNameInput-1, #patientAgeInput-1').forEach(field => {
            field.disabled = false;
        });

        document.querySelectorAll('#patientSuffixDropdownBtn-1, #patientSexDropdownBtn-1').forEach(btn => {
            btn.disabled = false;
        });
    }

    function handleCheckboxChange() {
        if (checkbox.checked) {
            copyApplicantToPatient1();
        } else {
            clearPatient1();
        }
    }

    function handleApplicantFieldChange() {
        if (checkbox.checked) {
            copyApplicantToPatient1();
        }
    }

    if (checkbox && patientNumberInput) {
        checkbox.addEventListener('change', handleCheckboxChange);

        document.getElementById('applicantLastNameInput').addEventListener('input', handleApplicantFieldChange);
        document.getElementById('applicantFirstNameInput').addEventListener('input', handleApplicantFieldChange);
        document.getElementById('applicantMiddleNameInput').addEventListener('input', handleApplicantFieldChange);

        document.querySelectorAll('#applicantSuffixDropdownBtn + .dropdown-menu a').forEach(item => {
            item.addEventListener('click', handleApplicantFieldChange);
        });

        document.querySelectorAll('#applicantSexDropdownBtn + .dropdown-menu a').forEach(item => {
            item.addEventListener('click', handleApplicantFieldChange);
        });

        patientNumberInput.addEventListener('input', function () {
            if (this.value > 10) {
                this.value = 10;
            }
        });
    }

    document.querySelectorAll('.patient-age-input').forEach(input => {
        input.addEventListener('input', function () {
            if (this.value > 200) {
                this.value = 200;
            }
        });
    });
});
