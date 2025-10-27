document.addEventListener('DOMContentLoaded', function () {
    const createTariffModal = new bootstrap.Modal(document.getElementById('createTariffModal'));
    const createForm = document.getElementById('tariffCreateFormModal');
    const effectivityDateInput = document.getElementById('effectivity-date-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const selectedServicesError = document.getElementById('selectedServices-error');
    const effectivityDateError = document.getElementById('effectivity_date-error');
    const createDraftButton = document.getElementById('createDraftButton');
    let takenDates = [];

    async function fetchTakenDates() {
        try {
            const response = await fetch('/tariff-lists/taken-dates');
            const result = await response.json();
            takenDates = result.taken_dates || [];
        } catch (error) {
            console.error('Error fetching taken dates:', error);
        }
    }

    fetchTakenDates();

    function hideErrors() {
        selectedServicesError.style.display = 'none';
        effectivityDateError.style.display = 'none';
    }

    function isDateTaken(dateString) {
        return takenDates.includes(dateString);
    }

    window.closeCreateModal = function () {
        createForm.reset();
        hideErrors();
        createTariffModal.hide();
    };

    window.showCreateTariffModal = async function () {
        createForm.reset();
        await fetchTakenDates();

        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowString = tomorrow.toISOString().substring(0, 10);
        effectivityDateInput.setAttribute('min', tomorrowString);
        effectivityDateInput.value = tomorrowString;

        if (isDateTaken(tomorrowString)) {
            effectivityDateError.querySelector('span').textContent = 'The selected effectivity date is already taken by another tariff list version.';
            effectivityDateError.style.display = 'block';
        } else {
            hideErrors();
        }

        createTariffModal.show();
    };

    effectivityDateInput.addEventListener('change', function () {
        if (!effectivityDateInput.value) return;

        const selectedDate = effectivityDateInput.value;

        if (isDateTaken(selectedDate)) {
            effectivityDateError.querySelector('span').textContent = 'The selected effectivity date is already taken by another tariff list version.';
            effectivityDateError.style.display = 'block';
        } else {
            effectivityDateError.style.display = 'none';
        }
    });

    createForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        hideErrors();

        const selectedServices = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => cb.value);
        const effectivityDate = effectivityDateInput.value;

        if (selectedServices.length === 0) {
            selectedServicesError.style.display = 'block';
            return;
        }

        if (isDateTaken(effectivityDate)) {
            effectivityDateError.querySelector('span').textContent = 'The selected effectivity date is already taken by another tariff list version.';
            effectivityDateError.style.display = 'block';
            alert('The selected effectivity date is already taken by another tariff list version. Please select a different date.');
            return;
        }

        if (effectivityDateError.style.display === 'block') {
            alert('Please fix the effectivity date error before proceeding.');
            return;
        }

        createDraftButton.disabled = true;

        const data = {
            effectivity_date: effectivityDate,
            selectedServices: selectedServices,
        };

        try {
            const response = await fetch('/tariff-lists', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok) {
                await fetchTakenDates();
                window.closeCreateModal();
                alert(result.message);
                window.refreshTariffTable();
            } else if (response.status === 422) {
                const errors = result.errors;

                if (errors.selectedServices) {
                    selectedServicesError.querySelector('span').textContent = errors.selectedServices[0];
                    selectedServicesError.style.display = 'block';
                }

                if (errors.effectivity_date) {
                    effectivityDateError.querySelector('span').textContent = errors.effectivity_date[0];
                    effectivityDateError.style.display = 'block';
                }

                if (result.message && result.message.includes('You cannot create more than 9 tariff list versions in the same month')) {
                    alert(result.message);
                }
            } else {
                alert(result.message || 'An unexpected error occurred.');
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert('A network error occurred. Please try again.');
        } finally {
            createDraftButton.disabled = false;
        }
    });
});
