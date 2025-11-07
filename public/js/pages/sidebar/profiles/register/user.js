document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(buttonId, inputId) {
        const dropdownButton = document.getElementById(buttonId);
        const hiddenInput = document.getElementById(inputId);
        if (!dropdownButton || !hiddenInput) return;
        let dropdownMenu = dropdownButton.nextElementSibling;

        if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) {
            dropdownMenu = document.querySelector(`#${buttonId} + .dropdown-menu`);
        }

        if (!dropdownMenu) return;
        const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');

        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const val = this.getAttribute('data-value') || '';
                hiddenInput.value = val;
                dropdownButton.textContent = this.textContent.trim();
                dropdownItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                if (inputId === 'roleInput') {
                    updateRoleInfo(val);
                }
            });
        });

        dropdownButton.addEventListener('show.bs.dropdown', () => {
            dropdownButton.classList.add('rotated');
        });

        dropdownButton.addEventListener('hide.bs.dropdown', () => {
            dropdownButton.classList.remove('rotated');
        });

        const initialVal = hiddenInput.value;
        let found = false;

        if (initialVal) {
            dropdownItems.forEach(item => {
                if (item.getAttribute('data-value') === initialVal) {
                    dropdownButton.textContent = item.textContent.trim();
                    item.classList.add('active');
                    found = true;
                } else {
                    item.classList.remove('active');
                }
            });
        }
        if (!found) {
            const serverActive = dropdownMenu.querySelector('.dropdown-item.active');

            if (serverActive) {
                dropdownButton.textContent = serverActive.textContent.trim();
            } else {
                const first = dropdownMenu.querySelector('.dropdown-item');
                if (first) dropdownButton.textContent = first.textContent.trim();
            }
        }
    }

    setupDropdown('roleDropdownBtn', 'roleInput');
    setupDropdown('suffixDropdownBtn', 'suffixInput');

    const fileInput = document.getElementById('profilePictureActualInput');
    const imageUploadWrap = document.getElementById('imageUploadWrap');
    const wrapPreviewImage = document.getElementById('wrapPreviewImage');
    const wrapPlaceholder = document.getElementById('wrapPlaceholder');
    const wrapRemoveBtn = document.getElementById('wrapRemoveBtn');
    const fileNameDisplay = document.querySelector('.file-name-display');
    const roleAvatarImg = document.getElementById('roleAvatarImg');
    const roleAvatarPlaceholder = document.getElementById('roleAvatarPlaceholder');
    const roleAvatarRemoveBtn = document.getElementById('roleAvatarRemoveBtn');

    function readURL(input) {
        if (!input) return;

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                if (wrapPreviewImage) {
                    wrapPreviewImage.src = e.target.result;
                    wrapPreviewImage.classList.remove('d-none');
                }

                if (wrapPlaceholder) wrapPlaceholder.classList.add('d-none');
                if (wrapRemoveBtn) wrapRemoveBtn.style.display = 'flex';
                if (fileNameDisplay) fileNameDisplay.textContent = input.files[0].name;

                if (roleAvatarImg) {
                    roleAvatarImg.src = e.target.result;
                    roleAvatarImg.classList.remove('d-none');
                }

                if (roleAvatarPlaceholder) roleAvatarPlaceholder.classList.add('d-none');
                if (roleAvatarRemoveBtn) roleAvatarRemoveBtn.style.display = 'flex';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            removeUpload();
        }
    }

    function removeUpload() {
        if (fileInput) fileInput.value = '';

        if (wrapPreviewImage) {
            wrapPreviewImage.src = '#';
            wrapPreviewImage.classList.add('d-none');
        }

        if (wrapRemoveBtn) wrapRemoveBtn.style.display = 'none';
        if (fileNameDisplay) fileNameDisplay.textContent = '';
        if (wrapPlaceholder) wrapPlaceholder.classList.remove('d-none');

        if (roleAvatarImg) {
            roleAvatarImg.src = '#';
            roleAvatarImg.classList.add('d-none');
        }

        if (roleAvatarPlaceholder) roleAvatarPlaceholder.classList.remove('d-none');
        if (roleAvatarRemoveBtn) roleAvatarRemoveBtn.style.display = 'none';
    }

    if (imageUploadWrap) {
        imageUploadWrap.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('image-dropping');
        });

        imageUploadWrap.addEventListener('dragleave', function (e) {
            e.preventDefault();
            this.classList.remove('image-dropping');
        });

        imageUploadWrap.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('image-dropping');

            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length && fileInput) {
                fileInput.files = e.dataTransfer.files;
                readURL(fileInput);
            }
        });

        imageUploadWrap.addEventListener('click', function (e) {
            const target = e.target;

            if (target === wrapRemoveBtn || (wrapRemoveBtn && wrapRemoveBtn.contains(target)) || target === roleAvatarRemoveBtn || (roleAvatarRemoveBtn && roleAvatarRemoveBtn.contains(target))) {
                removeUpload();
                return;
            }

            if (fileInput) fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () { readURL(this); });
    }

    document.addEventListener('paste', function (e) {
        try {
            if (e && e.clipboardData && e.clipboardData.files && e.clipboardData.files.length && fileInput) {
                fileInput.files = e.clipboardData.files;
                readURL(fileInput);
            }
        } catch (err) { }
    });

    if (wrapRemoveBtn) {
        wrapRemoveBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            removeUpload();
        });
    }

    if (roleAvatarRemoveBtn) {
        roleAvatarRemoveBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            removeUpload();
        });
    }

    window.removeUpload = removeUpload;

    let rolesMap = {};
    const livewireContainer = document.querySelector('[wire\\:id]') || null;

    if (livewireContainer) {
        const lwRoles = livewireContainer.getAttribute('data-roles-json');
        if (lwRoles) {
            try {
                rolesMap = JSON.parse(lwRoles);
            } catch (e) {
                rolesMap = {};
            }
        }
    }

    function escapeHtml(unsafe) {
        return String(unsafe)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    const roleAllowedActionsList = document.getElementById('roleAllowedActionsList');
    const roleAccessScopeList = document.getElementById('roleAccessScopeList');
    const roleInfoName = document.getElementById('roleInfoName');

    function normalizeAndSplitByPeriod(text) {
        if (!text && text !== 0) return [];
        const str = String(text).trim();
        if (!str) return [];
        return str.split('.').map(s => s.trim()).filter(s => s.length > 0).map(s => s.endsWith('.') ? s : s + '.');
    }

    function generateListHtml(items) {
        if (!items || !items.length) return '<span class="text-muted">N/A</span>';
        return '<ul style="list-style: disc;">' + items.map(item => `<li style="margin-left: 14px;">${escapeHtml(item)}</li>`).join('') + '</ul>';
    }

    function updateRoleInfo(roleId) {
        const id = roleId || (document.getElementById('roleInput') ? document.getElementById('roleInput').value : '');
        const role = rolesMap[id] || null;

        if (role) {
            roleInfoName.textContent = role.role || 'Unnamed Role';
            const allowedActionsItems = normalizeAndSplitByPeriod(role.allowed_actions);
            const accessScopeItems = normalizeAndSplitByPeriod(role.access_scope);
            const allowedActionsHtml = generateListHtml(allowedActionsItems);
            const accessScopeHtml = generateListHtml(accessScopeItems);
            roleAllowedActionsList.innerHTML = allowedActionsHtml;
            roleAccessScopeList.innerHTML = accessScopeHtml;
        } else {
            roleInfoName.textContent = 'Select a role to view details.';
            roleAllowedActionsList.innerHTML = '<span class="text-muted">N/A</span>';
            roleAccessScopeList.innerHTML = '<span class="text-muted">N/A</span>';
        }
    }

    updateRoleInfo(document.getElementById('roleInput') ? document.getElementById('roleInput').value : '');
    const preloadedFileUploadImage = document.querySelector('.file-upload-image');

    if (preloadedFileUploadImage && preloadedFileUploadImage.src && preloadedFileUploadImage.src !== '#' && !preloadedFileUploadImage.src.includes('data:')) {
        if (wrapPreviewImage) {
            wrapPreviewImage.src = preloadedFileUploadImage.src;
            wrapPreviewImage.classList.remove('d-none');
        }

        if (wrapPlaceholder) wrapPlaceholder.classList.add('d-none');
        if (wrapRemoveBtn) wrapRemoveBtn.style.display = 'flex';

        if (roleAvatarImg) {
            roleAvatarImg.src = preloadedFileUploadImage.src;
            roleAvatarImg.classList.remove('d-none');
        }

        if (roleAvatarPlaceholder) roleAvatarPlaceholder.classList.add('d-none');
    }

    document.addEventListener('livewire:load', function () {
        const lwRoot = document.querySelector('[wire\\:id]');

        if (lwRoot) {
            const json = lwRoot.getAttribute('data-roles-json');

            if (json) {
                try {
                    rolesMap = JSON.parse(json);
                    updateRoleInfo(document.getElementById('roleInput') ? document.getElementById('roleInput').value : '');
                } catch (e) { }
            }
        }
    });

    // Real-time username validation
    const usernameInput = document.getElementById('form-input-username');
    let usernameTimeout = null;
    let lastCheckedUsername = '';

    if (usernameInput) {
        // Find or create feedback div
        let feedbackDiv = usernameInput.parentNode.querySelector('.username-feedback');
        if (!feedbackDiv) {
            feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'username-feedback';
            feedbackDiv.style.fontSize = '0.875em';
            feedbackDiv.style.marginTop = '0.25rem';
            usernameInput.parentNode.appendChild(feedbackDiv);
        }

        usernameInput.addEventListener('input', function() {
            const username = this.value.trim();
            
            // Clear previous timeout
            if (usernameTimeout) {
                clearTimeout(usernameTimeout);
            }

            // Reset validation state
            usernameInput.classList.remove('is-invalid', 'is-valid');
            feedbackDiv.textContent = '';
            feedbackDiv.className = 'username-feedback';

            // Don't validate if empty or same as last checked
            if (!username || username === lastCheckedUsername) {
                return;
            }

            // Show checking message
            feedbackDiv.textContent = 'Checking username...';
            feedbackDiv.className = 'username-feedback text-muted';

            // Debounce: wait 500ms after user stops typing
            usernameTimeout = setTimeout(function() {
                lastCheckedUsername = username;

                // Make AJAX request
                fetch('/api/validate-username', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ username: username })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        usernameInput.classList.remove('is-invalid');
                        usernameInput.classList.add('is-valid');
                        feedbackDiv.textContent = data.message;
                        feedbackDiv.className = 'username-feedback text-success';
                    } else {
                        usernameInput.classList.remove('is-valid');
                        usernameInput.classList.add('is-invalid');
                        feedbackDiv.textContent = data.message;
                        feedbackDiv.className = 'username-feedback text-danger';
                    }
                })
                .catch(error => {
                    console.error('Username validation error:', error);
                    feedbackDiv.textContent = '';
                    feedbackDiv.className = 'username-feedback';
                    usernameInput.classList.remove('is-valid', 'is-invalid');
                });
            }, 500);
        });

        // Clear validation when field is focused
        usernameInput.addEventListener('focus', function() {
            lastCheckedUsername = '';
        });
    }
});
