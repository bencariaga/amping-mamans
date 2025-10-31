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

    setupDropdown('sortDropdownBtn', 'filter-sort-by', 'tariff-filter-form');
    setupDropdown('perPageDropdownBtn', 'filter-per-page', 'tariff-filter-form');

    window.openCreateModal = function () {
        window.showCreateTariffModal();
    };

    window.openViewModal = function (tariffListId) {
        window.showViewTariffModal(tariffListId);
    };

    window.openDeleteModal = function (tariffListId) {
        window.showDeleteTariffModal(tariffListId);
    };

    window.refreshTariffTable = function () {
        window.location.reload();
    };

    window.addRange = function (serviceId) {
        document.dispatchEvent(new CustomEvent('add-range-request', { detail: { serviceId: serviceId } }));
    };

    window.removeRange = function (serviceId, expRangeId) {
        document.dispatchEvent(new CustomEvent('remove-range-request', { detail: { serviceId: serviceId, expRangeId: expRangeId } }));
    };

    document.addEventListener('tariff-view-edit-opened', function () {
        initTariffEditModalFunctions(document);
    });

    document.addEventListener('tariff-ranges-updated', function () {
        initTariffEditModalFunctions(document);
    });

    window.formatNumber = function (n) {
        const num = String(n).replace(/[^0-9]/g, '');
        const parts = num.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    };

    window.stripCommas = function (n) {
        return String(n).replace(/,/g, '');
    };

    window.initRangeInputMasks = function (scope) {
        scope.querySelectorAll('.money-amount-input, .coverage-percent-input').forEach(input => {
            if (!input.hasAttribute('data-mask-initialized')) {
                input.addEventListener('input', function (e) {
                    let value = e.target.value;
                    const maxDigits = parseInt(e.target.getAttribute('data-max-digits'), 10) || 7;
                    const isCoverage = e.target.classList.contains('coverage-percent-input');

                    value = value.replace(/[^0-9]/g, '');

                    if (value.length > maxDigits) {
                        value = value.substring(0, maxDigits);
                    }

                    if (isCoverage) {
                        e.target.value = value;
                    } else {
                        e.target.value = window.formatNumber(value);
                    }

                    if (window.checkAllRangeOverlaps) {
                        window.checkAllRangeOverlaps();
                    }
                });

                input.setAttribute('data-mask-initialized', 'true');
            }
        });
    };

    window.initAddRemoveButtons = function (scope) {
        scope.querySelectorAll('.add-range-button').forEach(button => {
            if (!button.hasAttribute('data-event-initialized')) {
                button.addEventListener('click', function () {
                    const serviceId = this.getAttribute('data-service-id');

                    if (serviceId && window.addRange) {
                        window.addRange(serviceId);
                    }
                });

                button.setAttribute('data-event-initialized', 'true');
            }
        });

        scope.querySelectorAll('.remove-range-button').forEach(button => {
            if (!button.hasAttribute('data-event-initialized')) {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const serviceId = row.closest('.service-group').getAttribute('data-service-id');
                    const expRangeId = row.getAttribute('data-exp-range-id');

                    if (serviceId && expRangeId && window.removeRange) {
                        window.removeRange(serviceId, expRangeId, row);
                    }
                });

                button.setAttribute('data-event-initialized', 'true');
            }
        });
    };
});
