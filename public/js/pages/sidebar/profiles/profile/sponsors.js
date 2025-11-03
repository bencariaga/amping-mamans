document.addEventListener('DOMContentLoaded', function () {
    const sponsorshipListContainer = document.getElementById('sponsorshipListContainer');
    const tableBody = sponsorshipListContainer.querySelector('.sponsorship-rows');
    const updateBtn = document.getElementById('updateTableBtn');

    function formatDateTimeForDb(date) {
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const hh = String(date.getHours()).padStart(2, '0');
        const mi = String(date.getMinutes()).padStart(2, '0');
        const ss = String(date.getSeconds()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
    }

    function updateOrdinalNumbers() {
        const rows = tableBody.querySelectorAll('.money-amount-row');

        rows.forEach((row, index) => {
            const ordinalText = row.querySelector('.ordinal-text');
            const number = index + 1;
            let suffix = "th";

            const lastDigit = number % 10;
            const lastTwoDigits = number % 100;

            if (lastTwoDigits >= 11 && lastTwoDigits <= 13) {
                suffix = "th";
            } else {
                switch (lastDigit) {
                    case 1:
                        suffix = "st";
                        break;
                    case 2:
                        suffix = "nd";
                        break;
                    case 3:
                        suffix = "rd";
                        break;
                    default:
                        suffix = "th";
                        break;
                }
            }

            if (ordinalText) {
                ordinalText.textContent = `${number}${suffix}`;
            }

            const removeBtn = row.querySelector('.row-remove-btn');

            if (removeBtn) {
                if (rows.length === 1) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = '';
                }
            }
        });
    }

    function calculateTotalAmount() {
        let runningTotal = 0;
        const rows = tableBody.querySelectorAll('.money-amount-row');

        rows.forEach(row => {
            const amountInput = row.querySelector('.sponsorship-input');
            const amount = parseFloat(amountInput.value) || 0;
            runningTotal += amount;
            const totalInput = row.querySelector('.total-amount-input');
            if (totalInput) {
                totalInput.value = runningTotal.toFixed(2);
            }
        });
    }

    function buildNewRowElement() {
        const tr = document.createElement('tr');
        tr.className = 'money-amount-row';

        tr.innerHTML = `
            <td class="money-amount-cell ordinal-number-cell">
                <div class="money-amount-container">
                    <span class="ordinal-text"></span>
                </div>
            </td>

            <td class="money-amount-cell">
                <div class="money-amount-container">
                    <span class="money-currency fw-bold">₱</span>
                    <input type="number" step="0.01" name="amount_change_new[]" class="form-control form-control-sm sponsorship-input text-end money-value" value="0.00">
                    <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                </div>
            </td>

            <td class="money-amount-cell">
                <div class="money-amount-container">
                    <span class="money-currency fw-bold">₱</span>
                    <input type="text" class="form-control form-control-sm total-amount-input text-end money-value" readonly>
                    <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                </div>
            </td>

            <td class="text-center time-sponsored-cell" data-created="">
                <div class="time-sponsored-text">
                    <span class="time-text"></span>
                </div>
            </td>
        `;

        return tr;
    }

    function handleRowAdd(event) {
        const target = event.target.closest('.row-add-btn');

        if (!target) return;

        const currentRow = target.closest('.money-amount-row');
        const newRow = buildNewRowElement();
        const createdText = newRow.querySelector('.time-text');
        const now = new Date();
        const formattedTime = formatDateTimeForDb(now);
        createdText.textContent = formattedTime;

        if (currentRow && currentRow.nextSibling) {
            tableBody.insertBefore(newRow, currentRow.nextSibling);
        } else {
            tableBody.appendChild(newRow);
        }

        updateOrdinalNumbers();
        calculateTotalAmount();
    }

    function handleRowRemove(event) {
        const target = event.target.closest('.row-remove-btn');

        if (!target) return;

        const currentRow = target.closest('.money-amount-row');
        const budgetUpdateId = currentRow.dataset.id;
        const rows = tableBody.querySelectorAll('.money-amount-row');

        if (budgetUpdateId) {
            fetch(`/sponsors/sponsorships/${budgetUpdateId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! Status: ${response.status}, Response text: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        currentRow.remove();
                        updateOrdinalNumbers();
                        calculateTotalAmount();
                    } else {
                        alert('Error deleting sponsorship: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred.');
                });
        } else {
            if (rows.length > 1) {
                currentRow.remove();
            } else {
                currentRow.querySelector('.sponsorship-input').value = '0.00';
            }
            updateOrdinalNumbers();
            calculateTotalAmount();
        }
    }

    tableBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('sponsorship-input')) {
            calculateTotalAmount();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.row-add-btn')) {
            handleRowAdd(e);
        } else if (e.target.closest('.row-remove-btn')) {
            handleRowRemove(e);
        }
    });

    updateBtn.addEventListener('click', function () {
        const rows = tableBody.querySelectorAll('.money-amount-row');
        const sponsorships = [];
        const sponsorId = window.sponsorshipConfig.sponsorId;

        rows.forEach(row => {
            const id = row.dataset.id || null;
            const amountInput = row.querySelector('.sponsorship-input');
            const amount = parseFloat(amountInput.value) || 0;
            const createdText = row.querySelector('.time-text');
            const createdAt = createdText ? createdText.textContent : (row.querySelector('.time-sponsored-cell') ? row.querySelector('.time-sponsored-cell').dataset.created || null : null);

            sponsorships.push({ id, amount, created_at: createdAt });
        });

        fetch(`/sponsors/sponsorships/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ contributions: sponsorships, sponsorId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error updating sponsorships: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
    });

    updateOrdinalNumbers();
    calculateTotalAmount();
});
