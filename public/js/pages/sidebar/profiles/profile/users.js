document.addEventListener('DOMContentLoaded', function () {
    const removeProfilePictureBtn = document.querySelector('#removeProfilePictureBtn');
    const profilePicUpload = document.getElementById('profile_picture_upload');
    const profilePicWrapper = document.querySelector('.profile-pic-wrapper');
    const suffixDropdownBtn = document.getElementById('suffixDropdownBtn');
    const suffixInput = document.getElementById('suffixInput');
    const suffixDropdownItems = document.querySelectorAll('#suffixDropdownBtn + .dropdown-menu .dropdown-item');
    const firstNameInput = document.querySelector('input[name="first_name"]');
    const middleNameInput = document.querySelector('input[name="middle_name"]');
    const lastNameInput = document.querySelector('input[name="last_name"]');
    const deactivateUserBtn = document.getElementById('deactivateUser');
    
    if (deactivateUserBtn) {
        deactivateUserBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            const status = this.getAttribute('data-status');
            if (confirm(`Are you sure you want to ${status} this user?`)) {
                const memberId = this.getAttribute('data-member-id');
                const response = await fetch(`/profiles/users/${memberId}/deactivate`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while trying to deactivate the user.');
                });
                if (response.ok) {
                    window.location.reload();
                }
                else {
                    alert('Failed to deactivate user.');
                }
            }
        });
    }

    if (removeProfilePictureBtn) {
        removeProfilePictureBtn.addEventListener('click', function () {
            const flagInput = document.getElementById('remove_profile_picture_flag');
            flagInput.value = '1';
            alert('Profile picture will be removed upon saving changes.');
            const profilePicElem = document.querySelector('.profile-pic');
            const avatarPlaceholderElem = document.querySelector('.avatar-placeholder');

            if (profilePicElem) {
                profilePicElem.style.display = 'none';
            }

            if (!avatarPlaceholderElem) {
                const newPlaceholder = document.createElement('div');
                newPlaceholder.classList.add('avatar-placeholder');
                newPlaceholder.textContent = '';
                profilePicWrapper.prepend(newPlaceholder);
            } else {
                avatarPlaceholderElem.style.display = 'flex';
            }
        });
    }

    if (profilePicWrapper) {
        profilePicWrapper.addEventListener('click', function () {
            profilePicUpload.click();
        });
    }

    if (profilePicUpload) {
        profilePicUpload.addEventListener('change', function (event) {
            const [file] = event.target.files;
            if (file) {
                const existingPic = document.querySelector('.profile-pic');
                const existingPlaceholder = document.querySelector('.avatar-placeholder');

                if (existingPic) {
                    existingPic.src = URL.createObjectURL(file);
                    existingPic.style.display = 'block';
                    if (existingPlaceholder) existingPlaceholder.style.display = 'none';
                } else if (existingPlaceholder) {
                    const newImg = document.createElement('img');
                    newImg.classList.add('profile-pic');
                    newImg.src = URL.createObjectURL(file);
                    existingPlaceholder.parentNode.insertBefore(newImg, existingPlaceholder);
                    existingPlaceholder.style.display = 'none';
                }

                const flagInput = document.getElementById('remove_profile_picture_flag');
                flagInput.value = '0';
            }
        });
    }

    if (suffixDropdownBtn && suffixInput && suffixDropdownItems.length > 0) {
        suffixDropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const selectedValue = this.getAttribute('data-value');
                const selectedText = this.textContent.trim();
                suffixInput.value = selectedValue;
                suffixDropdownBtn.textContent = selectedText;
                suffixDropdownItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        suffixDropdownBtn.addEventListener('show.bs.dropdown', () => {
            suffixDropdownBtn.classList.add('rotated');
        });

        suffixDropdownBtn.addEventListener('hide.bs.dropdown', () => {
            suffixDropdownBtn.classList.remove('rotated');
        });

        const initialSuffixValue = suffixInput.value;
        let found = false;

        if (initialSuffixValue) {
            suffixDropdownItems.forEach(item => {
                if (item.getAttribute('data-value') === initialSuffixValue) {
                    suffixDropdownBtn.textContent = item.textContent.trim();
                    item.classList.add('active');
                    found = true;
                } else {
                    item.classList.remove('active');
                }
            });
        }

        if (!found) {
            suffixDropdownBtn.textContent = '';
            suffixDropdownItems[0].classList.add('active');
        }
    }

    const validateNameInput = inputElement => {
        inputElement.addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });
    };

    if (firstNameInput) validateNameInput(firstNameInput);
    if (middleNameInput) validateNameInput(middleNameInput);
    if (lastNameInput) validateNameInput(lastNameInput);
});
