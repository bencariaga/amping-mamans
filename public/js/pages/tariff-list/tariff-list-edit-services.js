document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit services script loaded');
    
    const addServiceDropdown = document.getElementById('addServiceDropdown');
    const tariffListId = document.getElementById('tariffListId').value;
    const editServicesBtn = document.getElementById('editServicesBtn');
    const saveServicesBtn = document.getElementById('saveServicesBtn');

    console.log('Tariff List ID:', tariffListId);

    // SweetAlert toast helper
    const Toast = (typeof Swal !== 'undefined') ? Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
    }) : null;

    // Busy flags to avoid duplicate requests
    let isAdding = false;
    let removingSet = new Set(); // track serviceIds being removed

    // Toggle Edit Mode
    if (editServicesBtn) {
        editServicesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Edit Services button clicked');
            toggleEditMode(true);
        });
    }

    if (saveServicesBtn) {
        saveServicesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Save Changes button clicked');
            toggleEditMode(false);
        });
    }

    function toggleEditMode(isEditMode) {
        console.log('Toggle edit mode:', isEditMode);
        
        const viewModeElements = document.querySelectorAll('.view-mode');
        const editModeElements = document.querySelectorAll('.edit-mode');
        const removeButtons = document.querySelectorAll('.btn-remove-service');
        const addServiceContainer = document.getElementById('addServiceDropdownContainer');

        console.log('Remove buttons found:', removeButtons.length);

        if (isEditMode) {
            // Hide view mode, show edit mode
            viewModeElements.forEach(el => {
                el.style.display = 'none';
            });
            editModeElements.forEach(el => {
                if (el.classList.contains('footer-btn')) {
                    el.style.display = 'inline-block';
                } else {
                    el.style.display = el.classList.contains('alert') ? 'block' : 'inline-block';
                }
            });
            removeButtons.forEach(btn => {
                btn.style.display = 'block';
                console.log('Showing delete button for service:', btn.getAttribute('data-service-type'));
                
                // Attach click handler directly to each button
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (this.disabled) {
                        console.log('Button is disabled');
                        return;
                    }
                    
                    const serviceId = this.getAttribute('data-service-id');
                    const serviceName = this.getAttribute('data-service-type');
                    
                    console.log('Delete button clicked:', { serviceId, serviceName });
                    
                    Swal.fire({
                        text: `Are you sure you want to remove "${serviceName}" from this tariff list?`,
                        showCancelButton: true,
                        confirmButtonText: 'OK',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#f8f9fa',
                        cancelButtonColor: '#6c757d',
                        customClass: {
                            confirmButton: 'btn btn-light',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        reverseButtons: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            removeService(serviceId, serviceName);
                        }
                    });
                };
            });
            if (addServiceContainer) addServiceContainer.style.display = 'block';
        } else {
            // Show view mode, hide edit mode
            viewModeElements.forEach(el => {
                if (el.classList.contains('footer-btn')) {
                    el.style.display = 'inline-block';
                } else {
                    el.style.display = el.classList.contains('alert') ? 'block' : 'inline-block';
                }
            });
            editModeElements.forEach(el => {
                el.style.display = 'none';
            });
            removeButtons.forEach(btn => {
                btn.style.display = 'none';
                btn.onclick = null; // Remove click handler
            });
            if (addServiceContainer) addServiceContainer.style.display = 'none';
            
            // Reset dropdown
            if (addServiceDropdown) addServiceDropdown.value = '';
        }
    }

    // Add Service when dropdown selection changes
    if (addServiceDropdown) {
        addServiceDropdown.addEventListener('change', function() {
            const serviceId = this.value;
            
            if (!serviceId) {
                return;
            }

            const selectedOption = this.options[this.selectedIndex];
            const serviceName = selectedOption.text;

            Swal.fire({
                text: `Are you sure you want to add "${serviceName}" to this tariff list?`,
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f8f9fa',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-light',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                reverseButtons: false
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isAdding) return; // prevent double submits
                    addService(serviceId, serviceName);
                } else {
                    // Reset dropdown
                    addServiceDropdown.value = '';
                }
            });
        });
    }

    function addService(serviceId, serviceName) {
        // Loading state and disable dropdown
        isAdding = true;
        if (addServiceDropdown) addServiceDropdown.disabled = true;

        fetch(`/tariff-lists/${tariffListId}/add-service`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                service_id: serviceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                // Update UI without reload
                try {
                    // Remove added option from dropdown
                    if (addServiceDropdown) {
                        const opt = addServiceDropdown.querySelector(`option[value="${serviceId}"]`);
                        if (opt) opt.remove();

                        // Reset dropdown value
                        addServiceDropdown.value = '';

                        // If no more options, hide the add service container
                        const hasOptions = addServiceDropdown.querySelectorAll('option[value]')?.length > 0;
                        const addServiceContainer = document.getElementById('addServiceDropdownContainer');
                        if (addServiceContainer) {
                            addServiceContainer.style.display = hasOptions ? 'block' : 'none';
                        }
                    }

                    // Add a new tab for the service
                    const tabsUl = document.getElementById('serviceTabs');
                    const tabsContent = document.getElementById('serviceTabsContent');
                    if (tabsUl && tabsContent) {
                        // Compute next index based on existing service tabs (li with data-service-id)
                        const existingLis = tabsUl.querySelectorAll('li.nav-item[role="presentation"][data-service-id]');
                        const nextIndex = existingLis.length; // zero-based

                        // Deactivate current active tab
                        const currentActiveBtn = tabsUl.querySelector('button.nav-link.active');
                        if (currentActiveBtn) currentActiveBtn.classList.remove('active');
                        const currentActivePane = tabsContent.querySelector('.tab-pane.show.active');
                        if (currentActivePane) currentActivePane.classList.remove('show', 'active');

                        // Create new tab li
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

                        // Attach same onclick handler used in edit mode
                        del.onclick = function(e){
                            e.preventDefault();
                            e.stopPropagation();
                            if (this.disabled) return;
                            Swal.fire({
                                text: `Are you sure you want to remove "${serviceName}" from this tariff list?`,
                                showCancelButton: true,
                                confirmButtonText: 'OK',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#f8f9fa',
                                cancelButtonColor: '#6c757d',
                                customClass: { confirmButton: 'btn btn-light', cancelButton: 'btn btn-secondary' },
                                buttonsStyling: false,
                                reverseButtons: false
                            }).then((result)=>{ if(result.isConfirmed){ removeService(serviceId, serviceName); } });
                        };

                        wrapper.appendChild(btn);
                        wrapper.appendChild(del);
                        li.appendChild(wrapper);
                        // Insert before the add dropdown container if present, else append
                        const addContainer = document.getElementById('addServiceDropdownContainer');
                        if (addContainer) tabsUl.insertBefore(li, addContainer); else tabsUl.appendChild(li);

                        // Create tab pane
                        const pane = document.createElement('div');
                        pane.className = 'tab-pane fade show active';
                        pane.id = `service-${nextIndex}`;
                        pane.setAttribute('role', 'tabpanel');
                        pane.setAttribute('aria-labelledby', `tab-${nextIndex}`);
                        pane.innerHTML = `
                            <div class="alert alert-info view-mode">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Service: ${serviceName}</strong><br>
                                Click "Edit Services" button below to add or remove services.
                            </div>
                            <div class="alert alert-info edit-mode" style="display: none;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Service: ${serviceName}</strong><br>
                                You can add or remove services using the buttons above.
                            </div>`;
                        tabsContent.appendChild(pane);

                        // Update count text
                        const countEl = document.querySelector('.service-count');
                        if (countEl) {
                            const match = countEl.textContent.match(/(\d+)/);
                            const n = match ? parseInt(match[1]) + 1 : existingLis.length + 1;
                            countEl.textContent = `TL Version's Number of Services: ${n}`;
                        }

                        // Ensure delete buttons are enabled when more than 1 service
                        const removeButtons = document.querySelectorAll('.btn-remove-service');
                        if (removeButtons.length > 1) {
                            removeButtons.forEach(b => b.disabled = false);
                        }
                    }

                    if (Toast) { Toast.fire({ icon: 'success', title: data.message }); }
                } catch (e) {
                    console.error('DOM update failed after addService:', e);
                    // fallback to reload
                    window.location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (Toast) { Toast.fire({ icon: 'error', title: 'Failed to add service. Please try again.' }); }
        })
        .finally(() => {
            isAdding = false;
            if (addServiceDropdown) addServiceDropdown.disabled = false;
        });
    }

    function removeService(serviceId, serviceName) {
        console.log('removeService called with:', { serviceId, serviceName });
        
        // Show loading
        Swal.fire({
            title: 'Removing Service',
            text: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const url = `/tariff-lists/${tariffListId}/remove-service`;
        console.log('Fetching URL:', url);

        // Prevent duplicate removal of the same service
        if (removingSet.has(serviceId)) return;
        removingSet.add(serviceId);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                service_id: serviceId
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.message) {
                try {
                    // Remove the tab and pane
                    const tabsUl = document.getElementById('serviceTabs');
                    const tabsContent = document.getElementById('serviceTabsContent');
                    const li = tabsUl.querySelector(`li.nav-item[role="presentation"][data-service-id="${serviceId}"]`);
                    if (li) {
                        // Identify pane by button target
                        const btn = li.querySelector('button.nav-link');
                        const target = btn ? btn.getAttribute('data-bs-target') : null;
                        li.remove();
                        if (target) {
                            const pane = document.querySelector(target);
                            if (pane) pane.remove();
                        }
                    }

                    // Activate first remaining tab
                    const firstBtn = tabsUl.querySelector('button.nav-link');
                    if (firstBtn) {
                        const target = firstBtn.getAttribute('data-bs-target');
                        firstBtn.classList.add('active');
                        const pane = target ? document.querySelector(target) : null;
                        if (pane) pane.classList.add('show', 'active');
                    }

                    // Re-add option to dropdown
                    if (addServiceDropdown) {
                        const existsOpt = addServiceDropdown.querySelector(`option[value="${serviceId}"]`);
                        if (!existsOpt) {
                            const opt = document.createElement('option');
                            opt.value = serviceId;
                            opt.textContent = serviceName;
                            addServiceDropdown.appendChild(opt);
                        }
                        // Ensure container visible since we now have at least one option
                        const addServiceContainer = document.getElementById('addServiceDropdownContainer');
                        if (addServiceContainer) addServiceContainer.style.display = 'block';
                    }

                    // Update count text
                    const countEl = document.querySelector('.service-count');
                    if (countEl) {
                        const match = countEl.textContent.match(/(\d+)/);
                        const n = match ? Math.max(0, parseInt(match[1]) - 1) : 0;
                        countEl.textContent = `TL Version's Number of Services: ${n}`;
                    }

                    // If only one service remains, disable its remove button
                    const removeButtons = document.querySelectorAll('.btn-remove-service');
                    if (removeButtons.length <= 1) {
                        removeButtons.forEach(b => b.disabled = true);
                    }

                    if (Toast) { Toast.fire({ icon: 'success', title: data.message }); }
                } catch (e) {
                    console.error('DOM update failed after removeService:', e);
                    // fallback to reload
                    window.location.reload();
                }
            } else {
                throw new Error(data.error || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (Toast) { Toast.fire({ icon: 'error', title: error.message || 'Failed to remove service. Please try again.' }); }
        })
        .finally(() => {
            removingSet.delete(serviceId);
        });
    }
});
