document.addEventListener('DOMContentLoaded', () => {
    const supplementaryBudgetModalOverlay = document.getElementById('supplementary-budget-modal-overlay');
    const supplementaryBudgetModalClose = document.getElementById('supplementary-budget-modal-close');
    const supplementaryAllocationAmountInput = document.getElementById('supplementary-allocation-amount');
    const supplementaryAllocationAmountError = document.getElementById('supplementary-allocation-amount-error');
    const supplementaryPossessorIdInput = document.getElementById('supplementary-possessor-id');
    const supplementaryPossessorTypeError = document.getElementById('supplementary-possessor-type-error');
    const supplementaryReasonIdInput = document.getElementById('supplementary-reason-id');
    const supplementaryAllocationReasonError = document.getElementById('supplementary-allocation-reason-error');
    const supplementaryDirectionIdInput = document.getElementById('supplementary-direction-id');
    const supplementaryAllocationDirectionError = document.getElementById('supplementary-allocation-direction-error');
    const supplementaryCurrentAmountInput = document.getElementById('supplementary-current-amount');
    const supplementaryRemainingAmountInput = document.getElementById('supplementary-remaining-amount');
    const supplementaryAmountBeforeHidden = document.getElementById('supplementary-amount-before-hidden');
    const supplementaryAmountAccumHidden = document.getElementById('supplementary-amount-accum-hidden');
    const submitSupplementaryAllocationBtn = document.getElementById('submit-supplementary-allocation');
    const cancelSupplementaryAllocationBtn = document.getElementById('cancel-supplementary-allocation-changes');

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
        hideValidationError(supplementaryAllocationAmountError);
        hideValidationError(supplementaryPossessorTypeError);
        hideValidationError(supplementaryAllocationReasonError);
        hideValidationError(supplementaryAllocationDirectionError);
    };

    const closeAllDropdowns = () => {
        document.querySelectorAll('.dropdown-toggle.rotated').forEach(btn => btn.classList.remove('rotated'));
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
    };

    const updateSupplementaryRemainingAndCurrentAmount = () => {
        const currentAmount = parseFloat(supplementaryCurrentAmountInput.value) || 0;
        const allocationAmount = parseFloat(supplementaryAllocationAmountInput.value) || 0;
        const direction = supplementaryDirectionIdInput.value;
        let newRemainingAmount;

        if (direction === 'Decrease') {
            newRemainingAmount = currentAmount - allocationAmount;
        } else {
            newRemainingAmount = currentAmount + allocationAmount;
        }

        supplementaryRemainingAmountInput.value = newRemainingAmount.toFixed(2);
    };

    supplementaryAllocationAmountInput.addEventListener('input', updateSupplementaryRemainingAndCurrentAmount);

    const fetchSupplementaryBudgetData = async () => {
        try {
            const response = await fetch('/api/latest-budget', { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Failed to fetch budget data');
            const data = await response.json();
            supplementaryCurrentAmountInput.value = (data.amount_recent !== undefined) ? parseFloat(data.amount_recent).toFixed(2) : '0.00';
            supplementaryAmountBeforeHidden.value = supplementaryCurrentAmountInput.value;
            supplementaryAmountAccumHidden.value = (data.amount_accum !== undefined) ? parseFloat(data.amount_accum).toFixed(2) : '0.00';
            updateSupplementaryRemainingAndCurrentAmount();
        } catch (error) {
            console.error('Error fetching budget data:', error);
            showValidationError(supplementaryAllocationAmountError, 'Failed to load budget data. Please try again.');
        }
    };

    const closeSupplementaryBudgetModal = () => {
        supplementaryBudgetModalOverlay.style.display = 'none';
        hideAllErrors();
    };

    supplementaryBudgetModalClose.addEventListener('click', closeSupplementaryBudgetModal);
    cancelSupplementaryAllocationBtn.addEventListener('click', closeSupplementaryBudgetModal);

    window.openSupplementaryBudgetModal = () => {
        supplementaryBudgetModalOverlay.style.display = 'flex';
        supplementaryAllocationAmountInput.value = '';
        hideAllErrors();
        fetchSupplementaryBudgetData();
    };

    submitSupplementaryAllocationBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        hideAllErrors();
        const amountChange = parseFloat(supplementaryAllocationAmountInput.value) || 0;
        const possessor = supplementaryPossessorIdInput.value;
        const reason = supplementaryReasonIdInput.value;
        const direction = supplementaryDirectionIdInput.value;
        const amountBefore = parseFloat(supplementaryAmountBeforeHidden.value) || 0;
        const amountAccumPrevious = parseFloat(supplementaryAmountAccumHidden.value) || 0;
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
            showValidationError(supplementaryAllocationAmountError, 'Allocation amount is required and must be greater than 0.');
            return;
        }

        const data = {
            amount_change: amountChange,
            possessor: possessor,
            reason: reason,
            direction: direction,
            amount_before: amountBefore,
            amount_recent: amountRecent,
            amount_accum: amountAccum
        };

        try {
            const response = await fetch('/budget-updates/supplementary', {
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
                    if (errorJson && errorJson.error) errorMessage = typeof errorJson.error === 'string' ? errorJson.error : JSON.stringify(errorJson.error);
                } catch (jsonErr) { }
                showValidationError(supplementaryAllocationAmountError, errorMessage);
                return;
            }

            const result = await response.json();
            if (result.success) {
                closeSupplementaryBudgetModal();
                window.location.reload();
            } else {
                const err = result.error || 'An error occurred. Please try again.';
                showValidationError(supplementaryAllocationAmountError, typeof err === 'string' ? err : JSON.stringify(err));
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showValidationError(supplementaryAllocationAmountError, 'Failed to submit data.');
        }
    });
});
