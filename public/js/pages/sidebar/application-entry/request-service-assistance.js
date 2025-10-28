document.addEventListener('DOMContentLoaded', function () {
    const applicantIdInput = document.getElementById('applicantId');
    const phoneInput = document.getElementById('phoneNumber');
    const verifyBtn = document.getElementById('verifyBtn');
    const applicantNameInput = document.getElementById('applicantName');
    const applicantFirstNameInput = document.getElementById('applicantFirstName');
    const applicantMiddleNameInput = document.getElementById('applicantMiddleName');
    const applicantLastNameInput = document.getElementById('applicantLastName');
    const applicantSuffixInput = document.getElementById('applicantSuffix');
    const patientFirstNameInput = document.getElementById('patientFirstName');
    const patientMiddleNameInput = document.getElementById('patientMiddleName');
    const patientLastNameInput = document.getElementById('patientLastName');
    const patientSuffixInput = document.getElementById('patientSuffix');
    const patientNameDropdownBtn = document.getElementById('patientNameDropdownBtn');
    const patientNameDropdownList = document.getElementById('patientNameDropdownList');
    const patientIdInput = document.getElementById('patientId');
    const patientNameHiddenInput = document.getElementById('patientName');
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
    const messageTemplateDropdownBtn = document.getElementById('messageTemplateDropdownBtn');
    const messageTemplateDropdownList = document.getElementById('messageTemplateDropdownList');
    const messagePreviewText = document.getElementById('messagePreviewText');
    const messageTemplateIdInput = document.getElementById('messageTemplateId');
    const autoApplicantData = JSON.parse(document.getElementById('autoApplicantData').value || '{}');
    const submitBtn = document.getElementById('submitBtn');

    let currentBaseTemplate = '';

    function showMessage(element, message, type) {
        element.textContent = message;
        element.className = `form-text fw-bold mt-1 d-block ${type === 'success' ? 'text-success' : 'text-danger'}`;

        setTimeout(() => {
            element.textContent = '';
            element.className = 'form-text mt-1 d-none';
        }, 3000);
    }

    function formatDateHuman(date) {
        const options = { year: 'numeric', 'month': 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    function toIsoDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function removeCommasFromNumber(numberString) {
        return numberString.replace(/,/g, '');
    }

    function updateMessagePreview() {
        if (!currentBaseTemplate) {
            messagePreviewText.value = 'Select a message template to see and edit the preview.';
            return;
        }

        let previewText = currentBaseTemplate;

        const applicantFirstName = applicantFirstNameInput.value.trim();
        const applicantMiddleName = applicantMiddleNameInput.value.trim();
        const applicantLastName = applicantLastNameInput.value.trim();
        const applicantSuffix = applicantSuffixInput.value.trim();
        const patientFirstName = patientFirstNameInput.value.trim();
        const patientMiddleName = patientMiddleNameInput.value.trim();
        const patientLastName = patientLastNameInput.value.trim();
        const patientSuffix = patientSuffixInput.value.trim();
        const serviceType = serviceTypeDropdownBtn.textContent.trim();
        const affiliatePartner = affiliatePartnerDropdownBtn.textContent.trim();
        const billedAmount = billedAmountInput.value;
        const assistanceAmount = assistanceAmountInput.value;
        const appliedAt = dateAppliedInput.value;
        const reapplyAt = dateToReapplyInput.value;

        const applicantFullName = [applicantFirstName, applicantMiddleName, applicantLastName, applicantSuffix].filter(Boolean).join(' ');
        const patientFullName = [patientFirstName, patientMiddleName, patientLastName, patientSuffix].filter(Boolean).join(' ');

        previewText = previewText.replace(/\[\$application->applicant->client->member->first_name\]/g, applicantFirstName);
        previewText = previewText.replace(/\[\$application->applicant->client->member->middle_name\]/g, applicantMiddleName);
        previewText = previewText.replace(/\[\$application->applicant->client->member->last_name\]/g, applicantLastName);
        previewText = previewText.replace(/\[\$application->applicant->client->member->suffix\]/g, applicantSuffix);
        previewText = previewText.replace(/\[\$application->applicant->client->member->full_name\]/g, applicantFullName);
        previewText = previewText.replace(/\[\$application->patient->client->member->first_name\]/g, patientFirstName);
        previewText = previewText.replace(/\[\$application->patient->client->member->middle_name\]/g, patientMiddleName);
        previewText = previewText.replace(/\[\$application->patient->client->member->last_name\]/g, patientLastName);
        previewText = previewText.replace(/\[\$application->patient->client->member->suffix\]/g, patientSuffix);
        previewText = previewText.replace(/\[\$application->patient->client->member->full_name\]/g, patientFullName);
        previewText = previewText.replace(/\[\$application->service_type\]/g, serviceType);
        previewText = previewText.replace(/\[\$application->affiliate_partner->affiliate_partner_name\]/g, affiliatePartner);
        previewText = previewText.replace(/\[\$application->billed_amount\]/g, billedAmount);
        previewText = previewText.replace(/\[\$application->assistance_amount\]/g, assistanceAmount);
        previewText = previewText.replace(/\[\$application->applied_at\]/g, appliedAt);
        previewText = previewText.replace(/\[\$application->reapply_at\]/g, reapplyAt);
        previewText = previewText.replace(/;/g, '\n');
        previewText = previewText.replace(/<br>/g, '\n');
        messagePreviewText.value = previewText;
    }

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

    if (Object.keys(autoApplicantData).length > 0 && autoApplicantData.phone_number) {
        showMessage(phoneVerificationMessage, 'Loading...', 'error');

        setTimeout(() => {
            phoneInput.value = autoApplicantData.phone_number;
            applicantIdInput.value = autoApplicantData.applicant_id;
            applicantNameInput.value = autoApplicantData.applicant_name;
            applicantFirstNameInput.value = autoApplicantData.applicant_first_name || '';
            applicantMiddleNameInput.value = autoApplicantData.applicant_middle_name || '';
            applicantLastNameInput.value = autoApplicantData.applicant_last_name || '';
            applicantSuffixInput.value = autoApplicantData.applicant_suffix || '';
            patientFirstNameInput.value = autoApplicantData.patient_first_name || '';
            patientMiddleNameInput.value = autoApplicantData.patient_middle_name || '';
            patientLastNameInput.value = autoApplicantData.patient_last_name || '';
            patientSuffixInput.value = autoApplicantData.patient_suffix || '';

            if (autoApplicantData.patient_id && autoApplicantData.patient_name) {
                patientIdInput.value = autoApplicantData.patient_id;
                patientNameHiddenInput.value = autoApplicantData.patient_name;
                patientNameDropdownBtn.textContent = autoApplicantData.patient_name;
            }

            patientNameDropdownBtn.disabled = false;
            patientNameDropdownBtn.textContent = 'Select a patient.';

            if (autoApplicantData.patients && autoApplicantData.patients.length > 0) {
                populatePatientDropdown(autoApplicantData.patients, autoApplicantData.patient_id);
            }

            showMessage(phoneVerificationMessage, 'Applicant data automatically loaded.', 'success');
        }, 250);
    }

    function populatePatientDropdown(patients, selectedPatientId = null) {
        patientNameDropdownList.innerHTML = '';
        const defaultItem = document.createElement('li');
        defaultItem.innerHTML = '<a class="dropdown-item" href="#" data-value="" data-text="">Select a patient.</a>';
        patientNameDropdownList.appendChild(defaultItem);

        patients.forEach(patient => {
            const item = document.createElement('li');
            const link = document.createElement('a');
            link.className = 'dropdown-item';
            link.href = '#';
            link.setAttribute('data-value', patient.patient_id);
            link.setAttribute('data-text', patient.patient_name);
            link.setAttribute('data-first-name', patient.patient_first_name || '');
            link.setAttribute('data-middle-name', patient.patient_middle_name || '');
            link.setAttribute('data-last-name', patient.patient_last_name || '');
            link.setAttribute('data-suffix', patient.patient_suffix || '');
            link.textContent = patient.patient_name;

            if (selectedPatientId && patient.patient_id === selectedPatientId) {
                patientNameDropdownBtn.textContent = patient.patient_name;
                patientIdInput.value = patient.patient_id;
                patientNameHiddenInput.value = patient.patient_name;
                patientFirstNameInput.value = patient.patient_first_name || '';
                patientMiddleNameInput.value = patient.patient_middle_name || '';
                patientLastNameInput.value = patient.patient_last_name || '';
                patientSuffixInput.value = patient.patient_suffix || '';
            }

            link.addEventListener('click', function (event) {
                event.preventDefault();
                const value = this.getAttribute('data-value') || '';
                const text = this.getAttribute('data-text') || '';
                const firstName = this.getAttribute('data-first-name') || '';
                const middleName = this.getAttribute('data-middle-name') || '';
                const lastName = this.getAttribute('data-last-name') || '';
                const suffix = this.getAttribute('data-suffix') || '';
                patientNameDropdownBtn.textContent = text;
                patientIdInput.value = value;
                patientNameHiddenInput.value = text;
                patientFirstNameInput.value = firstName;
                patientMiddleNameInput.value = middleName;
                patientLastNameInput.value = lastName;
                patientSuffixInput.value = suffix;
                updateMessagePreview();
            });

            item.appendChild(link);
            patientNameDropdownList.appendChild(item);
        });

        patientNameDropdownBtn.disabled = false;

        if (!patientIdInput.value) {
            patientNameDropdownBtn.textContent = 'Select a patient.';
        }
    }

    const serviceMenu = serviceTypeDropdownBtn.nextElementSibling;

    if (serviceMenu) {
        serviceMenu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();

                const value = this.getAttribute('data-value') || '';
                const text = this.textContent || '';

                serviceTypeDropdownBtn.textContent = text;
                serviceTypeInput.value = value;
                updateMessagePreview();
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

    if (messageTemplateDropdownList) {
        messageTemplateDropdownList.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();

                const value = this.getAttribute('data-value') || '';
                const text = this.getAttribute('data-text') || '';
                const title = this.textContent || '';

                messageTemplateDropdownBtn.textContent = title;
                messageTemplateIdInput.value = value;
                currentBaseTemplate = text;
                updateMessagePreview();
            });
        });
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

    phoneInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');

        if (value.length > 11) {
            value = value.substring(0, 11);
        }

        if (value.length > 0) {
            if (value.startsWith('0')) {
                if (value.length <= 4) {
                    this.value = value;
                } else if (value.length <= 7) {
                    this.value = value.substring(0, 4) + '-' + value.substring(4);
                } else {
                    this.value = value.substring(0, 4) + '-' + value.substring(4, 7) + '-' + value.substring(7);
                }
            } else {
                this.value = value;
            }
        } else {
            this.value = '';
        }
    });

    billedAmountInput.addEventListener('input', function () {
        let value = this.value.replace(/,/g, '');

        if (!isNaN(value) && value !== '') {
            this.value = formatNumberWithCommas(value);
        }

        updateMessagePreview();
    });

    verifyBtn.addEventListener('click', async function () {
        const phoneNumber = phoneInput.value.trim();

        if (!phoneNumber) {
            showMessage(phoneVerificationMessage, 'Please enter a phone number.', 'error');
            return;
        }

        const candidates = normalizeInputPhoneCandidates(phoneNumber);
        showMessage(phoneVerificationMessage, 'Verifying...', 'error');

        try {
            const response = await fetch('/applications/verify-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ phone_number: phoneNumber, candidates: candidates })
            });

            const data = await response.json();

            setTimeout(() => {
                if (response.ok) {
                    applicantIdInput.value = data.applicant_id;
                    applicantNameInput.value = data.applicant_name;
                    applicantFirstNameInput.value = data.applicant_first_name || '';
                    applicantMiddleNameInput.value = data.applicant_middle_name || '';
                    applicantLastNameInput.value = data.applicant_last_name || '';
                    applicantSuffixInput.value = data.applicant_suffix || '';
                    patientIdInput.value = data.patient_id || '';
                    patientNameHiddenInput.value = data.patient_name || '';
                    patientFirstNameInput.value = data.patient_first_name || '';
                    patientMiddleNameInput.value = data.patient_middle_name || '';
                    patientLastNameInput.value = data.patient_last_name || '';
                    patientSuffixInput.value = data.patient_suffix || '';
                    patientNameDropdownBtn.textContent = data.patient_name || 'Select a patient.';
                    patientNameDropdownBtn.disabled = false;
                    populatePatientDropdown(data.patients, data.patient_id);
                    showMessage(phoneVerificationMessage, data.message, 'success');
                } else {
                    applicantIdInput.value = '';
                    applicantNameInput.value = '';
                    patientIdInput.value = '';
                    patientNameHiddenInput.value = '';
                    patientNameDropdownBtn.textContent = 'Verify the phone number first.';
                    patientNameDropdownBtn.disabled = true;
                    const text = data.error || (data.errors && Object.values(data.errors).flat().join(' ')) || 'Verification failed.';
                    showMessage(phoneVerificationMessage, text, 'error');
                }
            }, 250);
        } catch (error) {
            setTimeout(() => {
                showMessage(phoneVerificationMessage, 'An error occurred during verification.', 'error');
            }, 250);
        }
    });

    calculateBtn.addEventListener('click', async function () {
        const serviceId = serviceTypeInput.value;
        const billedAmount = removeCommasFromNumber(billedAmountInput.value);

        if (!serviceId) {
            showMessage(billedAmountMessage, 'Please select a service type.', 'error');
            return;
        }

        if (!billedAmount || isNaN(billedAmount)) {
            showMessage(billedAmountMessage, 'Please enter a valid billed amount.', 'error');
            return;
        }

        showMessage(billedAmountMessage, 'Calculating from the latest active tariff version...', 'error');

        try {
            const response = await fetch(`/applications/calculate-amount?service_id=${encodeURIComponent(serviceId)}&billed_amount=${encodeURIComponent(billedAmount)}`);
            const data = await response.json();

            setTimeout(() => {
                if (response.ok) {
                    assistanceAmountInput.value = formatNumberWithCommas(data.assistance_amount);
                    assistanceAmountRawInput.value = Number(data.assistance_amount);
                    tariffListVersionInput.value = data.tariff_list_version;
                    tariffListVersionRawInput.value = data.tariff_list_version;
                    showMessage(billedAmountMessage, 'Assistance amount has been calculated.', 'success');
                    updateMessagePreview();
                } else {
                    assistanceAmountInput.value = '';
                    assistanceAmountRawInput.value = '';
                    tariffListVersionInput.value = '';
                    tariffListVersionRawInput.value = '';
                    const text = data.error || (data.errors && Object.values(data.errors).flat().join(' ')) || 'Calculation failed.';
                    showMessage(billedAmountMessage, text, 'error');
                }
            }, 250);
        } catch (error) {
            setTimeout(() => {
                showMessage(billedAmountMessage, 'An error occurred during calculation.', 'error');
            }, 250);
        }
    });

    async function handleAssistanceSubmission(form) {
        const formData = new FormData(form);
        const payload = {};

        formData.forEach((value, key) => {
            if (key === 'billed_amount') {
                payload[key] = removeCommasFromNumber(value);
            } else if (key === 'message_text') {
                payload[key] = value.replace(/\n/g, '<br>');
            } else {
                payload[key] = value;
            }
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
                alert(result.message || 'Application submitted successfully!');
                if (result.redirect) {
                    window.location.href = result.redirect;
                }
            } else {
                const errMsg = result.error || (result.errors && Object.values(result.errors).flat().join(' ')) || 'Validation failed.';
                alert(errMsg);
            }
        } catch (error) {
            alert('An error occurred while submitting the application.');
        }
    }

    assistanceRequestForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        await handleAssistanceSubmission(this);
    });

    if (submitBtn) {
        submitBtn.addEventListener('click', async function (event) {
            event.preventDefault();
            await handleAssistanceSubmission(assistanceRequestForm);
        });
    }
});
