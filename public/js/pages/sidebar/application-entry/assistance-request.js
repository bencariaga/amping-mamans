const patientsContainer = document.getElementById('patientsContainer');
const patientsList = document.getElementById('patientsList');
const patientIdInput = document.getElementById('patientId');

document.addEventListener('DOMContentLoaded', function () {
    const applicantIdInput = document.getElementById('applicantId');
    const phoneInput = document.getElementById('phoneNumber');
    // const verifyBtn = document.getElementById('verifyBtn');
    const applicantNameInput = document.getElementById('applicantName');
    const phoneVerificationMessage = document.getElementById('phoneVerificationMessage');
    const assistanceRequestForm = document.getElementById('assistanceRequestForm');
    const serviceTypeDropdownBtn = document.getElementById('serviceTypeDropdownBtn');
    const serviceTypeInput = document.getElementById('serviceType');
    const affiliatePartnerDropdownBtn = document.getElementById('affiliatePartnerDropdownBtn');
    const affiliatePartnerInput = document.getElementById('affiliatePartner');
    const billedAmountInput = document.getElementById('billedAmount');
    const calculateBtn = document.getElementById('calculateBtn');
    const assistanceAmountInput = document.getElementById('assistanceAmount');
    const assistanceAmountRawInput = document.getElementById('assistanceAmountRaw');
    const tariffListVersionInput = document.getElementById('tariffListVersion');
    const tariffListVersionRawInput = document.getElementById('tariffListVersionRaw');
    const billedAmountMessage = document.getElementById('billedAmountMessage');
    const dateAppliedInput = document.getElementById('dateApplied');
    const dateToReapplyInput = document.getElementById('dateToReapply');
    const appliedAtRawInput = document.getElementById('appliedAtRaw');
    const reapplyAtRawInput = document.getElementById('reapplyAtRaw');


    function calculateDates() {
        const today = new Date();
        const reapplyDate = new Date();
        reapplyDate.setDate(today.getDate() + 90);
        dateAppliedInput.value = formatDateHuman(today);
        dateToReapplyInput.value = formatDateHuman(reapplyDate);
        appliedAtRawInput.value = toIsoDate(today);
        reapplyAtRawInput.value = toIsoDate(reapplyDate);
    }

    calculateDates();

    const serviceMenu = serviceTypeDropdownBtn.nextElementSibling;
    if (serviceMenu) {
        serviceMenu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();
                const value = this.getAttribute('data-value') || '';
                const text = this.textContent || '';
                serviceTypeDropdownBtn.textContent = text;
                serviceTypeInput.value = value;
            });
        });
    }

    const affiliateMenu = affiliatePartnerDropdownBtn.nextElementSibling;
    if (affiliateMenu) {
        affiliateMenu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();
                const value = this.getAttribute('data-value') || '';
                const text = this.textContent || '';
                affiliatePartnerDropdownBtn.textContent = text;
                affiliatePartnerInput.value = value;
            });
        });
    }

    // verifyBtn.addEventListener('click', async function () {
    //     const phoneNumber = phoneInput.value.trim();
    //     if (!phoneNumber) {
    //         showMessage(phoneVerificationMessage, 'Please enter a phone number.', 'error');
    //         return;
    //     }

    //     const candidates = normalizeInputPhoneCandidates(phoneNumber);

    //     try {
    //         const response = await fetch('/applications/verify-phone', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //             },
    //             body: JSON.stringify({ phone_number: phoneNumber, candidates: candidates })
    //         });

    //         const data = await response.json();

    //         if (response.ok) {
    //             applicantIdInput.value = data.applicant_id;
    //             applicantNameInput.value = data.applicant_name;
    //             showMessage(phoneVerificationMessage, data.message, 'success');

    //             if (data.patients && data.patients.length > 0) {
    //                 renderPatients(data.patients);
    //             }
    //         } else {
    //             applicantIdInput.value = '';
    //             applicantNameInput.value = '';
    //             patientsContainer.classList.add('d-none');
    //             const text = data.error || (data.errors && Object.values(data.errors).flat().join(' ')) || 'Verification failed.';
    //             showMessage(phoneVerificationMessage, text, 'error');
    //         }
    //     } catch (error) {
    //         showMessage(phoneVerificationMessage, 'An error occurred during verification.', 'error');
    //     }
    // });

    calculateBtn.addEventListener('click', async function () {
        const serviceId = serviceTypeInput.value;
        const billedAmount = billedAmountInput.value;
        if (!serviceId) {
            showMessage(billedAmountMessage, 'Please select a service type.', 'error');
            return;
        }
        if (!billedAmount || isNaN(billedAmount)) {
            showMessage(billedAmountMessage, 'Please enter a valid billed amount.', 'error');
            return;
        }

        try {
            const response = await fetch(`/applications/calculate-amount?service_id=${encodeURIComponent(serviceId)}&billed_amount=${encodeURIComponent(billedAmount)}`);
            const data = await response.json();
            if (response.ok) {
                assistanceAmountInput.value = `₱${Number(data.assistance_amount).toLocaleString()}`;
                assistanceAmountRawInput.value = Number(data.assistance_amount);
                tariffListVersionInput.value = data.tariff_list_version;
                tariffListVersionRawInput.value = data.tariff_list_version;
                showMessage(billedAmountMessage, 'Assistance amount has been calculated.', 'success');
            } else {
                assistanceAmountInput.value = '';
                assistanceAmountRawInput.value = '';
                tariffListVersionInput.value = '';
                tariffListVersionRawInput.value = '';
                const text = data.error || (data.errors && Object.values(data.errors).flat().join(' ')) || 'Calculation failed.';
                showMessage(billedAmountMessage, text, 'error');
            }
        } catch (error) {
            showMessage(billedAmountMessage, 'An error occurred during calculation.', 'error');
        }
    });

    assistanceRequestForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const payload = {};
        formData.forEach((value, key) => {
            payload[key] = value;
        });

        try {
            const response = await fetch('/applications/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (response.ok) {
                const applicationId = result.application_id;
                if(applicationId) {
                    processApplication(applicationId, 'authorize');
                }
                alert(result.message || 'Saved');
                if (result.redirect) {
                    window.location.href = result.redirect;
                }
            } else {
                const errMsg = result.error || (result.errors && Object.values(result.errors).flat().join(' ')) || 'Validation failed.';
                alert(errMsg);
            }
        } catch (error) {
            alert('Application has been added successfully.');
        }
    });
});

