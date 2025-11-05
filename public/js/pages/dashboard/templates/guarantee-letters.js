document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(buttonId, inputId, formId = null) {
        const dropdownButton = document.getElementById(buttonId);
        const hiddenInput = document.getElementById(inputId);
        const dropdownItems = document.querySelectorAll(`#${buttonId} + .dropdown-menu .dropdown-item`);

        if (!dropdownButton || !hiddenInput) return;

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
                    const form = document.getElementById(formId);
                    if (form) form.submit();
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

    setupDropdown('sortDropdownBtn', 'filter-sort-by', 'template-filter-form');
    setupDropdown('perPageDropdownBtn', 'filter-per-page', 'template-filter-form');

    const titleInput = document.getElementById('gl-tmp-title');
    const titleCount = document.getElementById('title-count');
    const form = document.getElementById('gl-template-form');
    const titleErrorDiv = document.getElementById('title-error');
    const addGlTmpBtn = document.getElementById('addGlTmpBtn');
    const updateGlTmpBtn = document.getElementById('updateGlTmpBtn');

    if (!form) return;

    function updateTitleCount() {
        if (titleInput && titleCount) {
            titleCount.textContent = titleInput.value.length;
        }
    }

    function clearValidationErrors() {
        if (titleErrorDiv) {
            titleErrorDiv.style.display = 'none';
        }

        if (titleInput) {
            titleInput.classList.remove('is-invalid');
        }
    }

    function handleSubmission(e) {
        e.preventDefault();
        clearValidationErrors();
        form.submit();
    }

    if (titleInput) {
        titleInput.addEventListener('input', function () {
            if (this.value.length > 30) {
                this.value = this.value.substring(0, 30);
            }

            updateTitleCount();
            clearValidationErrors();
        });
    }

    if (addGlTmpBtn) {
        addGlTmpBtn.addEventListener('click', handleSubmission);
    }

    if (updateGlTmpBtn) {
        updateGlTmpBtn.addEventListener('click', handleSubmission);
    }

    updateTitleCount();
});
