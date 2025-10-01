document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(buttonId, inputId, formId = null) {
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
            hiddenInput.value = dropdownItems[0].getAttribute('data-value');
        }

        dropdownButton.addEventListener('show.bs.dropdown', function () {
            this.classList.add('rotated');
        });

        dropdownButton.addEventListener('hide.bs.dropdown', function () {
            this.classList.remove('rotated');
        });
    }

    setupDropdown('sortDropdownBtn', 'filter-sort-by', 'client-filter-form');
    setupDropdown('perPageDropdownBtn', 'filter-per-page', 'client-filter-form');

    document.querySelectorAll('.copy-symbol').forEach(copySymbol => {
        copySymbol.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const phoneNumber = this.getAttribute('data-phone-number');

            navigator.clipboard.writeText(phoneNumber).then(() => {
                const originalIcon = this.querySelector('i');
                originalIcon.className = 'fa fa-check';

                setTimeout(() => {
                    originalIcon.className = 'fa fa-copy';
                }, 1000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });
    });
});
