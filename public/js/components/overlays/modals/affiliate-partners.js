document.addEventListener('DOMContentLoaded', () => {
    const affiliatePartnersModalOverlay = document.getElementById('affiliate-partners-modal-overlay');
    const affiliatePartnersModalClose = document.getElementById('affiliate-partners-modal-close');
    const addAffiliatePartnerForm = document.getElementById('add-affiliate-partner-form');
    const newAffiliatePartnerNameInput = document.getElementById('affiliate-partner-name');
    const newAffiliatePartnerNameError = document.getElementById('affiliate-partner-name-error');
    const newAffiliatePartnerTypeInput = document.getElementById('affiliate-partner-type');
    const newAffiliatePartnerTypeDropdownBtn = document.getElementById('affiliate-partner-type-dropdown');
    const newAffiliatePartnerTypeError = document.getElementById('affiliate-partner-type-error');
    const affiliatePartnersList = document.getElementById('affiliate-partners-list');
    const confirmChangesBtn = document.getElementById('confirm-affiliate-partners-changes');
    const cancelChangesBtn = document.getElementById('cancel-affiliate-partners-changes');

    let affiliatePartners = [];

    let pendingChanges = {
        added: [],
        edited: [],
        deleted: []
    };

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.getAttribute) {
            return meta.getAttribute('content');
        }
        const inputToken = document.querySelector('input[name="_token"]');
        return inputToken ? inputToken.value : '';
    };

    const customAlert = (message, type = 'Error') => {
        const modal = document.createElement('div');
        modal.classList.add('modal-overlay');
        modal.innerHTML = `
            <div class="modal-container">
                <div class="modal-header">
                    <h2>${type}</h2>
                    <button class="modal-close alert-close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary alert-ok">OKAY, I UNDERSTAND</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.querySelector('.alert-ok').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        modal.querySelector('.alert-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
    };

    const customConfirm = (message) => {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.classList.add('modal-overlay');
            modal.innerHTML = `
                <div class="modal-container">
                    <div class="modal-header">
                        <h2>Confirm Action</h2>
                        <button class="modal-close confirm-close">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary confirm-cancel">Cancel</button>
                        <button class="btn btn-danger confirm-ok">Confirm</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            modal.querySelector('.confirm-ok').addEventListener('click', () => {
                document.body.removeChild(modal);
                resolve(true);
            });
            modal.querySelector('.confirm-cancel').addEventListener('click', () => {
                document.body.removeChild(modal);
                resolve(false);
            });
            modal.querySelector('.confirm-close').addEventListener('click', () => {
                document.body.removeChild(modal);
                resolve(false);
            });
        });
    };

    const fetchAffiliatePartners = async () => {
        try {
            const response = await fetch('/api/affiliate-partners');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            affiliatePartners = data.map(ap => ({
                id: ap.affiliate_partner_id,
                name: ap.affiliate_partner_name,
                type: ap.affiliate_partner_type,
                status: 'existing'
            }));
            renderAffiliatePartners();
        } catch (error) {
            console.error('Error fetching affiliate partners:', error);
            customAlert('Failed to load affiliate partners: ' + error.message);
        }
    };

    const renderAffiliatePartners = () => {
        affiliatePartnersList.innerHTML = '';

        affiliatePartners.forEach(partner => {
            if (partner.status === 'deleted') return;

            const listItem = document.createElement('li');
            listItem.id = `affiliate-partner-item-${partner.id}`;
            listItem.classList.add('affiliate-partner-item');

            if (partner.status === 'new') {
                listItem.classList.add('item');
            } else if (partner.status === 'edited') {
                listItem.classList.add('edited-item');
            }

            if (partner.editing) {
                listItem.innerHTML = `
                    <form class="editing-form" data-id="${partner.id}">
                        <div class="row gx-3 gy-3 mb-3">
                            <div class="form-group col-md-6">
                                <label for="editing-affiliate-partner-name-${partner.id}" class="fw-bold">Affiliate Partner Name</label>
                                <input type="text" id="editing-affiliate-partner-name-${partner.id}" value="${partner.name}" class="editing-affiliate-partner-name form-control">
                                <span class="error-message editing-affiliate-partner-name-error" style="display: none;"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editing-affiliate-partner-type-${partner.id}" class="fw-bold">Affiliate Partner Type</label>
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle editing-affiliate-partner-type-dropdown" type="button" id="editing-affiliate-partner-type-dropdown-${partner.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        ${partner.type || ''}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="editing-affiliate-partner-type-dropdown-${partner.id}">
                                        <li><a class="dropdown-item" href="#" data-value="Hospital / Clinic">Hospital / Clinic</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Pharmacy / Drugstore">Pharmacy / Drugstore</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="Other">Other</a></li>
                                    </ul>
                                    <input type="hidden" id="editing-affiliate-partner-type-${partner.id}" class="editing-affiliate-partner-type" value="${partner.type || ''}">
                                </div>
                                <span class="error-message editing-affiliate-partner-type-error" style="display: none;"></span>
                            </div>
                            <div class="button-group">
                                <button type="submit" class="btn btn-success btn-sm" id="saveBtn">SAVE</button>
                                <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-id="${partner.id}" id="cancelBtn">CANCEL</button>
                            </div>
                        </div>
                    </form>
                `;
            } else {
                listItem.innerHTML = `
                    <div class="affiliate-partner-details">
                        <div class="affiliate-partner-name">${partner.name}</div>
                        <div class="affiliate-partner-type">${partner.type}</div>
                    </div>
                    <div class="affiliate-partner-actions">
                        <button class="btn btn-info btn-sm edit-affiliate-partner-btn" data-id="${partner.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.35-.350.106-.106-.35-.35-.106a.5.5 0 0 1 .106-.35l.35-.106zM6.5 13H5v1.5a.5.5 0 0 1-.5.5h-.5a.5.5 0 0 1-.5-.5V13h-.5a.5.5 0 0 1-.5-.5v-.5a.5.5 0 0 1 .5-.5h.5V11a.5.5 0 0 1 .5-.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 0 1-.5.5z"/>
                            </svg>
                        </button>
                        <button class="btn btn-danger btn-sm delete-affiliate-partner-btn" data-id="${partner.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                            </svg>
                        </button>
                    </div>
                `;
            }

            affiliatePartnersList.appendChild(listItem);
        });

        document.querySelectorAll('.edit-affiliate-partner-btn').forEach(button => {
            button.onclick = (e) => editAffiliatePartner(e.currentTarget.dataset.id);
        });

        document.querySelectorAll('.delete-affiliate-partner-btn').forEach(button => {
            button.onclick = (e) => deleteAffiliatePartner(e.currentTarget.dataset.id);
        });

        document.querySelectorAll('.editing-form').forEach(form => {
            form.onsubmit = (e) => {
                e.preventDefault();
                updateAffiliatePartner(form.dataset.id);
            };
        });

        document.querySelectorAll('.cancel-edit-btn').forEach(button => {
            button.onclick = (e) => cancelEdit(e.currentTarget.dataset.id);
        });

        document.querySelectorAll('.editing-affiliate-partner-type-dropdown').forEach(dropdownBtn => {
            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownBtn.classList.toggle('rotated');
            });
        });
    };

    const showValidationError = (element, message) => {
        element.textContent = message;
        element.style.display = 'block';
    };

    const hideValidationError = (element) => {
        element.textContent = '';
        element.style.display = 'none';
    };

    if (addAffiliatePartnerForm) {
        addAffiliatePartnerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const newName = newAffiliatePartnerNameInput.value.trim();
            const newType = newAffiliatePartnerTypeInput.value.trim();
            let isValid = true;

            if (newName.length < 3) {
                showValidationError(newAffiliatePartnerNameError, 'Affiliate Partner name must be at least 3 characters.');
                isValid = false;
            } else {
                hideValidationError(newAffiliatePartnerNameError);
            }

            if (newType === '' || !['Hospital / Clinic', 'Pharmacy / Drugstore', 'Other'].includes(newType)) {
                showValidationError(newAffiliatePartnerTypeError, 'Please select a valid Affiliate Partner type.');
                isValid = false;
            } else {
                hideValidationError(newAffiliatePartnerTypeError);
            }

            if (!isValid) return;

            const tempId = `temp-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

            const newPartner = {
                id: tempId,
                name: newName,
                type: newType,
                status: 'new',
                editing: false
            };

            affiliatePartners.push(newPartner);
            pendingChanges.added.push(newPartner);
            newAffiliatePartnerNameInput.value = '';
            newAffiliatePartnerTypeInput.value = '';
            if (newAffiliatePartnerTypeDropdownBtn) newAffiliatePartnerTypeDropdownBtn.textContent = '';
            renderAffiliatePartners();
        });
    }

    const editAffiliatePartner = (partnerId) => {
        const partnerIndex = affiliatePartners.findIndex(o => o.id === partnerId);
        if (partnerIndex > -1) {
            affiliatePartners.forEach(o => o.editing = false);
            affiliatePartners[partnerIndex].editing = true;
            renderAffiliatePartners();
        }
    };

    const updateAffiliatePartner = (partnerId) => {
        const partnerIndex = affiliatePartners.findIndex(o => o.id === partnerId);
        if (partnerIndex > -1) {
            const currentItem = affiliatePartnersList.querySelector(`#affiliate-partner-item-${partnerId}`);
            const editingNameInput = currentItem.querySelector('.editing-affiliate-partner-name');
            const editingNameError = currentItem.querySelector('.editing-affiliate-partner-name-error');
            const editingTypeInput = currentItem.querySelector('.editing-affiliate-partner-type');
            const editingTypeError = currentItem.querySelector('.editing-affiliate-partner-type-error');

            const updatedName = editingNameInput.value.trim();
            const updatedType = editingTypeInput.value.trim();
            let isValid = true;

            if (updatedName.length < 3) {
                showValidationError(editingNameError, 'Affiliate Partner name must be at least 3 characters.');
                isValid = false;
            } else {
                hideValidationError(editingNameError);
            }

            if (updatedType === '' || !['Hospital / Clinic', 'Pharmacy / Drugstore', 'Other'].includes(updatedType)) {
                showValidationError(editingTypeError, 'Please select a valid Affiliate Partner type.');
                isValid = false;
            } else {
                hideValidationError(editingTypeError);
            }

            if (!isValid) return;

            affiliatePartners[partnerIndex].name = updatedName;
            affiliatePartners[partnerIndex].type = updatedType;
            affiliatePartners[partnerIndex].editing = false;

            if (affiliatePartners[partnerIndex].status === 'existing') {
                affiliatePartners[partnerIndex].status = 'edited';
                const existingEditedIndex = pendingChanges.edited.findIndex(item => item.id === partnerId);
                if (existingEditedIndex > -1) {
                    pendingChanges.edited[existingEditedIndex] = { ...affiliatePartners[partnerIndex] };
                } else {
                    pendingChanges.edited.push({ ...affiliatePartners[partnerIndex] });
                }
            } else if (affiliatePartners[partnerIndex].status === 'new') {
                const existingAddedIndex = pendingChanges.added.findIndex(item => item.id === partnerId);
                if (existingAddedIndex > -1) {
                    pendingChanges.added[existingAddedIndex] = { ...affiliatePartners[partnerIndex] };
                }
            }
            renderAffiliatePartners();
        }
    };

    const cancelEdit = (partnerId) => {
        const partnerIndex = affiliatePartners.findIndex(o => o.id === partnerId);
        if (partnerIndex > -1) {
            if (affiliatePartners[partnerIndex].status === 'edited') {

                const originalPartner = affiliatePartners.find(p => p.id === partnerId && p.status === 'existing');
                if (originalPartner) {
                    affiliatePartners[partnerIndex].name = originalPartner.name;
                    affiliatePartners[partnerIndex].type = originalPartner.type;
                }

                affiliatePartners[partnerIndex].status = 'existing';
                pendingChanges.edited = pendingChanges.edited.filter(item => item.id !== partnerId);
            }
            affiliatePartners[partnerIndex].editing = false;
            renderAffiliatePartners();
        }
    };

    const deleteAffiliatePartner = async (partnerId) => {
        const partnerIndex = affiliatePartners.findIndex(o => o.id === partnerId);
        if (partnerIndex === -1) return;

        const currentPartner = affiliatePartners[partnerIndex];
        const confirmationMessage = `Are you sure you want to delete the affiliate partner '<b>${currentPartner.name}</b>'?<br>This action will be permanent once changes are confirmed.`;
        const userConfirmed = await customConfirm(confirmationMessage);

        if (!userConfirmed) {
            return;
        }

        affiliatePartners[partnerIndex].status = 'deleted';
        pendingChanges.deleted.push(partnerId);

        pendingChanges.added = pendingChanges.added.filter(item => item.id !== partnerId);
        pendingChanges.edited = pendingChanges.edited.filter(item => item.id !== partnerId);

        renderAffiliatePartners();
    };

    document.addEventListener('click', (e) => {
        const item = e.target.closest('.dropdown-item');
        if (!item) return;
        e.preventDefault();
        const dropdown = item.closest('.dropdown');
        if (!dropdown) return;
        const btn = dropdown.querySelector('.dropdown-toggle');
        const hidden = dropdown.querySelector('input[type="hidden"]');
        const value = item.getAttribute('data-value') || item.dataset.value || item.textContent.trim();
        const text = item.textContent.trim();

        if (btn) {
            btn.textContent = text;
            btn.classList.remove('rotated');
        }

        if (hidden) {
            hidden.value = value;
        }

        const form = item.closest('.editing-form');
        if (form) {
            const editingId = form.dataset.id;
            const partnerIndex = affiliatePartners.findIndex(p => p.id === editingId);
            if (partnerIndex > -1) {
                affiliatePartners[partnerIndex].type = value;
            }
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-toggle.rotated').forEach(btn => btn.classList.remove('rotated'));
        }
    });

    if (newAffiliatePartnerTypeDropdownBtn) {
        newAffiliatePartnerTypeDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            newAffiliatePartnerTypeDropdownBtn.classList.toggle('rotated');
        });
    }

    if (confirmChangesBtn) {
        confirmChangesBtn.addEventListener('click', async () => {
            const changesToSend = {
                create: pendingChanges.added.map(partner => ({
                    affiliate_partner_name: partner.name,
                    affiliate_partner_type: partner.type
                })),
                update: pendingChanges.edited.map(partner => ({
                    affiliate_partner_id: partner.id,
                    affiliate_partner_name: partner.name,
                    affiliate_partner_type: partner.type
                })),
                delete: pendingChanges.deleted
            };

            try {
                const csrfToken = getCsrfToken();
                const response = await fetch('/affiliate-partners/confirm-changes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(changesToSend)
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.error || 'Failed to confirm changes.');
                }

                pendingChanges = { added: [], edited: [], deleted: [] };
                if (affiliatePartnersModalOverlay) affiliatePartnersModalOverlay.style.display = 'none';
                await fetchAffiliatePartners();
                customAlert('Changes saved successfully!', 'Success');
            } catch (error) {
                console.error('Error confirming changes:', error);
                customAlert('Failed to save changes: ' + error.message);
            }
        });
    }

    const resetPendingChanges = () => {
        pendingChanges = {
            added: [],
            edited: [],
            deleted: []
        };
    };

    if (cancelChangesBtn) {
        cancelChangesBtn.addEventListener('click', () => {
            resetPendingChanges();
            if (affiliatePartnersModalOverlay) affiliatePartnersModalOverlay.style.display = 'none';
            fetchAffiliatePartners();
        });
    }

    if (affiliatePartnersModalClose) {
        affiliatePartnersModalClose.addEventListener('click', () => {
            resetPendingChanges();
            if (affiliatePartnersModalOverlay) affiliatePartnersModalOverlay.style.display = 'none';
            fetchAffiliatePartners();
        });
    }

    window.openAffiliatePartnersModal = () => {
        if (affiliatePartnersModalOverlay) affiliatePartnersModalOverlay.style.display = 'flex';
        fetchAffiliatePartners();
    };

    fetchAffiliatePartners();
});
