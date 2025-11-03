document.addEventListener('DOMContentLoaded', function() {
    const addServiceDropdown = document.getElementById('addServiceDropdown');
    const addServiceContainer = document.getElementById('addServiceDropdownContainer');
    const tariffListId = document.getElementById('tariffListId').value;
    const editServicesBtn = document.getElementById('editServicesBtn');
    const saveServicesBtn = document.getElementById('saveServicesBtn');

    let isEditMode = false;
    let isAdding = false;
    let removingSet = new Set();

    function getCsrfToken() {
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        return tokenElement ? tokenElement.content : null;
    }

    function updateAddServiceDropdownVisibility() {
        if (!addServiceDropdown || !addServiceContainer) return;
        const hasAvailableOptions = addServiceDropdown.querySelectorAll('option:not([value=""]):not([disabled])').length > 0;
        addServiceContainer.style.display = isEditMode && hasAvailableOptions ? 'block' : 'none';

        if (!isEditMode) {
            addServiceDropdown.value = '';
        }
    }

    if (editServicesBtn) {
        editServicesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleEditMode(true);
        });
    }

    if (saveServicesBtn) {
        saveServicesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleEditMode(false);
        });
    }

    function toggleEditMode(editMode) {
        isEditMode = editMode;
        const viewModeElements = document.querySelectorAll('.view-mode');
        const editModeElements = document.querySelectorAll('.edit-mode');
        const removeButtons = document.querySelectorAll('.btn-remove-service');

        if (isEditMode) {
            viewModeElements.forEach(el => {
                el.style.display = 'none';
            });

            editModeElements.forEach(el => {
                if (el.classList.contains('footer-btn')) {
                    el.style.display = 'flex';
                } else {
                    el.style.display = el.classList.contains('alert') ? 'block' : 'flex';
                }
            });
            removeButtons.forEach(btn => {
                btn.style.display = 'block';

                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (this.disabled) {
                        return;
                    }

                    const serviceId = this.getAttribute('data-service-id');
                    const serviceName = this.getAttribute('data-service-type');

                    if (window.confirm(`Are you sure you want to remove "${serviceName}" from this tariff list?`)) {
                        removeService(serviceId, serviceName);
                    }
                };
            });

            updateAddServiceDropdownVisibility();
        } else {
            viewModeElements.forEach(el => {
                if (el.classList.contains('footer-btn')) {
                    el.style.display = 'flex';
                } else {
                    el.style.display = el.classList.contains('alert') ? 'block' : 'flex';
                }
            });
            editModeElements.forEach(el => {
                el.style.display = 'none';
            });
            removeButtons.forEach(btn => {
                btn.style.display = 'none';
                btn.onclick = null;
            });

            updateAddServiceDropdownVisibility();
        }
    }

    if (addServiceDropdown) {
        addServiceDropdown.addEventListener('change', function() {
            const serviceId = this.value;

            if (!serviceId) {
                return;
            }

            const selectedOption = this.options[this.selectedIndex];
            const serviceName = selectedOption.text;

            if (window.confirm(`Are you sure you want to add "${serviceName}" to this tariff list?`)) {
                if (isAdding) return;
                addService(serviceId, serviceName);
            } else {
                addServiceDropdown.value = '';
            }
        });
    }

    function addService(serviceId, serviceName) {
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            alert('Error: CSRF token is missing. Cannot proceed with the request.');
            return;
        }

        isAdding = true;
        if (addServiceDropdown) addServiceDropdown.disabled = true;

        fetch(`/tariff-lists/${tariffListId}/add-service`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                service_id: serviceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                try {
                    if (addServiceDropdown) {
                        const opt = addServiceDropdown.querySelector(`option[value="${serviceId}"]`);
                        if (opt) opt.remove();

                        addServiceDropdown.value = '';

                        updateAddServiceDropdownVisibility();
                    }

                    const tabsUl = document.getElementById('serviceTabs');
                    const tabsContent = document.getElementById('serviceTabsContent');
                    if (tabsUl && tabsContent) {
                        const existingLis = tabsUl.querySelectorAll('li.nav-item[role="presentation"][data-service-id]');
                        const nextIndex = existingLis.length;

                        const currentActiveBtn = tabsUl.querySelector('button.nav-link.active');
                        if (currentActiveBtn) currentActiveBtn.classList.remove('active');
                        const currentActivePane = tabsContent.querySelector('.tab-pane.show.active');
                        if (currentActivePane) currentActivePane.classList.remove('show', 'active');

                        const li = document.createElement('li');
                        li.className = 'nav-item';
                        li.setAttribute('role', 'presentation');
                        li.setAttribute('data-service-type', serviceName);
                        li.setAttribute('data-service-id', serviceId);

                        const wrapper = document.createElement('div');
                        wrapper.className = 'service-tab-wrapper';

                        const btn = document.createElement('button');
                        btn.className = 'nav-link active';
                        btn.id = `tab-${nextIndex}`;
                        btn.setAttribute('data-bs-toggle', 'tab');
                        btn.setAttribute('data-bs-target', `#service-${nextIndex}`);
                        btn.type = 'button';
                        btn.setAttribute('role', 'tab');
                        btn.setAttribute('aria-controls', `service-${nextIndex}`);
                        btn.setAttribute('aria-selected', 'true');
                        btn.textContent = serviceName;

                        const del = document.createElement('button');
                        del.type = 'button';
                        del.className = 'btn-remove-service';
                        del.setAttribute('data-service-type', serviceName);
                        del.setAttribute('data-service-id', serviceId);
                        del.title = 'Remove this service type';
                        del.style.display = 'block';
                        del.innerHTML = '<i class="fas fa-trash"></i>';

                        del.onclick = function(e){
                            e.preventDefault();
                            e.stopPropagation();
                            if (this.disabled) return;
                            if(window.confirm(`Are you sure you want to remove "${serviceName}" from this tariff list?`)){
                                removeService(serviceId, serviceName);
                            }
                        };

                        wrapper.appendChild(btn);
                        wrapper.appendChild(del);
                        li.appendChild(wrapper);
                        const addContainer = document.getElementById('addServiceDropdownContainer');
                        if (addContainer) tabsUl.insertBefore(li, addContainer); else tabsUl.appendChild(li);

                        const pane = document.createElement('div');
                        pane.className = 'tab-pane fade show active';
                        pane.id = `service-${nextIndex}`;
                        pane.setAttribute('role', 'tabpanel');
                        pane.setAttribute('aria-labelledby', `tab-${nextIndex}`);
                        pane.innerHTML = `
                            <div class="alert alert-info view-mode" id="alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Service: ${serviceName}</strong><br>
                                Click "Edit Services" button below to add or remove services.
                            </div>
                            <div class="alert alert-info edit-mode" id="alert-info" style="display: none;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Service: ${serviceName}</strong><br>
                                You can add or remove services using the buttons above.
                            </div>`;
                        tabsContent.appendChild(pane);

                        const countEl = document.querySelector('.service-count');
                        if (countEl) {
                            const match = countEl.textContent.match(/(\d+)/);
                            const n = match ? parseInt(match[1]) + 1 : existingLis.length + 1;
                            countEl.textContent = `TL Version's Number of Services: ${n}`;
                        }

                        const removeButtons = document.querySelectorAll('.btn-remove-service');
                        if (removeButtons.length > 1) {
                            removeButtons.forEach(b => b.disabled = false);
                        }
                    }

                    alert(data.message);
                } catch (e) {
                    console.error('DOM update failed after addService:', e);
                    window.location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add service. Please try again.');
        })
        .finally(() => {
            isAdding = false;
            if (addServiceDropdown) addServiceDropdown.disabled = false;
        });
    }

    function removeService(serviceId, serviceName) {
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            alert('Error: CSRF token is missing. Cannot proceed with the request.');
            return;
        }

        if (removingSet.has(serviceId)) return;
        removingSet.add(serviceId);

        const url = `/tariff-lists/${tariffListId}/remove-service`;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                service_id: serviceId
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.error || 'Network response was not ok'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                try {
                    const tabsUl = document.getElementById('serviceTabs');
                    const li = tabsUl.querySelector(`li.nav-item[role="presentation"][data-service-id="${serviceId}"]`);

                    if (li) {
                        const btn = li.querySelector('button.nav-link');
                        const target = btn ? btn.getAttribute('data-bs-target') : null;

                        li.remove();

                        if (target) {
                            const pane = document.querySelector(target);
                            if (pane) pane.remove();
                        }
                    }

                    const firstBtn = tabsUl.querySelector('button.nav-link');
                    if (firstBtn) {
                        const target = firstBtn.getAttribute('data-bs-target');
                        firstBtn.classList.add('active');
                        const pane = target ? document.querySelector(target) : null;
                        if (pane) pane.classList.add('show', 'active');
                    }

                    if (addServiceDropdown) {
                        const existsOpt = addServiceDropdown.querySelector(`option[value="${serviceId}"]`);
                        if (!existsOpt) {
                            const opt = document.createElement('option');
                            opt.value = serviceId;
                            opt.textContent = serviceName;
                            addServiceDropdown.appendChild(opt);
                        }

                        updateAddServiceDropdownVisibility();
                    }

                    const countEl = document.querySelector('.service-count');
                    if (countEl) {
                        const match = countEl.textContent.match(/(\d+)/);
                        const n = match ? Math.max(0, parseInt(match[1]) - 1) : 0;
                        countEl.textContent = `TL Version's Number of Services: ${n}`;
                    }

                    const removeButtons = document.querySelectorAll('.btn-remove-service');
                    if (removeButtons.length <= 1) {
                        removeButtons.forEach(b => b.disabled = true);
                    }

                    alert(data.message);
                } catch (e) {
                    console.error('DOM update failed after removeService:', e);
                    window.location.reload();
                }
            } else {
                throw new Error(data.error || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Failed to remove service. Please try again.');
        })
        .finally(() => {
            removingSet.delete(serviceId);
        });
    }

    if (!isEditMode) {
        if (addServiceContainer) addServiceContainer.style.display = 'none';
        if (addServiceDropdown) addServiceDropdown.value = '';
    }
});
