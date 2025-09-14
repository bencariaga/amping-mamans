document.addEventListener('DOMContentLoaded', function () {
    const triggers = Array.from(document.querySelectorAll('.service-modal-trigger'));
    const modalEl = document.getElementById('tariffServiceModal');
    const modalBody = modalEl ? modalEl.querySelector('.modal-body') : null;
    const modalTitle = modalEl ? modalEl.querySelector('.modal-title') : null;
    let bootstrapModalInstance = null;

    function createSpinner() {
        const spinnerWrap = document.createElement('div');
        spinnerWrap.className = 'text-center p-4';
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border';
        spinner.setAttribute('role', 'status');
        spinnerWrap.appendChild(spinner);
        return spinnerWrap;
    }

    async function loadServiceContent(url, serviceName) {
        if (!modalBody) return;
        modalBody.innerHTML = '';
        modalBody.appendChild(createSpinner());

        try {
            const res = await fetch(url, { credentials: 'same-origin' });

            if (!res.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const mainContent = doc.querySelector('#tariffCarousel');

            if (mainContent) {
                modalBody.innerHTML = '';
                const mainClone = mainContent.cloneNode(true);
                modalBody.appendChild(mainClone);

                const carouselEl = modalBody.querySelector('#tariffCarousel');
                const bsCarousel = (typeof bootstrap !== 'undefined' && carouselEl) ? new bootstrap.Carousel(carouselEl, { ride: false, interval: false }) : null;
                const formControls = modalBody.querySelectorAll('.form-control');

                formControls.forEach(control => {
                    control.setAttribute('readonly', 'readonly');
                });

                if (bsCarousel) {
                    const allItems = Array.from(carouselEl.querySelectorAll('.carousel-item'));
                    const targetItem = allItems.find(i => i.getAttribute('data-service-type') === serviceName);

                    if (targetItem) {
                        const idx = allItems.indexOf(targetItem);
                        if (idx >= 0) {
                            bsCarousel.to(idx);
                        }
                    }
                }
            } else {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'p-4';
                emptyDiv.textContent = 'No tariff data available for this service in this version.';
                emptyDiv.style.color = 'var(--text-color)';
                modalBody.appendChild(emptyDiv);
            }
        } catch (e) {
            modalBody.innerHTML = '';
            const errDiv = document.createElement('div');
            errDiv.className = 'p-4 text-danger';
            errDiv.textContent = 'Failed to load tariff data.';
            modalBody.appendChild(errDiv);
        }
    }

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function (ev) {
            ev.preventDefault();
            const url = this.getAttribute('data-tariff-url');
            const service = this.getAttribute('data-service');

            if (!modalEl) return;
            if (!bootstrapModalInstance) bootstrapModalInstance = new bootstrap.Modal(modalEl);
            if (modalTitle) modalTitle.textContent = service;

            bootstrapModalInstance.show();
            loadServiceContent(url, service);
        });
    });

    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            if (modalBody) modalBody.innerHTML = '';
        });
    }
});
