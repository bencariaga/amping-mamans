document.addEventListener('DOMContentLoaded', function () {
    const editBtn = document.getElementById('editRoleBtn');
    const saveBtn = document.getElementById('saveChangesBtn');
    const roleSpans = document.querySelectorAll('.user-role-label');
    const roleSelects = document.querySelectorAll('.user-role-dropdown');

    editBtn.addEventListener('click', function () {
        roleSpans.forEach(span => span.classList.add('d-none'));
        roleSelects.forEach(sel => sel.classList.remove('d-none'));
        editBtn.classList.add('d-none');
        saveBtn.classList.remove('d-none');
    });

    function setupDropdown(buttonId, inputId, isFilter = false) {
        const dropdownButton = document.getElementById(buttonId);
        const hiddenInput = document.getElementById(inputId);
        const dropdownItems = document.querySelectorAll(`#${buttonId} + .dropdown-menu .dropdown-item`);

        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();

                const value = this.getAttribute('data-value');
                const text = this.textContent.trim();

                hiddenInput.value = value;
                dropdownButton.textContent = text;
                dropdownItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                if (isFilter) {
                    document.getElementById('user-filter-form').submit();
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
            hiddenInput.value = dropdownItems[0].getAttribute('data-value');
        }

        dropdownButton.addEventListener('show.bs.dropdown', function () {
            this.classList.add('rotated');
        });

        dropdownButton.addEventListener('hide.bs.dropdown', function () {
            this.classList.remove('rotated');
        });
    }

    document.querySelectorAll('.user-card-box .user-role-dropdown').forEach(dropdownDiv => {
        const userId = dropdownDiv.querySelector('input[type="hidden"]').id.replace('roleInput-', '');
        setupDropdown(`roleDropdownBtn-${userId}`, `roleInput-${userId}`);
    });

    saveBtn.addEventListener('click', function () {
        document.getElementById('user-role-update-form').submit();
    });

    setupDropdown('sortDropdownBtn', 'filter-sort-by', true);
    setupDropdown('perPageDropdownBtn', 'filter-per-page', true);
});
