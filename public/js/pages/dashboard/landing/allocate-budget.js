document.addEventListener('DOMContentLoaded', () => {
    const allocateBudgetModalOverlay = document.getElementById('allocate-budget-modal-overlay');
    const allocateBudgetModalClose = document.getElementById('allocate-budget-modal-close');
    const allocationAmountInput = document.getElementById('allocation-amount');
    const allocationAmountError = document.getElementById('allocation-amount-error');
    const possessorDropdownBtn = document.getElementById('possessor-dropdown-btn');
    const possessorIdInput = document.getElementById('possessor-id');
    const possessorTypeError = document.getElementById('possessor-type-error');
    const reasonDropdownBtn = document.getElementById('reason-dropdown-btn');
    const reasonIdInput = document.getElementById('reason-id');
    const allocationReasonError = document.getElementById('allocation-reason-error');
    const directionDropdownBtn = document.getElementById('direction-dropdown-btn');
    const directionIdInput = document.getElementById('direction-id');
    const allocationDirectionError = document.getElementById('allocation-direction-error');
    const sponsorContainer = document.getElementById('sponsor-container');
    const sponsorDropdownBtn = document.getElementById('sponsor-dropdown');
    const sponsorIdInput = document.getElementById('sponsor-id');
    const sponsorNameError = document.getElementById('sponsor-name-error');
    const currentAmountInput = document.getElementById('current-amount');
    const remainingAmountInput = document.getElementById('remaining-amount');
    const amountBeforeHidden = document.getElementById('amount-before-hidden');
    const amountAccumHidden = document.getElementById('amount-accum-hidden');
    const submitAllocationBtn = document.getElementById('submit-allocation');
    const cancelAllocationBtn = document.getElementById('cancel-allocation-changes');

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');

        if (meta && meta.getAttribute) {
            return meta.getAttribute('content');
        }

        const inputToken = document.querySelector('input[name="_token"]');
        return inputToken ? inputToken.value : '';
    };

    const showValidationError = (element, message) => {
        if (!element) return;
        element.textContent = message;
        element.style.display = 'block';
    };

    const hideValidationError = (element) => {
        if (!element) return;
        element.textContent = '';
        element.style.display = 'none';
    };

    const hideAllErrors = () => {
        hideValidationError(allocationAmountError);
        hideValidationError(possessorTypeError);
        hideValidationError(allocationReasonError);
        hideValidationError(allocationDirectionError);
        hideValidationError(sponsorNameError);
    };

    const closeAllDropdowns = () => {
        document.querySelectorAll('.dropdown-toggle.rotated').forEach(btn => btn.classList.remove('rotated'));
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
    };

    const updateDropdown = (btn, hidden, value, text) => {
        if (btn) {
            btn.textContent = text;
            btn.classList.remove('rotated');
        }

        if (hidden) {
            hidden.value = value;
        }
    };

    const updateRemainingAndCurrentAmount = () => {
        const currentAmount = parseFloat(currentAmountInput.value) || 0;
        const allocationAmount = parseFloat(allocationAmountInput.value) || 0;
        const direction = directionIdInput.value;
        let newRemainingAmount;

        if (direction === 'Decrease') {
            newRemainingAmount = currentAmount - allocationAmount;
        } else {
            newRemainingAmount = currentAmount + allocationAmount;
        }

        remainingAmountInput.value = newRemainingAmount.toFixed(2);
    };

    allocationAmountInput.addEventListener('input', updateRemainingAndCurrentAmount);

    const fetchBudgetData = async () => {
        try {
            const response = await fetch('/api/latest-budget', { credentials: 'same-origin' });

            if (!response.ok) {
                throw new Error('Failed to fetch budget data');
            }

            const data = await response.json();
            currentAmountInput.value = (data.amount_recent !== undefined) ? parseFloat(data.amount_recent).toFixed(2) : '0.00';
            amountBeforeHidden.value = currentAmountInput.value;
            amountAccumHidden.value = (data.amount_accum !== undefined) ? parseFloat(data.amount_accum).toFixed(2) : '0.00';
            updateRemainingAndCurrentAmount();
        } catch (error) {
            console.error('Error fetching budget data:', error);
            showValidationError(allocationAmountError, 'Failed to load budget data. Please try again.');
        }
    };

    const fetchSponsors = async () => {
        try {
            const response = await fetch('/api/sponsors', { credentials: 'same-origin' });

            if (!response.ok) {
                throw new Error('Failed to fetch sponsors');
            }

            const data = await response.json();
            const sponsors = data.sponsors || [];
            const sponsorList = document.getElementById('sponsor-list');
            sponsorList.innerHTML = '';

            if (Array.isArray(sponsors)) {
                sponsors.forEach(s => {
                    const first = s.first_name || '';
                    const middle = s.middle_name || '';
                    const last = s.last_name || '';
                    const suffix = s.suffix || '';
                    const computedName = `${first} ${middle} ${last} ${suffix}`.replace(/\s+/g, ' ').trim();
                    const displayName = s.sponsor_name && s.sponsor_name.trim() ? s.sponsor_name : computedName;
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.className = 'dropdown-item';
                    a.href = '#';
                    a.textContent = displayName;
                    a.dataset.value = s.sponsor_id !== undefined ? s.sponsor_id : (s.id !== undefined ? s.id : '');
                    li.appendChild(a);
                    sponsorList.appendChild(li);
                });
            } else {
                console.error('Sponsors data is not an array:', sponsors);
                showValidationError(sponsorNameError, 'Sponsors data is not in expected format.');
            }
        } catch (error) {
            console.error('Error fetching sponsors:', error);
            showValidationError(sponsorNameError, 'Failed to load sponsors. Please try again.');
        }
    };

    possessorDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdowns();
        possessorDropdownBtn.classList.add('rotated');
        document.querySelector(`#${possessorDropdownBtn.id} + .dropdown-menu`).classList.add('show');
    });

    reasonDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdowns();
        reasonDropdownBtn.classList.add('rotated');
        document.querySelector(`#${reasonDropdownBtn.id} + .dropdown-menu`).classList.add('show');
    });

    directionDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdowns();
        directionDropdownBtn.classList.add('rotated');
        document.querySelector(`#${directionDropdownBtn.id} + .dropdown-menu`).classList.add('show');
    });

    sponsorDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdowns();
        sponsorDropdownBtn.classList.add('rotated');
        document.querySelector(`#${sponsorDropdownBtn.id} + .dropdown-menu`).classList.add('show');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });

    document.querySelectorAll('#possessor-dropdown-btn + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const possessorValue = e.target.dataset.value;
            const possessorText = e.target.textContent.trim();
            updateDropdown(possessorDropdownBtn, possessorIdInput, possessorValue, possessorText);
            hideValidationError(possessorTypeError);

            sponsorContainer.style.display = 'none';
            reasonDropdownBtn.disabled = true;
            reasonIdInput.value = '';
            reasonDropdownBtn.textContent = 'Select Reason';
            directionDropdownBtn.disabled = true;
            directionIdInput.value = 'Increase';
            directionDropdownBtn.textContent = 'Increase';

            document.querySelectorAll('#reason-dropdown-btn + .dropdown-menu .dropdown-item').forEach(reasonItem => {
                reasonItem.style.display = 'block';
            });

            if (possessorValue === 'AMPING') {
                reasonDropdownBtn.disabled = false;
                const sponsorDonationItem = document.querySelector('#reason-dropdown-btn + .dropdown-menu a[data-value="Sponsor Donation"]');
                if (sponsorDonationItem) {
                    sponsorDonationItem.style.display = 'none';
                }
            } else if (possessorValue === 'Sponsor') {
                sponsorContainer.style.display = 'flex';
                reasonIdInput.value = 'Sponsor Donation';
                reasonDropdownBtn.textContent = 'Sponsor Donation';
                reasonDropdownBtn.disabled = true;
            }
            updateRemainingAndCurrentAmount();
        });
    });

    document.querySelectorAll('#reason-dropdown-btn + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const reasonValue = e.target.dataset.value;
            const reasonText = e.target.textContent.trim();
            updateDropdown(reasonDropdownBtn, reasonIdInput, reasonValue, reasonText);
            hideValidationError(allocationReasonError);

            if (reasonValue === 'Budget Manipulation') {
                directionDropdownBtn.disabled = false;
            } else {
                directionDropdownBtn.disabled = true;
                directionIdInput.value = 'Increase';
                directionDropdownBtn.textContent = 'Increase';
            }
            updateRemainingAndCurrentAmount();
        });
    });

    document.querySelectorAll('#direction-dropdown-btn + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const directionValue = e.target.dataset.value;
            const directionText = e.target.textContent.trim();
            updateDropdown(directionDropdownBtn, directionIdInput, directionValue, directionText);
            hideValidationError(allocationDirectionError);
            updateRemainingAndCurrentAmount();
        });
    });

    document.getElementById('sponsor-list').addEventListener('click', (e) => {
        const item = e.target.closest('.dropdown-item');
        if (!item) return;
        e.preventDefault();
        const sponsorValue = item.dataset.value;
        const sponsorText = item.textContent.trim();
        updateDropdown(sponsorDropdownBtn, sponsorIdInput, sponsorValue, sponsorText);
        hideValidationError(sponsorNameError);
    });

    const closeAllocateBudgetModal = () => {
        allocateBudgetModalOverlay.style.display = 'none';
        hideAllErrors();
    };

    allocateBudgetModalClose.addEventListener('click', closeAllocateBudgetModal);
    cancelAllocationBtn.addEventListener('click', closeAllocateBudgetModal);

    window.openAllocateBudgetModal = () => {
        allocateBudgetModalOverlay.style.display = 'flex';
        allocationAmountInput.value = '';
        possessorIdInput.value = '';
        reasonIdInput.value = '';
        sponsorIdInput.value = '';
        possessorDropdownBtn.textContent = 'Select Possessor';
        reasonDropdownBtn.textContent = 'Select Reason';
        sponsorDropdownBtn.textContent = 'Select Sponsor';
        directionIdInput.value = 'Increase';
        directionDropdownBtn.textContent = 'Increase';
        sponsorContainer.style.display = 'none';
        reasonDropdownBtn.disabled = true;
        directionDropdownBtn.disabled = true;

        hideAllErrors();
        fetchBudgetData();
        fetchSponsors();
    };

    submitAllocationBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        hideAllErrors();
        const amountChange = parseFloat(allocationAmountInput.value) || 0;
        const possessor = possessorIdInput.value;
        const reason = reasonIdInput.value;
        const direction = directionIdInput.value;
        const sponsorId = sponsorIdInput.value;
        const amountBefore = parseFloat(amountBeforeHidden.value) || 0;
        const amountAccumPrevious = parseFloat(amountAccumHidden.value) || 0;
        let amountRecent;
        let amountAccum;

        if (direction === 'Decrease') {
            amountRecent = amountBefore - amountChange;
            amountAccum = amountAccumPrevious - amountChange;
        } else {
            amountRecent = amountBefore + amountChange;
            amountAccum = amountAccumPrevious + amountChange;
        }

        if (!amountChange || amountChange <= 0) {
            showValidationError(allocationAmountError, 'Allocation amount is required and must be greater than 0.');
            return;
        }

        if (!possessor) {
            showValidationError(possessorTypeError, 'Possessor is required.');
            return;
        }

        if (possessor === 'AMPING' && !reason) {
            showValidationError(allocationReasonError, 'Reason is required for AMPING.');
            return;
        }

        if (possessor === 'Sponsor' && !sponsorId) {
            showValidationError(sponsorNameError, 'Sponsor name is required.');
            return;
        }

        if (reason === 'Budget Manipulation' && !direction) {
            showValidationError(allocationDirectionError, 'Direction is required for Budget Manipulation.');
            return;
        }

        const data = {
            amount_change: amountChange,
            possessor: possessor,
            reason: reason,
            direction: direction,
            sponsor_id: sponsorId,
            amount_before: amountBefore,
            amount_recent: amountRecent,
            amount_accum: amountAccum
        };

        try {
            const response = await fetch('/budget-updates', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                let errorMessage = `Server error: ${response.status}. Please try again.`;

                try {
                    const errorJson = await response.json();
                    if (errorJson && errorJson.error) {
                        errorMessage = typeof errorJson.error === 'string' ? errorJson.error : JSON.stringify(errorJson.error);
                    }
                } catch (jsonErr) { }

                showValidationError(allocationAmountError, errorMessage);
                return;
            }

            const result = await response.json();

            if (result.success) {
                closeAllocateBudgetModal();
                window.location.reload();
            } else {
                const err = result.error || 'An error occurred. Please try again.';
                showValidationError(allocationAmountError, typeof err === 'string' ? err : JSON.stringify(err));
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showValidationError(allocationAmountError, 'Failed to submit data.');
        }
    });
});
