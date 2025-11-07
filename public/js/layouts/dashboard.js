document.addEventListener('DOMContentLoaded', function () {
    const updateClock = () => {
        const now = new Date();

        const options = {
            timeZone: 'Asia/Manila',
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const formatter = new Intl.DateTimeFormat('en-US', options);
        const formattedDate = formatter.format(now);
        const [month, day, year, time, ampm] = formattedDate.replace(',', '').split(' ');
        const formattedTime = `${time} ${ampm}`;
        const finalFormat = `${month}. ${day}, ${year} ${formattedTime}`;
        document.getElementById('live-clock').textContent = finalFormat;
    };

    updateClock();
    setInterval(updateClock, 1000);

    const disabledButtons = document.querySelectorAll('#disabled-ui-component');

    disabledButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
        });
    });

    fetch('/api/latest-budget', { credentials: 'same-origin' }).then(response => {
        return response.json();
    }).then(data => {
        const formatCurrency = (amount) => {
            const value = Number(amount);
            if (Number.isNaN(value)) return 'Error';

            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        };

        if (data && Object.keys(data).length) {
            document.getElementById('allocated-budget-amount').innerText = formatCurrency(data.amount_accum);
            document.getElementById('budget-used-amount').innerText = formatCurrency(data.amount_change);
            document.getElementById('remaining-budget-amount').innerText = formatCurrency(data.amount_recent);

            const supplementaryBudgetStatusElement = document.getElementById('supplementary-budget-status');

            if (data.has_supplementary_budget) {
                supplementaryBudgetStatusElement.innerText = 'YES';
                supplementaryBudgetStatusElement.style.color = '#48b748';
            } else {
                supplementaryBudgetStatusElement.innerText = 'NO';
                supplementaryBudgetStatusElement.style.color = '#ff0000';
            }
        } else {
            document.getElementById('allocated-budget-amount').innerText = 'N/A';
            document.getElementById('budget-used-amount').innerText = 'N/A';
            document.getElementById('remaining-budget-amount').innerText = 'N/A';
            document.getElementById('supplementary-budget-status').innerText = 'N/A';
        }
    }).catch(error => {
        console.error('Error fetching budget data:', error);
        document.getElementById('allocated-budget-amount').innerText = 'Error';
        document.getElementById('budget-used-amount').innerText = 'Error';
        document.getElementById('remaining-budget-amount').innerText = 'Error';
        document.getElementById('supplementary-budget-status').innerText = 'Error';
    });
});
