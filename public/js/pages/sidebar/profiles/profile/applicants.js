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
    ].forEach(setupDropdownArrowRotation);

    Livewire.on('update-ui-elements', () => {
        const occupationDropdownBtn = document.getElementById('occupationDropdownBtn');
        const customOccupationInput = document.getElementById('customOccupationInput');

        if (customOccupationInput && occupationDropdownBtn) {
            if (customOccupationInput.value.trim() !== '') {
                occupationDropdownBtn.disabled = true;
            } else {
                occupationDropdownBtn.disabled = false;
            }
            customOccupationInput.readOnly = (occupationDropdownBtn.textContent.trim() !== '— Select —' && occupationDropdownBtn.textContent.trim() !== '');
        }

        const phicAffiliationDropdownBtn = document.getElementById('phicAffiliationDropdownBtn');
        const phicCategoryDropdownBtn = document.getElementById('phicCategoryDropdownBtn');

        if (phicAffiliationDropdownBtn && phicCategoryDropdownBtn) {
            if (phicAffiliationDropdownBtn.textContent.trim() === 'Unaffiliated' || phicAffiliationDropdownBtn.textContent.trim() === '— Select —') {
                phicCategoryDropdownBtn.disabled = true;
                phicCategoryDropdownBtn.textContent = '— Select —';
            } else {
                phicCategoryDropdownBtn.disabled = false;
            }
        }
    });
});
