document.addEventListener('DOMContentLoaded', function () {
    const deleteTariffModal = new bootstrap.Modal(document.getElementById('deleteTariffModal'));
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    const tariffIdDisplay = document.getElementById('delete-tariff-id-display');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentTariffListId = null;

    window.closeDeleteModal = function () {
        currentTariffListId = null;
        deleteTariffModal.hide();
    };

    window.showDeleteTariffModal = function (tariffListId) {
        currentTariffListId = tariffListId;
        tariffIdDisplay.textContent = `${tariffListId}`;
        deleteTariffModal.show();
    };

    confirmDeleteButton.addEventListener('click', async function () {
        if (!currentTariffListId) {
            alert('Invalid tariff list ID.');
            window.closeDeleteModal();
            return;
        }

        confirmDeleteButton.disabled = true;

        try {
            const response = await fetch(`/tariff-lists/${currentTariffListId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                alert('Tariff list version has been deleted.');
                window.refreshTariffTable();
            } else if (response.status === 404) {
                alert(result.message || 'Tariff list not found. It may have already been deleted.');
                window.refreshTariffTable();
            } else {
                alert(result.message || 'Failed to delete tariff list. Please try again.');
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('A network error occurred. Please try again.');
        } finally {
            confirmDeleteButton.disabled = false;
            window.closeDeleteModal();
        }
    });
});
