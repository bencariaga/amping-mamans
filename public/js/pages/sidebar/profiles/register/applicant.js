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
});
