document.addEventListener('DOMContentLoaded', function () {
    initializeDatePickers();
    updateRemoveButtonsState();
    initializeCustomDropdowns();
    initializeClientSelectionModal();
    initializeVerificationButtons();
    setupFormSubmissionCleanup();
    formatExistingMonthlyIncomes();
    setClientFieldReadonlyState();

    document.querySelectorAll('.select-client-btn').forEach(button => {
        button.addEventListener('click', function () {
            const index = this.getAttribute('data-index');
            if (index !== null && index !== '') {
                window.activeRowIndex = parseInt(index);
            }
        });
    });

    Livewire.hook('element.updated', () => {
        initializeDatePickers();
        updateRemoveButtonsState();
        initializeCustomDropdowns();
        initializeVerificationButtons();
        formatExistingMonthlyIncomes();
        setClientFieldReadonlyState();
    });
});

let activeRowIndex = null;

const APPLICANT_READ_ONLY_FIELDS = [
    'last-name-input',
    'first-name-input',
    'middle-name-input',
    'suffix-hidden-input',
    'birthdate-input',
    'age-input',
    'civil-status-hidden-input',
    'occupation-hidden-input',
    'monthly-income-input'
];

const PATIENT_READ_ONLY_FIELDS = [
    'last-name-input',
    'first-name-input',
    'middle-name-input',
    'suffix-hidden-input',
    'birthdate-input',
    'age-input'
];

function formatExistingMonthlyIncomes() {
    document.querySelectorAll('.monthly-income-input').forEach(input => {
        if (input.value && !isNaN(input.value)) {
            input.value = formatNumber(parseInt(input.value));
        }
    });
}

function initializeDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach(input => {
        input.removeEventListener('change', handleDateChange);
        input.addEventListener('change', handleDateChange);

        if (input.value) {
            calculateAge(input);
        }
    });
}

function handleDateChange(event) {
    calculateAge(event.target);
}

function calculateAge(input) {
    const dob = input.value;
    const ageDisplay = input.closest('td').querySelector('.age-display');

    if (dob && ageDisplay) {
        const today = new Date();
        const birthDate = new Date(dob);
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        ageDisplay.textContent = age > 0 ? `${age} yrs` : '—';
    } else if (ageDisplay) {
        ageDisplay.textContent = '—';
    }
}

function initializeCustomDropdowns() {
    document.querySelectorAll('.occupation-dropdown').forEach(dropdown => {
        const input = dropdown.previousElementSibling;
        if (!input || !input.classList.contains('occupation-input')) {
            return;
        }

        dropdown.innerHTML = '';

        OCCUPATIONS_LIST.forEach(occupation => {
            const option = document.createElement('a');
            option.className = 'dropdown-item';
            option.href = '#';
            option.textContent = occupation;
            option.addEventListener('click', function (e) {
                e.preventDefault();
                input.value = occupation;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
            dropdown.appendChild(option);
        });
    });
}

function formatFullName({ first_name, middle_name, last_name, suffix }) {
    const lastNamePart = last_name ? `${last_name},` : null;
    const firstNamePart = [first_name, middle_name, suffix].filter(Boolean).join(' ');
    return [lastNamePart, firstNamePart].filter(Boolean).join(' ');
}

function initializeClientSelectionModal() {
    const searchInput = document.getElementById('clientSearchInput');
    const searchResults = document.getElementById('clientSearchResults');
    const selectClientModal = document.getElementById('selectClientModal');
    let searchTimeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '<li class="list-group-item text-center muted-text fw-semibold py-3">Start typing to see results.<br>(Minimum: 2 characters)</li>';
            return;
        }

        searchResults.innerHTML = '<li class="list-group-item text-center fw-semibold py-3"><i class="fas fa-spinner fa-spin"></i> Searching...</li>';

        searchTimeout = setTimeout(() => {
            performClientSearch(query);
        }, 500);
    });

    selectClientModal.addEventListener('click', function (e) {
        if (e.target.closest('.client-search-item')) {
            const listItem = e.target.closest('.client-search-item');
            handleClientSelection(listItem);
        }
    });

    selectClientModal.addEventListener('hidden.bs.modal', function () {
        searchInput.value = '';
        searchResults.innerHTML = '<li class="list-group-item text-center muted-text fw-semibold py-3">Start typing to see results.<br>(Minimum: 2 characters)</li>';
    });
}

