document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('feedback-message-container');

    if (!container) return;

    const alerts = Array.from(container.querySelectorAll('.alert-message'));

    alerts.forEach((alert, index) => {
        setTimeout(() => {
            alert.classList.add('show');
        }, 60 + index * 110);

        const removeAlert = () => {
            if (alert.classList.contains('slide-out')) return;
            alert.classList.add('slide-out');
            alert.addEventListener('transitionend', () => {
                if (alert.parentNode) alert.parentNode.removeChild(alert);
            }, { once: true });
        };

        const closeBtn = alert.querySelector('.close-btn');

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                removeAlert();
            });
        }

        setTimeout(() => {
            removeAlert();
        }, 5000 + index * 300);
    });
});