$(document).ready(function() {

    let applicantsDataWithPatients = []

    $('#applicant').select2({
        theme: 'bootstrap-5',
        placeholder: "Search for an applicant...",
        allowClear: true,
        ajax: {
            url: '/applications/search-applicant', // Your API endpoint
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function(applicant) {
                        applicantsDataWithPatients.push(applicant); // Store the full applicant data
                        return {
                            id: applicant.id,
                            text: applicant.text // or whatever field you want to display
                        };
                    })
                };
            },
            cache: true
        }
    });

    $('#applicant').on('select2:select', function (e) {
        var data = e.params.data; // Access data of the selected item
        document.getElementById('applicantId').value = data.id;
        const selectedApplicant = applicantsDataWithPatients.find(applicant => applicant.id === data.id);
        if (selectedApplicant && selectedApplicant.patients && selectedApplicant.patients.length > 0) {
            renderPatients(selectedApplicant.patients);
        }
    });

});

//reusable functions starts here

function showMessage(element, message, type) {
    element.textContent = message;
    element.className = `form-text fw-bold mt-1 d-block ${type === 'success' ? 'text-success' : 'text-danger'}`;
}

function formatDateHuman(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function toIsoDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function handlePatientCheckboxChange() {
    const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
    const allBoxes = document.querySelectorAll('.patient-checkbox');
    const isAnyChecked = checkedBoxes.length > 0;

    allBoxes.forEach(checkbox => {
        if (isAnyChecked && !checkbox.checked) {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    });

    if (isAnyChecked) {
        patientIdInput.value = checkedBoxes[0].value;
    } else {
        patientIdInput.value = '';
    }
}

function renderPatients(patients) {
    patientsList.innerHTML = '';

    patients.forEach(patient => {
        const fullName = `${patient.first_name} ${patient.middle_name} ${patient.last_name} ${patient.suffix}`.replace(/\s+/g, ' ').trim();
        const isApplicant = patient.is_applicant;

        const patientDiv = document.createElement('div');
        patientDiv.className = 'col-md-4 mb-2';
        patientDiv.innerHTML = `
            <div class="form-check">
                <input class="form-check-input patient-checkbox" type="checkbox"
                        value="${patient.patient_id}"
                        id="patient-${patient.patient_id}">
                <label class="form-check-label" for="patient-${patient.patient_id}">
                    ${fullName} ${isApplicant ? '(applicant)' : ''}
                </label>
            </div>
        `;

        patientsList.appendChild(patientDiv);
    });

    document.querySelectorAll('.patient-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', handlePatientCheckboxChange);
    });

    patientsContainer.classList.remove('d-none');
}
function normalizeInputPhoneCandidates(raw) {
    if (!raw) return [];
    const onlyDigits = raw.replace(/[^\d]/g, '');
    const candidates = new Set();

    candidates.add(raw);
    candidates.add(onlyDigits);

    if (onlyDigits.length >= 10) {
        if (onlyDigits.startsWith('63')) {
            const local = '0' + onlyDigits.slice(2);
            candidates.add(local);
            if (local.length >= 11) {
                const dash = local.slice(0, 4) + '-' + local.slice(4, 7) + '-' + local.slice(7);
                candidates.add(dash);
            }
            candidates.add('+63' + onlyDigits.slice(2));
        } else if (onlyDigits.startsWith('9')) {
            const local = '0' + onlyDigits;
            candidates.add(local);
            if (local.length >= 11) {
                const dash = local.slice(0, 4) + '-' + local.slice(4, 7) + '-' + local.slice(7);
                candidates.add(dash);
            }
        } else if (onlyDigits.startsWith('0')) {
            const local = onlyDigits;
            candidates.add(local);
            if (local.length >= 11) {
                const dash = local.slice(0, 4) + '-' + local.slice(4, 7) + '-' + local.slice(7);
                candidates.add(dash);
            }
            if (local.startsWith('0') && local.length >= 11) {
                const intl = '+63' + local.slice(1);
                candidates.add(intl);
            }
        } else {
            const local = onlyDigits;
            candidates.add(local);
        }
    }

    return Array.from(candidates).filter(Boolean);
}

function processApplication(applicationId, action) {
    const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;
    fetch(`/applications/${applicationId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Application processed:', data);
        }
    })
    .catch(error => {
        console.error('Error processing application:', error);
    })
}