function performClientSearch(query) {
    const searchResults = document.getElementById('clientSearchResults');

    fetch(`${MEMBER_SEARCH_URL}?search=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not OK.');
            }
            return response.json();
        })
        .then(data => {
            if (data.results.length === 0) {
                searchResults.innerHTML = '<li class="list-group-item text-center text-danger fw-semibold py-3">No clients found matching your search.</li>';
                return;
            }

            const resultsHtml = data.results.map(client => {
                const clientTypeBadge = client.is_applicant ? '<span class="badge bg-primary ms-2">APPLICANT</span>' : client.is_patient ? '<span class="badge bg-secondary ms-2">PATIENT</span>' : '';
                const formattedName = formatFullName(client);

                return `
                    <li class="list-group-item list-group-item-action client-search-item"
                        data-client-id="${client.client_id}"
                        data-member-id="${client.member_id}"
                        data-first-name="${client.first_name}"
                        data-middle-name="${client.middle_name || ''}"
                        data-last-name="${client.last_name}"
                        data-suffix="${client.suffix || ''}"
                        data-full-name="${formattedName}"
                        data-gender="${client.gender}"
                        data-birth-date="${client.birth_date}"
                        data-monthly-income="${client.monthly_income || 0}"
                        data-is-applicant="${client.is_applicant}"
                        data-is-patient="${client.is_patient}"
                    >
                        <div class="w-100" style="cursor: pointer;">
                            <h6 class="mb-1 fw-bold d-flex justify-content-between">${formattedName} ${clientTypeBadge}</h6>
                        </div>
                    </li>
                `;
            }).join('');

            searchResults.innerHTML = resultsHtml;
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResults.innerHTML = '<li class="list-group-item text-center text-danger fw-semibold py-3">An error occurred during search.</li>';
        });
}

function handleClientSelection(listItem) {
    const memberId = listItem.getAttribute('data-member-id');
    const clientId = listItem.getAttribute('data-client-id');
    const fullName = listItem.getAttribute('data-full-name');
    const firstName = listItem.getAttribute('data-first-name');
    const middleName = listItem.getAttribute('data-middle-name');
    const lastName = listItem.getAttribute('data-last-name');
    const suffix = listItem.getAttribute('data-suffix');
    const gender = listItem.getAttribute('data-gender');
    const birthDate = listItem.getAttribute('data-birth-date');
    const monthlyIncome = listItem.getAttribute('data-monthly-income');
    const isApplicant = listItem.getAttribute('data-is-applicant') === '1';
    const isPatient = listItem.getAttribute('data-is-patient') === '1';
    const clientType = isApplicant ? 'APPLICANT' : isPatient ? 'PATIENT' : 'HOUSEHOLD_MEMBER';

    if (window.activeRowIndex !== null) {
        const row = document.querySelector(`[wire\\:key="member-${window.activeRowIndex}"]`);

        if (row) {
            row.querySelector('.client-id-input').value = clientId;
            row.querySelector('.member-id-input').value = memberId;

            row.querySelector('.last-name-input').value = lastName;
            row.querySelector('.first-name-input').value = firstName;
            row.querySelector('.middle-name-input').value = middleName;
            row.querySelector('.suffix-hidden-input').value = suffix;

            const suffixBtn = row.querySelector('.suffix-dropdown button');
            if (suffixBtn) {
                suffixBtn.textContent = suffix || '—';
            }

            row.querySelector('.birthdate-input').value = birthDate;
            row.querySelector('.monthly-income-input').value = monthlyIncome;

            const birthDateInput = row.querySelector('.birthdate-input');
            if (birthDateInput) {
                calculateAge(birthDateInput);
            }

            row.querySelectorAll('input:not([type="hidden"]), select').forEach(input => {
                input.disabled = false;
                input.removeAttribute('readonly');
                input.closest('.custom-select-wrapper')?.querySelectorAll('.dropdown').forEach(d => {
                    d.removeAttribute('data-readonly');
                });
            });

            let readOnlyFields = [];
            if (isApplicant) {
                readOnlyFields = APPLICANT_READ_ONLY_FIELDS;
            } else if (isPatient) {
                readOnlyFields = PATIENT_READ_ONLY_FIELDS;
            }

            readOnlyFields.forEach(className => {
                const input = row.querySelector(`.${className}`);
                if (input) {
                    if (input.tagName === 'INPUT' && input.type !== 'hidden') {
                        input.setAttribute('readonly', true);
                    } else if (input.classList.contains('suffix-hidden-input') || input.classList.contains('civil-status-hidden-input') || input.classList.contains('occupation-hidden-input')) {
                        const dropdownWrapper = input.closest('.custom-dropdown');
                        const dropdownBtn = dropdownWrapper ? dropdownWrapper.querySelector('button') : null;

                        if (dropdownBtn) {
                            dropdownBtn.setAttribute('disabled', true);
                            dropdownWrapper.setAttribute('data-readonly', 'true');
                        }
                    }
                }
            });

            row.querySelector('.select-client-btn').setAttribute('disabled', true);

            initializeVerificationButtons();
            formatExistingMonthlyIncomes();

            Livewire.dispatch('clientSelected', {
                index: window.activeRowIndex, clientData: {
                    client_id: clientId,
                    client_type: clientType,
                    member: {
                        last_name: lastName,
                        first_name: firstName,
                        middle_name: middleName,
                        suffix: suffix,
                    },
                    birthdate: birthDate,
                    monthly_income: monthlyIncome,
                }
            });
        }
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById('selectClientModal'));
    modal.hide();
}

function setClientFieldReadonlyState() {
    document.querySelectorAll('[wire\\:key^="member-"]').forEach(row => {
        const isClient = row.querySelector('.client-id-input')?.value !== '';
        if (!isClient) return;

        const clientType = row.getAttribute('data-client-type');

        let readOnlyFields = [];
        if (clientType === 'APPLICANT') {
            readOnlyFields = APPLICANT_READ_ONLY_FIELDS;
        } else if (clientType === 'PATIENT') {
            readOnlyFields = PATIENT_READ_ONLY_FIELDS;
        }

        readOnlyFields.forEach(className => {
            const input = row.querySelector(`.${className}`);
            if (input) {
                if (input.tagName === 'INPUT' && input.type !== 'hidden') {
                    input.setAttribute('readonly', true);
                } else if (input.classList.contains('suffix-hidden-input') || input.classList.contains('civil-status-hidden-input') || input.classList.contains('occupation-hidden-input')) {
                    const dropdownWrapper = input.closest('.custom-dropdown');
                    const dropdownBtn = dropdownWrapper ? dropdownWrapper.querySelector('button') : null;

                    if (dropdownBtn) {
                        dropdownBtn.setAttribute('disabled', true);
                        dropdownWrapper.setAttribute('data-readonly', 'true');
                    }
                }
            }
        });

        const selectClientBtn = row.querySelector('.select-client-btn');
        if (selectClientBtn) {
            selectClientBtn.setAttribute('disabled', true);
        }
    });
}

function checkNameExistence(lastName, firstName, middleName = null, inputElement) {
    const url = middleName ? VERIFY_FULL_NAME_URL : VERIFY_NAME_URL;
    const data = { last_name: lastName, first_name: firstName, middle_name: middleName };
    const csrfToken = CSRF_TOKEN;

    const btn = inputElement.closest('td').querySelector('.btn-verification-feedback');
    const initialText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    btn.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.exists) {
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Exists';
            } else {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
                btn.innerHTML = initialText;
            }
            btn.disabled = false;
        })
        .catch(error => {
            console.error('Verification error:', error);
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            btn.innerHTML = initialText;
            btn.disabled = false;
            console.error('An error occurred during verification.');
        });
}

function initializeVerificationButtons() {
    document.querySelectorAll('.verify-first-name-btn, .verify-middle-name-btn').forEach(btn => {
        const row = btn.closest('tr');
        const isClient = row.querySelector('.client-id-input').value !== '';

        if (isClient) {
            btn.disabled = true;
        } else {
            btn.disabled = false;
        }
    });
}

function updateRemoveButtonsState() {
    const removeButtons = document.querySelectorAll('.remove-member-btn');
    if (removeButtons.length <= 1) {
        removeButtons.forEach(button => button.disabled = true);
    } else {
        removeButtons.forEach(button => button.disabled = false);
    }
}

function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

window.formatMonthlyIncome = function (input) {
    let value = input.value.replace(/[^0-9]/g, '');

    if (value.length > 0) {
        value = Math.min(parseInt(value, 10), 9999999).toString();
        input.value = formatNumber(value);
    } else {
        input.value = '';
    }
}

function setupFormSubmissionCleanup() {
    const form = document.getElementById('householdProfileForm');
    form.removeEventListener('submit', formSubmissionCleanup);
    form.addEventListener('submit', formSubmissionCleanup);
}

function formSubmissionCleanup(e) {
    document.querySelectorAll('.monthly-income-input').forEach(input => {
        const rawValue = input.value.replace(/[^0-9]/g, '');
        input.value = rawValue;
    });
}
