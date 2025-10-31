document.addEventListener("DOMContentLoaded", function () {
    const updateClock = () => {
        const now = new Date();

        const options = {
            timeZone: "Asia/Manila",
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        };

        const formatter = new Intl.DateTimeFormat("en-US", options);
        const formattedDate = formatter.format(now);
        const [month, day, year, time, ampm] = formattedDate
            .replace(",", "")
            .split(" ");
        const formattedTime = `${time} ${ampm}`;
        const finalFormat = `${month}. ${day}, ${year} ${formattedTime}`;

        const clockElement = document.getElementById("live-clock");
        if (clockElement) {
            clockElement.textContent = finalFormat;
        }
    };

    updateClock();
    setInterval(updateClock, 1000);

    const formatCurrency = (amount) => {
        const value = Number(amount);
        if (Number.isNaN(value)) return "N/A";

        return new Intl.NumberFormat("en-PH", {
            style: "currency",
            currency: "PHP",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const updateBudgetDisplay = (data) => {
        const allocatedElement = document.getElementById(
            "allocated-budget-amount"
        );
        const usedElement = document.getElementById("budget-used-amount");
        const remainingElement = document.getElementById(
            "remaining-budget-amount"
        );
        const supplementaryElement = document.getElementById(
            "supplementary-budget-status"
        );

        if (data && Object.keys(data).length) {
            if (allocatedElement)
                allocatedElement.innerText = formatCurrency(data.amount_accum);
            if (usedElement)
                usedElement.innerText = formatCurrency(data.amount_change);
            if (remainingElement)
                remainingElement.innerText = formatCurrency(data.amount_recent);

            if (supplementaryElement) {
                supplementaryElement.innerText = data.has_supplementary_budget
                    ? "YES"
                    : "NO";
            }
        } else {
            handleBudgetError("N/A");
        }
    };

    const handleBudgetError = (message = "Error") => {
        const elements = [
            "allocated-budget-amount",
            "budget-used-amount",
            "remaining-budget-amount",
            "supplementary-budget-status",
        ];

        elements.forEach((id) => {
            const element = document.getElementById(id);
            if (element) element.innerText = message;
        });
    };

    const fetchBudgetData = async () => {
        try {
            const response = await fetch("/api/latest-budget", {
                credentials: "same-origin",
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            updateBudgetDisplay(data);
        } catch (error) {
            console.error("Error fetching budget data:", error);
            handleBudgetError();
        }
    };

    fetchBudgetData();
});
