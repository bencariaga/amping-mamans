document.addEventListener('DOMContentLoaded', function () {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function isLeapYear(y) {
        return (y % 4 === 0 && y % 100 !== 0) || (y % 400 === 0);
    }

    function daysInMonth(y, m) {
        const d = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        return m === 1 && isLeapYear(y) ? 29 : d[m];
    }

    function setupDateDropdown(btnId, inputId, populateFn) {
        const btn = document.getElementById(btnId),
            input = document.getElementById(inputId),
            menu = btn.nextElementSibling;
        populateFn(menu, btn, input);

        menu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();
                item.parentElement.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                btn.textContent = item.textContent.trim();
                input.value = item.getAttribute('data-value');
            });
        });

        btn.addEventListener('show.bs.dropdown', () => btn.classList.add('rotated'));
        btn.addEventListener('hide.bs.dropdown', () => btn.classList.remove('rotated'));
    }

    setupDateDropdown('dateYearDropdownBtn', 'dateYearSelect', (menu, btn, input) => {
        const cy = new Date().getFullYear(), sy = 2018;
        let h = '';

        for (let i = cy; i >= sy; i--) {
            h += `<li><a class="dropdown-item" href="#" data-value="${i}">${i}</a></li>`;
        }

        menu.innerHTML = h;
        const first = menu.querySelector('.dropdown-item');

        if (first) {
            btn.textContent = first.textContent;
            input.value = first.getAttribute('data-value');
        }
    });

    setupDateDropdown('dateMonthDropdownBtn', 'dateMonthSelect', (menu, btn, input) => {
        let h = '';

        monthNames.forEach((m, i) => {
            h += `<li><a class="dropdown-item" href="#" data-value="${i}">${m}</a></li>`;
        });

        menu.innerHTML = h;
        const today = new Date().getMonth(),
            item = menu.querySelector(`[data-value="${today}"]`);

        if (item) {
            btn.textContent = item.textContent;
            input.value = item.getAttribute('data-value');
        }
    });

    setupDateDropdown('dateDayDropdownBtn', 'dateDaySelect', (menu, btn, input) => {
        const y = parseInt(document.getElementById('dateYearSelect').value),
            m = parseInt(document.getElementById('dateMonthSelect').value),
            dim = daysInMonth(y, m);
        let h = '';

        for (let i = 1; i <= dim; i++) {
            h += `<li><a class="dropdown-item" href="#" data-value="${i}">${i}</a></li>`;
        }

        menu.innerHTML = h;
        const today = new Date().getDate(),
            item = menu.querySelector(`[data-value="${today}"]`);

        if (item) {
            btn.textContent = item.textContent;
            input.value = item.getAttribute('data-value');
        }
    });

    document.getElementById('dateYearDropdownBtn').addEventListener('hide.bs.dropdown', () => {
        setupDateDropdown('dateDayDropdownBtn', 'dateDaySelect', (menu, btn, input) => {
            const y = parseInt(document.getElementById('dateYearSelect').value),
                m = parseInt(document.getElementById('dateMonthSelect').value),
                dim = daysInMonth(y, m);
            let h = '';

            for (let i = 1; i <= dim; i++) {
                h += `<li><a class="dropdown-item" href="#" data-value="${i}">${i}</a></li>`;
            }

            menu.innerHTML = h;
            const currentDay = parseInt(input.value);
            const newItem = menu.querySelector(`[data-value="${currentDay}"]`) || menu.querySelector(`[data-value="1"]`);

            if (newItem) {
                btn.textContent = newItem.textContent;
                input.value = newItem.getAttribute('data-value');
            }
        });
    });

    document.getElementById('dateMonthDropdownBtn').addEventListener('hide.bs.dropdown', () => {
        setupDateDropdown('dateDayDropdownBtn', 'dateDaySelect', (menu, btn, input) => {
            const y = parseInt(document.getElementById('dateYearSelect').value),
                m = parseInt(document.getElementById('dateMonthSelect').value),
                dim = daysInMonth(y, m);
            let h = '';

            for (let i = 1; i <= dim; i++) {
                h += `<li><a class="dropdown-item" href="#" data-value="${i}">${i}</a></li>`;
            }

            menu.innerHTML = h;
            const currentDay = parseInt(input.value);
            const newItem = menu.querySelector(`[data-value="${currentDay}"]`) || menu.querySelector(`[data-value="1"]`);

            if (newItem) {
                btn.textContent = newItem.textContent;
                input.value = newItem.getAttribute('data-value');
            }
        });
    });
});
