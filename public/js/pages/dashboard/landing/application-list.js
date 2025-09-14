document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(buttonId, inputId, isFilter = false) {
        const dropdownButton = document.getElementById(buttonId);
        const hiddenInput = document.getElementById(inputId);
        const menu = dropdownButton ? dropdownButton.nextElementSibling : null;
        if (!menu) return;
        const dropdownItems = menu.querySelectorAll('.dropdown-item');

        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const value = this.getAttribute('data-value');
                const text = this.textContent.trim();
                hiddenInput.value = value;
                dropdownButton.textContent = text;
                dropdownItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                if (isFilter) {
                    document.getElementById('application-filter-form').submit();
                }
            });
        });

        const initialValue = hiddenInput.value;
        const initialTextElement = Array.from(dropdownItems).find(item => item.getAttribute('data-value') === initialValue);
        if (initialTextElement) {
            dropdownButton.textContent = initialTextElement.textContent.trim();
            initialTextElement.classList.add('active');
        } else if (dropdownItems.length > 0) {
            dropdownButton.textContent = dropdownItems[0].textContent.trim();
            dropdownItems[0].classList.add('active');
            hiddenInput.value = dropdownItems[0].getAttribute('data-value');
        }

        dropdownButton.addEventListener('show.bs.dropdown', function () {
            this.classList.add('rotated');
        });

        dropdownButton.addEventListener('hide.bs.dropdown', function () {
            this.classList.remove('rotated');
        });
    }

    setupDropdown('sortDropdownBtn', 'filter-sort-by', true);
    setupDropdown('perPageDropdownBtn', 'filter-per-page', true);

    const searchInput = document.getElementById('filter-search');
    let searchTimeout = null;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                document.getElementById('application-filter-form').submit();
            }, 500);
        });
    }

    document.querySelectorAll('.details-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const applicationId = this.dataset.applicationId;
            showApplicationDetails(applicationId);
        });
    });

    let currentApplicationId = null;

    document.querySelectorAll('.authorize-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentApplicationId = this.dataset.applicationId;
            showModal('approveModal');
        });
    });

    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentApplicationId = this.dataset.applicationId;
            showModal('rejectModal');
        });
    });

    const confirmAuthorize = document.getElementById('confirmAuthorize');
    if (confirmAuthorize) {
        confirmAuthorize.addEventListener('click', function () {
            if (!currentApplicationId) return;
            processApplication(currentApplicationId, 'authorize');
        });
    }

    const confirmReject = document.getElementById('confirmReject');
    if (confirmReject) {
        confirmReject.addEventListener('click', function () {
            if (!currentApplicationId) return;
            processApplication(currentApplicationId, 'reject');
        });
    }

    document.querySelectorAll('.preview-btn').forEach(a => {
        a.addEventListener('click', function (e) {
            const disabled = this.classList.contains('disabled') || this.getAttribute('aria-disabled') === 'true';
            if (disabled) {
                e.preventDefault();
                return;
            }
        });
    });
});

function showModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.style.display = 'flex';
}

function closeModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.style.display = 'none';
}

function showApplicationDetails(applicationId) {
    fetch(`/applications/${applicationId}/details`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            document.getElementById('detailsModalBody').innerHTML = data.html || '<div>No details available.</div>';
            showModal('detailsModal');
        })
        .catch(error => {
            document.getElementById('detailsModalBody').innerHTML = '<div class="error-message">Failed to load details.</div>';
            showModal('detailsModal');
        });
}

function processApplication(applicationId, action) {
    const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;
    fetch(`/applications/${applicationId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({})
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`[data-application-id="${applicationId}"]`).closest('tr');
                if (row) {
                    const statusCell = row.querySelector('.badge');
                    const actionsDiv = row.querySelector('.action-buttons .d-flex');

                    if (statusCell) {
                        statusCell.textContent = data.status;
                        statusCell.classList.remove('bg-warning', 'bg-danger', 'text-black');
                        if (data.status === 'Approved') {
                            statusCell.classList.add('bg-success');
                        } else if (data.status === 'Rejected') {
                            statusCell.classList.add('bg-danger');
                        }
                    }

                    const authorizeBtn = actionsDiv.querySelector('.authorize-btn');
                    const rejectBtn = actionsDiv.querySelector('.reject-btn');
                    if (authorizeBtn) authorizeBtn.disabled = true;
                    if (rejectBtn) rejectBtn.disabled = true;

                    const previewBtn = actionsDiv.querySelector('.preview-btn');
                    if (previewBtn) {
                        if (data.previewEnabled) {
                            previewBtn.classList.remove('disabled');
                            previewBtn.setAttribute('aria-disabled', 'false');
                            previewBtn.href = data.previewUrl;
                        } else {
                            previewBtn.classList.add('disabled');
                            previewBtn.setAttribute('aria-disabled', 'true');
                            previewBtn.href = '#';
                        }
                    }
                }
                showFeedbackMessage('success', 'Success', data.message);
            } else {
                showFeedbackMessage('danger', 'Error', data.message || 'An error occurred.');
            }
        })
        .catch(error => {
            showFeedbackMessage('danger', 'Error', 'An unexpected error occurred.');
        })
        .finally(() => {
            if (action === 'authorize') closeModal('approveModal');
            if (action === 'reject') closeModal('rejectModal');
        });
}
