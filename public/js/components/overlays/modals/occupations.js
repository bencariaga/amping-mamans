document.addEventListener('DOMContentLoaded', () => {
    const occupationsModalOverlay = document.getElementById('occupations-modal-overlay');
    const occupationsModalClose = document.getElementById('occupations-modal-close');
    const addOccupationForm = document.getElementById('add-occupation-form');
    const newOccupationNameInput = document.getElementById('new-occupation-name');
    const newOccupationNameError = document.getElementById('new-occupation-name-error');
    const occupationsList = document.getElementById('occupations-list');
    const confirmChangesBtn = document.getElementById('confirm-occupations-changes');
    const cancelChangesBtn = document.getElementById('cancel-occupations-changes');

    let occupations = [];
    let pendingChanges = {
        added: [],
        edited: [],
        deleted: []
    };

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.getAttribute) {
            return meta.getAttribute('content');
        }
        const inputToken = document.querySelector('input[name="_token"]');
        return inputToken ? inputToken.value : '';
    };

    const fetchOccupations = async () => {
        try {
            const response = await fetch('/api/occupations');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            occupations = data.map(occupation => ({
                id: occupation.occupation_id,
                name: occupation.occupation,
                status: 'existing'
            }));
            renderOccupations();
        } catch (error) {
            console.error('Error fetching occupations:', error);
        }
    };

    const renderOccupations = () => {
        occupationsList.innerHTML = '';
        occupations.forEach(occupation => {
            if (occupation.status === 'deleted') return;
            const listItem = document.createElement('li');
            listItem.id = `occupation-item-${occupation.id}`;
            listItem.classList.add('occupation-item');
            if (occupation.status === 'new') {
                listItem.classList.add('new-item');
            } else if (occupation.status === 'edited') {
                listItem.classList.add('edited-item');
            }
            if (occupation.editing) {
                listItem.innerHTML = `
                    <form class="editing-form" data-id="${occupation.id}">
                        <div class="form-group">
                            <input type="text" value="${occupation.name}" class="editing-occupation-name" id="editing-occupation-name">
                            <span class="error-message editing-occupation-name-error" style="display: none;"></span>
                        </div>
                        <div class="button-group">
                            <button type="submit" class="btn btn-success btn-sm" id="saveBtn">SAVE</button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-id="${occupation.id}" id="cancelBtn">CANCEL</button>
                        </div>
                    </form>
                `;
            } else {
                listItem.innerHTML = `
                    <div class="occupation-details">
                        <div class="occupation-name">${occupation.name}</div>
                    </div>
                    <div class="occupation-actions">
                        <button class="btn btn-info btn-sm edit-occupation-btn" data-id="${occupation.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.35-.350.106-.106-.35-.35-.106a.5.5 0 0 1 .106-.35l.35-.106zM6.5 13H5v1.5a.5.5 0 0 1-.5.5h-.5a.5.5 0 0 1-.5-.5V13h-.5a.5.5 0 0 1-.5-.5v-.5a.5.5 0 0 1 .5-.5h.5V11a.5.5 0 0 1 .5-.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 0 1-.5.5z"/>
                            </svg>
                        </button>
                        <button class="btn btn-danger btn-sm delete-occupation-btn" data-id="${occupation.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                            </svg>
                        </button>
                    </div>
                `;
            }
            occupationsList.appendChild(listItem);
        });

        document.querySelectorAll('.edit-occupation-btn').forEach(button => {
            button.onclick = (e) => editOccupation(e.currentTarget.dataset.id);
        });

        document.querySelectorAll('.delete-occupation-btn').forEach(button => {
            button.onclick = (e) => deleteOccupation(e.currentTarget.dataset.id);
        });

        document.querySelectorAll('.editing-form').forEach(form => {
            form.onsubmit = (e) => {
                e.preventDefault();
                updateOccupation(form.dataset.id);
            };
        });

        document.querySelectorAll('.cancel-edit-btn').forEach(button => {
            button.onclick = (e) => cancelEdit(e.currentTarget.dataset.id);
        });
    };

    const showValidationError = (element, message) => {
        element.textContent = message;
        element.style.display = 'block';
    };

    const hideValidationError = (element) => {
        element.textContent = '';
        element.style.display = 'none';
    };

    if (addOccupationForm) {
        addOccupationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const newName = newOccupationNameInput.value.trim();
            if (newName.length < 3) {
                showValidationError(newOccupationNameError, 'Occupation name must be at least 3 characters.');
                return;
            }
            hideValidationError(newOccupationNameError);
            const tempId = `new-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
            const newOccupation = {
                id: tempId,
                name: newName,
                status: 'new'
            };
            occupations.push(newOccupation);
            pendingChanges.added.push(newOccupation);
            newOccupationNameInput.value = '';
            renderOccupations();
        });
    }

    const editOccupation = (occupationId) => {
        const occupationIndex = occupations.findIndex(o => o.id === occupationId);
        if (occupationIndex > -1) {
            occupations.forEach(o => o.editing = false);
            occupations[occupationIndex].editing = true;
            renderOccupations();
        }
    };

    const updateOccupation = (occupationId) => {
        const occupationIndex = occupations.findIndex(o => o.id === occupationId);
        if (occupationIndex > -1) {
            const currentItem = occupationsList.querySelector(`#occupation-item-${occupationId}`);
            const editingNameInput = currentItem.querySelector('.editing-occupation-name');
            const editingNameError = currentItem.querySelector('.editing-occupation-name-error');
            const updatedName = editingNameInput.value.trim();
            if (updatedName.length < 3) {
                showValidationError(editingNameError, 'Occupation name must be at least 3 characters.');
                return;
            }
            hideValidationError(editingNameError);
            occupations[occupationIndex].name = updatedName;
            occupations[occupationIndex].editing = false;
            if (occupations[occupationIndex].status === 'existing') {
                occupations[occupationIndex].status = 'edited';
                const existingEditedIndex = pendingChanges.edited.findIndex(item => item.id === occupationId);
                if (existingEditedIndex > -1) {
                    pendingChanges.edited[existingEditedIndex] = { ...occupations[occupationIndex] };
                } else {
                    pendingChanges.edited.push({ ...occupations[occupationIndex] });
                }
            } else if (occupations[occupationIndex].status === 'new') {
                const existingAddedIndex = pendingChanges.added.findIndex(item => item.id === occupationId);
                if (existingAddedIndex > -1) {
                    pendingChanges.added[existingAddedIndex] = { ...occupations[occupationIndex] };
                }
            }
            renderOccupations();
        }
    };

    const cancelEdit = (occupationId) => {
        const occupationIndex = occupations.findIndex(o => o.id === occupationId);
        if (occupationIndex > -1) {
            if (occupations[occupationIndex].status === 'edited') {
                const original = pendingChanges.edited.find(item => item.id === occupationId);
                if (original) {
                    occupations[occupationIndex].name = original.name;
                }
            }

            occupations[occupationIndex].editing = false;
            renderOccupations();
        }
    };

    const deleteOccupation = async (occupationId) => {
        const occupationIndex = occupations.findIndex(o => o.id === occupationId);
        if (occupationIndex === -1) return;

        if (occupations[occupationIndex].status === 'new' || occupationId.startsWith('new-')) {
            occupations.splice(occupationIndex, 1);
            pendingChanges.added = pendingChanges.added.filter(item => item.id !== occupationId);
            renderOccupations();
            return;
        }

        if (!confirm('Are you sure you want to delete this occupation? This action cannot be undone and may affect associated client records.')) {
            return;
        }

        try {
            const csrfToken = getCsrfToken();
            const response = await fetch(`/occupations/${encodeURIComponent(occupationId)}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Failed to delete occupation.');
            }

            occupations = occupations.filter(o => o.id !== occupationId);
            pendingChanges.edited = pendingChanges.edited.filter(item => item.id !== occupationId);
            pendingChanges.deleted = pendingChanges.deleted.filter(id => id !== occupationId);


            renderOccupations();
        } catch (err) {
            console.error('Error deleting occupation:', err);
            alert('Error deleting occupation: ' + (err.message || 'unknown error'));
        }
    };


    if (confirmChangesBtn) {
        confirmChangesBtn.addEventListener('click', async () => {
            const changesToSend = {
                create: pendingChanges.added.map(occupation => ({
                    occupation: occupation.name
                })),
                update: pendingChanges.edited.map(occupation => ({
                    occupation_id: occupation.id,
                    occupation: occupation.name
                })),
                delete: pendingChanges.deleted
            };

            try {
                const csrfToken = getCsrfToken();
                const response = await fetch('/occupations/confirm-changes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(changesToSend)
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.error || 'Failed to confirm changes.');
                }

                if (Array.isArray(result.occupations)) {
                    occupations = result.occupations.map(occupation => ({
                        id: occupation.id,
                        name: occupation.name,
                        status: 'existing'
                    }));
                } else {
                    await fetchOccupations();
                }

                pendingChanges = { added: [], edited: [], deleted: [] };

                if (occupationsModalOverlay) occupationsModalOverlay.style.display = 'none';

                renderOccupations();
            } catch (error) {
                console.error('Error confirming changes:', error);
                alert('Failed to save changes: ' + error.message);
            }
        });
    }

    if (cancelChangesBtn) {
        cancelChangesBtn.addEventListener('click', () => {
            pendingChanges = {
                added: [],
                edited: [],
                deleted: []
            };

            if (occupationsModalOverlay) occupationsModalOverlay.style.display = 'none';
            fetchOccupations();
        });
    }

    if (occupationsModalClose) {
        occupationsModalClose.addEventListener('click', () => {
            pendingChanges = {
                added: [],
                edited: [],
                deleted: []
            };

            if (occupationsModalOverlay) occupationsModalOverlay.style.display = 'none';
            fetchOccupations();
        });
    }

    window.openOccupationsModal = () => {
        if (occupationsModalOverlay) occupationsModalOverlay.style.display = 'flex';
        fetchOccupations();
    };

    fetchOccupations();
});
