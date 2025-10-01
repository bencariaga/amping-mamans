document.addEventListener('DOMContentLoaded', () => {
    const sponsorsModalOverlay = document.getElementById('sponsors-modal-overlay');
    const sponsorsModalClose = document.getElementById('sponsors-modal-close');
    const addSponsorForm = document.getElementById('add-sponsor-form');
    const newSponsorTypeDropdownBtn = document.getElementById('sponsor-type-dropdown');
    const newSponsorTypeInput = document.getElementById('sponsor-type-input');
    const newSponsorTypeError = document.getElementById('sponsor-type-error');
    const newSponsorFirstNameInput = document.getElementById('sponsor-first-name');
    const newSponsorMiddleNameInput = document.getElementById('sponsor-middle-name');
    const newSponsorLastNameInput = document.getElementById('sponsor-last-name');
    const newSponsorSuffixInput = document.getElementById('sponsor-suffix-input');
    const newSponsorSuffixDropdownBtn = document.getElementById('sponsor-suffix-dropdown');
    const newDesignationInput = document.getElementById('designation');
    const newOrganizationNameInput = document.getElementById('organization-name');
    const newSponsorFirstNameError = document.getElementById('sponsor-first-name-error');
    const newSponsorLastNameError = document.getElementById('sponsor-last-name-error');
    const sponsorsList = document.getElementById('sponsors-list');
    const confirmChangesBtn = document.getElementById('confirm-sponsors-changes');
    const cancelChangesBtn = document.getElementById('cancel-sponsors-changes');

    let sponsors = [];

    let pendingChanges = {
        added: [],
        edited: [],
        deleted: []
    };

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');
    };

    const showValidationError = (element, message) => {
        element.textContent = message;
        element.style.display = 'block';
    };

    const hideValidationError = (element) => {
        if (!element) return;
        element.textContent = '';
        element.style.display = 'none';
    };

    const setupDropdown = (buttonId, inputId, parentElement) => {
        const dropdownButton = parentElement.querySelector(`#${buttonId}`) || document.getElementById(buttonId);
        const hiddenInput = parentElement.querySelector(`#${inputId}`) || document.getElementById(inputId);

        if (!dropdownButton || !hiddenInput) return;

        const dropdownMenu = dropdownButton.nextElementSibling;

        if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) return;

        dropdownMenu.addEventListener('click', function (e) {
            const item = e.target.closest('.dropdown-item');
            if (!item) return;
            e.preventDefault();

            const val = item.getAttribute('data-value') || '';
            hiddenInput.value = val;
            dropdownButton.textContent = item.textContent.trim();

            dropdownMenu.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });

        dropdownButton.addEventListener('show.bs.dropdown', () => {
            dropdownButton.classList.add('rotated');
        });

        dropdownButton.addEventListener('hide.bs.dropdown', () => {
            dropdownButton.classList.remove('rotated');
        });

        const initialVal = hiddenInput.value;
        let found = false;

        if (initialVal) {
            dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
                if (item.getAttribute('data-value') === initialVal) {
                    dropdownButton.textContent = item.textContent.trim();
                    item.classList.add('active');
                    found = true;
                } else {
                    item.classList.remove('active');
                }
            });
        }

        if (!found) {
            const serverActive = dropdownMenu.querySelector('.dropdown-item.active');

            if (serverActive) {
                dropdownButton.textContent = serverActive.textContent.trim();
            } else {
                const first = dropdownMenu.querySelector('.dropdown-item');
                if (first) dropdownButton.textContent = first.textContent.trim();
            }
        }
    };

    const renderSponsors = () => {
        sponsorsList.innerHTML = '';

        sponsors.forEach(sponsor => {
            if (sponsor.status === 'deleted') return;

            const listItem = document.createElement('li');
            listItem.id = `sponsor-item-${sponsor.sponsor_id}`;
            listItem.classList.add('sponsor-item');

            if (sponsor.status === 'new') {
                listItem.classList.add('item');
            } else if (sponsor.status === 'edited') {
                listItem.classList.add('edited-item');
            }

            if (sponsor.editing) {
                const nameParts = {
                    firstName: sponsor.first_name || '',
                    middleName: sponsor.middle_name || '',
                    lastName: sponsor.last_name || '',
                    suffix: sponsor.suffix || ''
                };

                listItem.innerHTML = `
                    <form class="editing-form mt-4" data-id="${sponsor.sponsor_id}">
                        <div class="row gx-3 gy-3 mb-1">
                            <div class="form-group col-md-4">
                                <label for="editing-sponsor-first-name-${sponsor.sponsor_id}" class="fw-bold">First Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="editing-sponsor-first-name-${sponsor.sponsor_id}" value="${nameParts.firstName || ''}" class="editing-sponsor-first-name form-control">
                                <span class="error-message editing-sponsor-first-name-error" style="display: none;"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="editing-sponsor-middle-name-${sponsor.sponsor_id}" class="fw-bold">Middle Name</label>
                                <input type="text" id="editing-sponsor-middle-name-${sponsor.sponsor_id}" value="${nameParts.middleName || ''}" class="editing-sponsor-middle-name form-control">
                                <span class="error-message editing-sponsor-middle-name-error" style="display: none;"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="editing-sponsor-last-name-${sponsor.sponsor_id}" class="fw-bold">Last Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="editing-sponsor-last-name-${sponsor.sponsor_id}" value="${nameParts.lastName || ''}" class="editing-sponsor-last-name form-control">
                                <span class="error-message editing-sponsor-last-name-error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="row gx-3 gy-3 mb-1">
                            <div class="form-group col-md-2">
                                <label for="editing-sponsor-suffix-dropdown-${sponsor.sponsor_id}" class="fw-bold">Suffix</label>
                                <div class="dropdown" id="editing-sponsor-suffix-${sponsor.sponsor_id}">
                                    <button class="btn dropdown-toggle w-100 editing-sponsor-suffix-dropdown" type="button" id="editing-sponsor-suffix-dropdown-${sponsor.sponsor_id}" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <ul class="dropdown-menu w-100 editing-sponsor-suffix-dropdown-menu" aria-labelledby="editing-sponsor-suffix-dropdown-${sponsor.sponsor_id}">
                                        <li><a class="dropdown-item" href="#" data-value=""></a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Jr.">Jr.</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Sr.">Sr.</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="II">II</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="III">III</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="IV">IV</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="V">V</a></li>
                                    </ul>
                                    <input type="hidden" id="editing-sponsor-suffix-input-${sponsor.sponsor_id}" class="editing-sponsor-suffix-input" value="${nameParts.suffix || ''}">
                                </div>
                                <span class="error-message editing-sponsor-suffix-error" style="display: none;"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="editing-sponsor-type-dropdown-${sponsor.sponsor_id}" class="fw-bold">Sponsor Type <span class="required-asterisk">*</span></label>
                                <div class="dropdown" id="editing-sponsor-type-${sponsor.sponsor_id}">
                                    <button class="btn dropdown-toggle w-100 editing-sponsor-type-dropdown" type="button" id="editing-sponsor-type-dropdown-${sponsor.sponsor_id}" data-bs-toggle="dropdown" aria-expanded="false">${sponsor.sponsor_type || ''}</button>
                                    <ul class="dropdown-menu w-100 editing-sponsor-type-dropdown-menu" aria-labelledby="editing-sponsor-type-dropdown-${sponsor.sponsor_id}">
                                        <li><a class="dropdown-item" href="#" data-value="Politician">Politician</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Business Owner">Business Owner</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Non-Governmental">Non-Governmental</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Non-Profit">Non-Profit</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Other">Other</a></li>
                                    </ul>
                                    <input type="hidden" id="editing-sponsor-type-input-${sponsor.sponsor_id}" class="editing-sponsor-type-input" value="${sponsor.sponsor_type || ''}">
                                </div>
                                <span class="error-message editing-sponsor-type-error" style="display: none;"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editing-designation-${sponsor.sponsor_id}" class="fw-bold">Designation</label>
                                <input type="text" id="editing-designation-${sponsor.sponsor_id}" value="${sponsor.designation || ''}" class="editing-designation form-control">
                                <span class="error-message editing-designation-error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="row gx-3 gy-3 mb-1">
                            <div class="form-group col-md-12">
                                <label for="editing-organization-name-${sponsor.sponsor_id}" class="fw-bold">Organization Name</label>
                                <input type="text" id="editing-organization-name-${sponsor.sponsor_id}" value="${sponsor.organization_name || ''}" class="editing-organization-name form-control">
                                <span class="error-message editing-organization-name-error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-secondary cancel-editing" data-id="${sponsor.sponsor_id}">CANCEL</button>
                            <button type="button" class="btn btn-sm btn-success save-editing" data-id="${sponsor.sponsor_id}">SAVE</button>
                        </div>
                    </form>
                `;
                setupDropdown(`editing-sponsor-type-dropdown-${sponsor.sponsor_id}`, `editing-sponsor-type-input-${sponsor.sponsor_id}`, listItem);
                setupDropdown(`editing-sponsor-suffix-dropdown-${sponsor.sponsor_id}`, `editing-sponsor-suffix-input-${sponsor.sponsor_id}`, listItem);
            } else {
                let infoText = sponsor.sponsor_type || '';
                if (sponsor.designation && sponsor.designation.trim() !== '') {
                    if (infoText) {
                        infoText += ', ';
                    }
                    infoText += sponsor.designation;
                }

                listItem.innerHTML = `
                    <div class="sponsor-details">
                        <div class="sponsor-name">${sponsor.sponsor_name || ''}</div>
                        <div><span class="sponsor-info-text">${infoText}</span></div>
                        <div><span class="organization-name">${sponsor.organization_name || ''}</span></div>
                    </div>
                    <div class="sponsor-action-buttons">
                        <button type="button" class="btn btn-info btn-sm btn-primary edit-sponsor" data-id="${sponsor.sponsor_id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.35-.350.106-.106-.35-.35-.106a.5.5 0 0 1 .106-.35l.35-.106zM6.5 13H5v1.5a.5.5 0 0 1-.5.5h-.5a.5.5 0 0 1-.5-.5V13h-.5a.5.5 0 0 1-.5-.5v-.5a.5.5 0 0 1 .5-.5h.5V11a.5.5 0 0 1 .5-.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 0 1-.5.5z"/>
                            </svg>
                        </button>
                        <button type="button" class="btn btn-info btn-sm btn-danger delete-sponsor" data-id="${sponsor.sponsor_id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                            </svg>
                        </button>
                    </div>
                `;
            }

            sponsorsList.appendChild(listItem);
        });

        document.querySelectorAll('.save-editing').forEach(btn => {
            btn.removeEventListener('click', handleSaveEditing);
            btn.addEventListener('click', handleSaveEditing);
        });

        document.querySelectorAll('.cancel-editing').forEach(btn => {
            btn.removeEventListener('click', handleCancelEditing);
            btn.addEventListener('click', handleCancelEditing);
        });
    };

    const fetchSponsors = async () => {
        try {
            const response = await fetch('/api/sponsors', { credentials: 'same-origin' });
            const data = await response.json();
            const rawSponsors = data.sponsors || [];
            sponsors = rawSponsors.map(s => {
                const first = s.first_name || '';
                const middle = s.middle_name || '';
                const last = s.last_name || '';
                const suffix = s.suffix || '';
                const computedName = `${first} ${middle} ${last} ${suffix}`.replace(/\s+/g, ' ').trim();
                return {
                    sponsor_id: s.sponsor_id !== undefined ? s.sponsor_id : (s.id !== undefined ? s.id : `s-${Date.now()}`),
                    sponsor_name: s.sponsor_name && s.sponsor_name.trim() ? s.sponsor_name : computedName,
                    first_name: first,
                    middle_name: middle,
                    last_name: last,
                    suffix: suffix,
                    sponsor_type: s.sponsor_type || '',
                    designation: s.designation || '',
                    organization_name: s.organization_name || '',
                    status: 'initial',
                    editing: false
                };
            });
            renderSponsors();
        } catch (error) {
            console.error('Error fetching sponsors:', error);
        }
    };

    const handleAddSponsor = async (event) => {
        event.preventDefault();
        const sponsorFirstName = newSponsorFirstNameInput.value.trim();
        const sponsorMiddleName = newSponsorMiddleNameInput.value.trim();
        const sponsorLastName = newSponsorLastNameInput.value.trim();
        const sponsorSuffix = newSponsorSuffixInput.value.trim();
        const sponsorType = newSponsorTypeInput.value;
        const designation = newDesignationInput.value.trim();
        const organizationName = newOrganizationNameInput.value.trim();

        hideValidationError(newSponsorFirstNameError);
        hideValidationError(newSponsorLastNameError);
        hideValidationError(newSponsorTypeError);

        let isValid = true;

        if (!sponsorFirstName) {
            showValidationError(newSponsorFirstNameError, 'First name is required.');
            isValid = false;
        }

        if (!sponsorLastName) {
            showValidationError(newSponsorLastNameError, 'Last name is required.');
            isValid = false;
        }

        if (!sponsorType) {
            showValidationError(newSponsorTypeError, 'Sponsor type is required.');
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        const sponsorName = `${sponsorFirstName} ${sponsorMiddleName} ${sponsorLastName} ${sponsorSuffix}`.replace(/\s+/g, ' ').trim();

        const newSponsor = {
            sponsor_id: `new-${Date.now()}`,
            sponsor_name: sponsorName,
            first_name: sponsorFirstName,
            middle_name: sponsorMiddleName,
            last_name: sponsorLastName,
            suffix: sponsorSuffix,
            sponsor_type: sponsorType,
            designation: designation,
            organization_name: organizationName,
            status: 'new',
            editing: false
        };

        sponsors.unshift(newSponsor);
        pendingChanges.added.push(newSponsor);
        renderSponsors();
        addSponsorForm.reset();
        newSponsorSuffixDropdownBtn.textContent = newSponsorSuffixInput.value;
        newSponsorTypeDropdownBtn.textContent = '';
        newSponsorTypeInput.value = '';
    };

    const handleEditSponsor = (e) => {
        const id = e.target.closest('.edit-sponsor').getAttribute('data-id');
        const sponsor = sponsors.find(s => String(s.sponsor_id) === String(id));

        if (sponsor) {
            sponsor.editing = true;
            sponsor.initial_data = { ...sponsor };
            renderSponsors();
        }
    };

    const handleCancelEditing = (e) => {
        const id = e.target.closest('.cancel-editing').getAttribute('data-id');
        const sponsorIndex = sponsors.findIndex(s => String(s.sponsor_id) === String(id));

        if (sponsorIndex > -1) {
            const originalSponsor = pendingChanges.edited.find(s => String(s.sponsor_id) === String(id))?.initial_data || sponsors[sponsorIndex].initial_data || sponsors[sponsorIndex];
            sponsors[sponsorIndex] = { ...originalSponsor, status: 'initial', editing: false };
            pendingChanges.edited = pendingChanges.edited.filter(s => String(s.sponsor_id) !== String(id));
            renderSponsors();
        }
    };

    const handleSaveEditing = (e) => {
        const id = e.target.closest('.save-editing').getAttribute('data-id');
        const listItem = e.target.closest('li');
        const sponsor = sponsors.find(s => String(s.sponsor_id) === String(id));

        if (!sponsor) return;

        const first_name_input = listItem.querySelector('.editing-sponsor-first-name');
        const middle_name_input = listItem.querySelector('.editing-sponsor-middle-name');
        const last_name_input = listItem.querySelector('.editing-sponsor-last-name');
        const suffix_input = listItem.querySelector('.editing-sponsor-suffix-input');
        const sponsor_type_input = listItem.querySelector('.editing-sponsor-type-input');
        const designation_input = listItem.querySelector('.editing-designation');
        const organization_name_input = listItem.querySelector('.editing-organization-name');

        hideValidationError(listItem.querySelector('.editing-sponsor-first-name-error'));
        hideValidationError(listItem.querySelector('.editing-sponsor-last-name-error'));
        hideValidationError(listItem.querySelector('.editing-sponsor-type-error'));

        let isValid = true;

        if (!first_name_input.value.trim()) {
            showValidationError(listItem.querySelector('.editing-sponsor-first-name-error'), 'First name is required.');
            isValid = false;
        }

        if (!last_name_input.value.trim()) {
            showValidationError(listItem.querySelector('.editing-sponsor-last-name-error'), 'Last name is required.');
            isValid = false;
        }

        if (!sponsor_type_input.value) {
            showValidationError(listItem.querySelector('.editing-sponsor-type-error'), 'Sponsor type is required.');
            isValid = false;
        }

        if (!isValid) return;

        sponsor.first_name = first_name_input.value.trim();
        sponsor.middle_name = middle_name_input.value.trim();
        sponsor.last_name = last_name_input.value.trim();
        sponsor.suffix = suffix_input.value;
        sponsor.sponsor_name = `${sponsor.first_name} ${sponsor.middle_name} ${sponsor.last_name} ${sponsor.suffix}`.replace(/\s+/g, ' ').trim();
        sponsor.sponsor_type = sponsor_type_input.value;
        sponsor.designation = designation_input.value.trim();
        sponsor.organization_name = organization_name_input.value.trim();
        sponsor.editing = false;
        sponsor.status = sponsor.status === 'new' ? 'new' : 'edited';

        const existingPending = pendingChanges.edited.findIndex(s => String(s.sponsor_id) === String(id));

        if (existingPending === -1) {
            pendingChanges.edited.push({ ...sponsor });
        } else {
            pendingChanges.edited[existingPending] = { ...sponsor };
        }

        renderSponsors();
    };

    const handleDeleteSponsor = async (e) => {
        const id = e.target.closest('.delete-sponsor').getAttribute('data-id');
        const sponsor = sponsors.find(s => String(s.sponsor_id) === String(id));

        if (sponsor) {
            if (sponsor.status === 'new') {
                pendingChanges.added = pendingChanges.added.filter(s => String(s.sponsor_id) !== String(sponsor.sponsor_id));
                sponsors = sponsors.filter(s => String(s.sponsor_id) !== String(sponsor.sponsor_id));
            } else {
                sponsor.status = 'deleted';
                pendingChanges.deleted.push(String(id));
            }

            renderSponsors();
        }
    };

    const handleConfirmChanges = async () => {
        try {
            const added = pendingChanges.added.map(s => ({
                first_name: s.first_name,
                middle_name: s.middle_name,
                last_name: s.last_name,
                suffix: s.suffix,
                sponsor_type: s.sponsor_type,
                designation: s.designation,
                organization_name: s.organization_name
            }));

            const edited = pendingChanges.edited.map(s => ({
                sponsor_id: s.sponsor_id,
                first_name: s.first_name,
                middle_name: s.middle_name,
                last_name: s.last_name,
                suffix: s.suffix,
                sponsor_type: s.sponsor_type,
                designation: s.designation,
                organization_name: s.organization_name
            }));

            const deleted = pendingChanges.deleted;

            const response = await fetch('/sponsors/confirm-changes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ added, edited, deleted })
            });

            const result = await response.json();

            if (result.success) {
                pendingChanges = {
                    added: [],
                    edited: [],
                    deleted: []
                };

                if (sponsorsModalOverlay) sponsorsModalOverlay.style.display = 'none';
                fetchSponsors();
            } else {
                alert(`Error: ${result.error}`);
            }
        } catch (error) {
            alert('Failed to apply changes.');
        }
    };

    const handleCancelChanges = () => {
        pendingChanges = {
            added: [],
            edited: [],
            deleted: []
        };

        if (sponsorsModalOverlay) sponsorsModalOverlay.style.display = 'none';
        fetchSponsors();
    };

    sponsorsList.addEventListener('click', (e) => {
        if (e.target.closest('.edit-sponsor')) {
            handleEditSponsor(e);
        } else if (e.target.closest('.delete-sponsor')) {
            handleDeleteSponsor(e);
        }
    });

    addSponsorForm.addEventListener('submit', handleAddSponsor);
    confirmChangesBtn.addEventListener('click', handleConfirmChanges);
    cancelChangesBtn.addEventListener('click', handleCancelChanges);

    if (sponsorsModalClose) {
        sponsorsModalClose.addEventListener('click', () => {
            pendingChanges = {
                added: [],
                edited: [],
                deleted: []
            };

            if (sponsorsModalOverlay) sponsorsModalOverlay.style.display = 'none';
            fetchSponsors();
        });
    }

    setupDropdown('sponsor-suffix-dropdown', 'sponsor-suffix-input', document);
    setupDropdown('sponsor-type-dropdown', 'sponsor-type-input', document);

    window.openSponsorsModal = () => {
        if (sponsorsModalOverlay) sponsorsModalOverlay.style.display = 'flex';
        fetchSponsors();
        addSponsorForm.reset();
        newSponsorTypeDropdownBtn.textContent = '';
        newSponsorSuffixDropdownBtn.textContent = '';
        newSponsorSuffixInput.value = '';
    };

    fetchSponsors();
});
