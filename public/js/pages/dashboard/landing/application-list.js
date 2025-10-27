document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(buttonId, inputId, formId = null) {
        const dropdownButton = document.getElementById(buttonId);
        const hiddenInput = document.getElementById(inputId);
        const dropdownItems = document.querySelectorAll(`#${buttonId} + .dropdown-menu .dropdown-item`);

        if (!dropdownButton || !hiddenInput || dropdownItems.length === 0) return;

        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();

                const value = this.getAttribute('data-value');
                const text = this.textContent.trim();

                hiddenInput.value = value;
                dropdownButton.textContent = text;
                dropdownItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                if (formId) {
                    document.getElementById(formId).submit();
                }
            });
        });

        const initialValue = hiddenInput.value;
        const initialTextElement = Array.from(dropdownItems).find(item => item.getAttribute('data-value') === initialValue);

        if (initialTextElement) {
            dropdownButton.textContent = initialTextElement.textContent.trim();
            initialTextElement.classList.add('active');
        } else if (dropdownItems.length > 0) {
            dropdownButton.textContent = dropdownItems[0].textContent.trim();
            dropdownItems[0].classList.add('active');
        }

        dropdownButton.addEventListener('show.bs.dropdown', function () {
            this.classList.add('rotated');
        });
        dropdownButton.addEventListener('hide.bs.dropdown', function () {
            this.classList.remove('rotated');
        });
    }

    const modal = document.getElementById('detailsModal');
    const modalBody = document.getElementById('detailsModalBody');

    document.querySelectorAll('.details-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const applicationId = this.getAttribute('data-application-id');
            fetch(`/applications/${applicationId}/details`)
                .then(response => response.json())
                .then(data => {
                    modalBody.innerHTML = data.html;
                    modal.style.display = 'flex';
                })
                .catch(error => {
                    modalBody.innerHTML = '<div>Error loading application details.</div>';
                    modal.style.display = 'flex';
                });
        });
    });

    setupDropdown('sortDropdownBtn', 'filter-sort-by', 'application-filter-form');
    setupDropdown('perPageDropdownBtn', 'filter-per-page', 'application-filter-form');
    setupDropdown('statusDropdownBtn', 'filter-status', 'application-filter-form');
    setupDropdown('serviceDropdownBtn', 'filter-service', 'application-filter-form');
});

function showModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
