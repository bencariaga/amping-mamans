document.addEventListener("DOMContentLoaded", () => {
    function setupDropdownArrowRotation(buttonId) {
        const btn = document.getElementById(buttonId);

        if (btn) {
            btn.addEventListener("show.bs.dropdown", () => {
                btn.classList.add("rotated");
            });

            btn.addEventListener("hide.bs.dropdown", () => {
                btn.classList.remove("rotated");
            });
        }
    }

    [
        "applicantSuffixDropdownBtn",
        "applicantSexDropdownBtn",
        "applicantBarangayDropdownBtn",
        "applicantCivilStatusDropdownBtn",
        "applicantOccupationDropdownBtn",
        "applicantPhicAffiliationDropdownBtn",
        "applicantPhicCategoryDropdownBtn",
        "applicantJobStatusDropdownBtn",
        "applicantHouseStatusDropdownBtn",
        "applicantLotStatusDropdownBtn",
    ].forEach(setupDropdownArrowRotation);

    function setupDynamicDropdownsRotation(prefix) {
        document.querySelectorAll(`[id^="${prefix}"]`).forEach((btn) => {
            btn.addEventListener("show.bs.dropdown", () => {
                btn.classList.add("rotated");
            });

            btn.addEventListener("hide.bs.dropdown", () => {
                btn.classList.remove("rotated");
            });
        });
    }

    [
        "patientSuffixDropdownBtn-",
        "patientSexDropdownBtn-",
        "patientCategoryDropdownBtn-",
    ].forEach(setupDynamicDropdownsRotation);

    function setupDropdownWithHidden(dropdownBtnId, hiddenInputId) {
        const btn = document.getElementById(dropdownBtnId);
        const hidden = document.getElementById(hiddenInputId);

        if (btn && hidden) {
            const menu = btn.nextElementSibling;
            if (menu) {
                menu.querySelectorAll(".dropdown-item").forEach((item) => {
                    item.addEventListener("click", (e) => {
                        e.preventDefault();
                        const value = item.getAttribute("data-value");
                        const text = item.textContent.trim();
                        btn.textContent = text;
                        hidden.value = value;

                        menu.querySelectorAll(".dropdown-item").forEach((i) =>
                            i.classList.remove("active")
                        );
                        item.classList.add("active");
                    });
                });
            }
        }
    }

    setupDropdownWithHidden("applicantSuffixDropdownBtn", "suffixHidden");
    setupDropdownWithHidden("applicantSexDropdownBtn", "sexHidden");
    setupDropdownWithHidden(
        "applicantCivilStatusDropdownBtn",
        "civilStatusHidden"
    );
    setupDropdownWithHidden("applicantJobStatusDropdownBtn", "jobStatusHidden");
    setupDropdownWithHidden("applicantBarangayDropdownBtn", "barangayHidden");
    setupDropdownWithHidden(
        "applicantHouseStatusDropdownBtn",
        "houseOccupStatusHidden"
    );
    setupDropdownWithHidden(
        "applicantLotStatusDropdownBtn",
        "lotOccupStatusHidden"
    );

    const occupationBtn = document.getElementById(
        "applicantOccupationDropdownBtn"
    );
    const occupationHidden = document.getElementById("occupationIdHidden");
    const customOccupationInput = document.getElementById(
        "applicantCustomOccupationInput"
    );

    if (occupationBtn && occupationHidden && customOccupationInput) {
        const occupationMenu = occupationBtn.nextElementSibling;

        if (occupationMenu) {
            occupationMenu
                .querySelectorAll(".dropdown-item")
                .forEach((item) => {
                    item.addEventListener("click", (e) => {
                        e.preventDefault();
                        const value = item.getAttribute("data-value");
                        const text = item.textContent.trim();
                        occupationBtn.textContent = text;
                        occupationHidden.value = value;

                        occupationMenu
                            .querySelectorAll(".dropdown-item")
                            .forEach((i) => i.classList.remove("active"));
                        item.classList.add("active");

                        if (value === "") {
                            customOccupationInput.disabled = false;
                            customOccupationInput.placeholder =
                                "If none in existing occupations.";

                            if (text === "— Select —") {
                                customOccupationInput.disabled = true;
                                customOccupationInput.value = "";
                                customOccupationInput.placeholder =
                                    'Select "Other" in Occupation.';
                            }
                        } else {
                            customOccupationInput.disabled = true;
                            customOccupationInput.value = "";
                            customOccupationInput.placeholder =
                                'Select "Other" in Occupation.';
                        }
                    });
                });
        }

        if (occupationHidden.value === "") {
            customOccupationInput.placeholder = 'Select "Other" in Occupation.';
        } else {
            customOccupationInput.placeholder = 'Select "Other" in Occupation.';
        }
    }

    const phicAffiliationBtn = document.getElementById(
        "applicantPhicAffiliationDropdownBtn"
    );
    const phicAffiliationHidden = document.getElementById(
        "phicAffiliationHidden"
    );
    const phicCategoryBtn = document.getElementById(
        "applicantPhicCategoryDropdownBtn"
    );
    const phicCategoryHidden = document.getElementById("phicCategoryHidden");

    if (
        phicAffiliationBtn &&
        phicAffiliationHidden &&
        phicCategoryBtn &&
        phicCategoryHidden
    ) {
        const phicAffiliationMenu = phicAffiliationBtn.nextElementSibling;
        if (phicAffiliationMenu) {
            phicAffiliationMenu
                .querySelectorAll(".dropdown-item")
                .forEach((item) => {
                    item.addEventListener("click", (e) => {
                        e.preventDefault();
                        const value = item.getAttribute("data-value");
                        const text = item.textContent.trim();
                        phicAffiliationBtn.textContent = text;
                        phicAffiliationHidden.value = value;

                        phicAffiliationMenu
                            .querySelectorAll(".dropdown-item")
                            .forEach((i) => i.classList.remove("active"));
                        item.classList.add("active");

                        if (value === "Affiliated") {
                            phicCategoryBtn.disabled = false;
                        } else {
                            phicCategoryBtn.disabled = true;
                            phicCategoryBtn.textContent = "— Select —";
                            phicCategoryHidden.value = "";
                        }
                    });
                });
        }

        const phicCategoryMenu = phicCategoryBtn.nextElementSibling;
        if (phicCategoryMenu) {
            phicCategoryMenu
                .querySelectorAll(".dropdown-item")
                .forEach((item) => {
                    item.addEventListener("click", (e) => {
                        e.preventDefault();
                        const value = item.getAttribute("data-value");
                        const text = item.textContent.trim();
                        phicCategoryBtn.textContent = text;
                        phicCategoryHidden.value = value;

                        phicCategoryMenu
                            .querySelectorAll(".dropdown-item")
                            .forEach((i) => i.classList.remove("active"));
                        item.classList.add("active");
                    });
                });
        }

        if (phicAffiliationHidden.value === "Affiliated") {
            phicCategoryBtn.disabled = false;
        } else {
            phicCategoryBtn.disabled = true;
        }
    }

    const monthlyIncomeDisplayInput = document.getElementById(
        "monthlyIncomeDisplayInput"
    );
    const monthlyIncomeHiddenInput = document.getElementById(
        "monthlyIncomeHiddenInput"
    );

    function setHiddenValueAndNotify(value) {
        monthlyIncomeHiddenInput.value = value;
        const ev = new Event("input", { bubbles: true });
        monthlyIncomeHiddenInput.dispatchEvent(ev);
    }

    if (monthlyIncomeDisplayInput && monthlyIncomeHiddenInput) {
        monthlyIncomeDisplayInput.addEventListener("input", (event) => {
            let value = event.target.value.replace(/[^0-9]/g, "");
            setHiddenValueAndNotify(value);
            event.target.value =
                value === "" ? "" : Number(value).toLocaleString();
        });

        monthlyIncomeDisplayInput.addEventListener("paste", (event) => {
            event.preventDefault();
            const paste = event.clipboardData.getData("text");
            const cleanPaste = paste.replace(/[^0-9]/g, "");
            setHiddenValueAndNotify(cleanPaste);
            monthlyIncomeDisplayInput.value =
                cleanPaste === "" ? "" : Number(cleanPaste).toLocaleString();
        });

        if (monthlyIncomeHiddenInput.value !== "") {
            const val = monthlyIncomeHiddenInput.value
                .toString()
                .replace(/[^0-9]/g, "");
            monthlyIncomeDisplayInput.value =
                val === "" ? "" : Number(val).toLocaleString();
        }
    }

    const phoneInput = document.getElementById("applicantPhoneNumberInput");

    function formatPhoneForDisplay(raw) {
        if (!raw) return "";
        let s = raw.toString().trim();
        s = s.replace(/[^\d+]/g, "");

        if (s.startsWith("+")) {
            s = s.slice(1);
        }

        s = s.replace(/[^\d]/g, "");

        if (s.startsWith("63")) {
            s = "0" + s.slice(2);
        } else if (s.startsWith("9")) {
            s = "0" + s;
        } else if (!s.startsWith("0")) {
            s = "0" + s;
        }

        const parts = [];

        if (s.length <= 4) {
            return s;
        }

        parts.push(s.slice(0, 4));

        if (s.length > 4) {
            parts.push(s.slice(4, Math.min(7, s.length)));
        }

        if (s.length > 7) {
            parts.push(s.slice(7, Math.min(11, s.length)));
        }

        return parts.join("-");
    }

    function dispatchInputEvent(el) {
        const ev = new Event("input", { bubbles: true });
        el.dispatchEvent(ev);
    }

    if (phoneInput) {
        phoneInput.setAttribute("inputmode", "tel");
        phoneInput.setAttribute("maxlength", "15");

        phoneInput.addEventListener("input", (e) => {
            const raw = e.target.value;
            const formatted = formatPhoneForDisplay(raw);
            e.target.value = formatted;

            if (document.activeElement === e.target) {
                try {
                    e.target.selectionStart = e.target.selectionEnd =
                        formatted.length;
                } catch (err) {}
            }
        });

        phoneInput.addEventListener("paste", (e) => {
            e.preventDefault();
            const paste = e.clipboardData.getData("text");
            const formatted = formatPhoneForDisplay(paste);
            phoneInput.value = formatted;
            dispatchInputEvent(phoneInput);
        });
    }

    const applicantBirthdateInput = document.getElementById(
        "applicantBirthdateInput"
    );
    const applicantAgeInput = document.getElementById("applicantAgeInput");
    const applicantAgeHidden = document.getElementById("applicantAgeHidden");

    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();

        if (
            monthDiff < 0 ||
            (monthDiff === 0 && today.getDate() < birth.getDate())
        ) {
            age--;
        }

        return age;
    }

    function updateApplicantAge() {
        if (applicantBirthdateInput.value) {
            const age = calculateAge(applicantBirthdateInput.value);
            applicantAgeInput.value = age;
            applicantAgeHidden.value = age;

            const event = new Event("input", { bubbles: true });
            applicantAgeHidden.dispatchEvent(event);

            if (checkbox.checked) {
                copyApplicantToPatient1();
            }
        } else {
            applicantAgeInput.value = "";
            applicantAgeHidden.value = "";
        }
    }

    const checkbox = document.getElementById("checkbox");
    const patientNumberInput = document.getElementById("patientNumberInput");

    if (applicantBirthdateInput && applicantAgeInput && applicantAgeHidden) {
        applicantBirthdateInput.addEventListener("change", updateApplicantAge);
        applicantBirthdateInput.addEventListener("input", updateApplicantAge);
        
        // Also trigger copy to patient 1 if checkbox is checked
        applicantBirthdateInput.addEventListener("change", function() {
            if (checkbox && checkbox.checked) {
                copyApplicantToPatient1();
            }
        });

        if (applicantBirthdateInput.value) {
            updateApplicantAge();
        }
    }

    function copyApplicantToPatient1() {
        const applicantLastName = document.getElementById(
            "applicantLastNameInput"
        ).value;
        const applicantFirstName = document.getElementById(
            "applicantFirstNameInput"
        ).value;
        const applicantMiddleName = document.getElementById(
            "applicantMiddleNameInput"
        ).value;
        const applicantSuffixBtn = document.getElementById("applicantSuffixDropdownBtn");
        const applicantSuffixHidden = document.getElementById("suffixHidden");
        const applicantSexBtn = document.getElementById("applicantSexDropdownBtn");
        const applicantSexHidden = document.getElementById("sexHidden");
        const applicantBirthdate = document.getElementById("applicantBirthdateInput").value;
        const applicantAge = document.getElementById("applicantAgeInput").value;

        document.getElementById("patientLastNameInput-1").value =
            applicantLastName;
        document.getElementById("patientFirstNameInput-1").value =
            applicantFirstName;
        document.getElementById("patientMiddleNameInput-1").value =
            applicantMiddleName;
        document.getElementById("patientSuffixDropdownBtn-1").textContent =
            applicantSuffixBtn.textContent.trim();
        document.getElementById("patientSuffixHidden-1").value =
            applicantSuffixHidden.value;
        document.getElementById("patientSexDropdownBtn-1").textContent =
            applicantSexBtn.textContent.trim();
        document.getElementById("patientSexHidden-1").value =
            applicantSexHidden.value;
        
        // Copy birthdate and trigger age calculation
        const patientBirthdateInput = document.getElementById("patientBirthdateInput-1");
        const patientAgeDisplay = document.getElementById("patientAgeDisplay-1");
        const patientAgeHidden = document.getElementById("patientAgeInput-1");
        const patientCategoryBtn = document.getElementById("patientCategoryDropdownBtn-1");
        
        if (patientBirthdateInput) {
            patientBirthdateInput.value = applicantBirthdate;
            // Trigger change event to calculate age and category
            patientBirthdateInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        document
            .querySelectorAll(
                "#patientLastNameInput-1, #patientFirstNameInput-1, #patientMiddleNameInput-1"
            )
            .forEach((field) => {
                field.readOnly = true;
                field.style.backgroundColor = "#e9ecef";
                field.style.cursor = "not-allowed";
            });

        // Lock name fields, suffix, sex, and birthdate
        document
            .querySelectorAll(
                "#patientSuffixDropdownBtn-1, #patientSexDropdownBtn-1, #patientBirthdateInput-1"
            )
            .forEach((btn) => {
                btn.disabled = true;
                btn.style.pointerEvents = "none";
                btn.style.backgroundColor = "#e9ecef";
                btn.style.cursor = "not-allowed";
            });
        
        // Lock category only if age is 60 or above
        const age = applicantAge ? parseInt(applicantAge, 10) : 0;
        if (age >= 60 && patientCategoryBtn) {
            patientCategoryBtn.disabled = true;
            patientCategoryBtn.style.pointerEvents = "none";
            patientCategoryBtn.style.backgroundColor = "#e9ecef";
            patientCategoryBtn.style.cursor = "not-allowed";
        }
    }

    function clearPatient1() {
        document.getElementById("patientLastNameInput-1").value = "";
        document.getElementById("patientFirstNameInput-1").value = "";
        document.getElementById("patientMiddleNameInput-1").value = "";
        document.getElementById("patientSuffixDropdownBtn-1").textContent =
            "— Select —";
        document.getElementById("patientSuffixHidden-1").value = "";
        document.getElementById("patientSexDropdownBtn-1").textContent =
            "— Select —";
        document.getElementById("patientSexHidden-1").value = "";
        
        // Clear birthdate and age fields
        const patientBirthdateInput = document.getElementById("patientBirthdateInput-1");
        const patientAgeDisplay = document.getElementById("patientAgeDisplay-1");
        const patientAgeHidden = document.getElementById("patientAgeInput-1");
        const patientCategoryBtn = document.getElementById("patientCategoryDropdownBtn-1");
        const patientCategoryHidden = document.getElementById("patientCategoryHidden-1");
        
        if (patientBirthdateInput) patientBirthdateInput.value = "";
        if (patientAgeDisplay) patientAgeDisplay.value = "";
        if (patientAgeHidden) patientAgeHidden.value = "";
        if (patientCategoryBtn) patientCategoryBtn.textContent = "— Select —";
        if (patientCategoryHidden) patientCategoryHidden.value = "";

        document
            .querySelectorAll(
                "#patientLastNameInput-1, #patientFirstNameInput-1, #patientMiddleNameInput-1"
            )
            .forEach((field) => {
                field.readOnly = false;
                field.style.backgroundColor = "";
                field.style.cursor = "";
            });

        document
            .querySelectorAll(
                "#patientSuffixDropdownBtn-1, #patientSexDropdownBtn-1, #patientBirthdateInput-1, #patientCategoryDropdownBtn-1"
            )
            .forEach((btn) => {
                btn.disabled = false;
                btn.style.pointerEvents = "";
                btn.style.backgroundColor = "";
                btn.style.cursor = "";
            });
    }

    function handleCheckboxChange() {
        if (checkbox.checked) {
            copyApplicantToPatient1();
        } else {
            clearPatient1();
        }
    }

    function handleApplicantFieldChange() {
        if (checkbox.checked) {
            copyApplicantToPatient1();
        }
    }

    if (checkbox && patientNumberInput) {
        checkbox.addEventListener("change", handleCheckboxChange);

        document
            .getElementById("applicantLastNameInput")
            .addEventListener("input", handleApplicantFieldChange);
        document
            .getElementById("applicantFirstNameInput")
            .addEventListener("input", handleApplicantFieldChange);
        document
            .getElementById("applicantMiddleNameInput")
            .addEventListener("input", handleApplicantFieldChange);

        document
            .querySelectorAll("#applicantSuffixDropdownBtn + .dropdown-menu a")
            .forEach((item) => {
                item.addEventListener("click", handleApplicantFieldChange);
            });

        document
            .querySelectorAll("#applicantSexDropdownBtn + .dropdown-menu a")
            .forEach((item) => {
                item.addEventListener("click", handleApplicantFieldChange);
            });

        patientNumberInput.addEventListener("input", function () {
            let value = parseInt(this.value);
            if (value > 10) {
                this.value = 10;
            } else if (value < 1 || isNaN(value)) {
                this.value = 1;
            }
        });

        if (checkbox.checked) {
            copyApplicantToPatient1();
        }
    }

    document.querySelectorAll(".patient-age-input").forEach((input) => {
        input.addEventListener("input", function () {
            if (this.value > 200) {
                this.value = 200;
            }
        });
    });

    function setupPatientDropdowns() {
        document
            .querySelectorAll('[id^="patientSuffixDropdownBtn-"]')
            .forEach((btn) => {
                const index = btn.id.split("-")[1];
                const hiddenId = `patientSuffixHidden-${index}`;
                const hidden = document.getElementById(hiddenId);

                if (hidden) {
                    const menu = btn.nextElementSibling;
                    if (menu) {
                        menu.querySelectorAll(".dropdown-item").forEach(
                            (item) => {
                                item.addEventListener("click", (e) => {
                                    e.preventDefault();
                                    const value =
                                        item.getAttribute("data-value");
                                    const text = item.textContent.trim();
                                    btn.textContent = text;
                                    hidden.value = value;
                                    menu.querySelectorAll(
                                        ".dropdown-item"
                                    ).forEach((i) =>
                                        i.classList.remove("active")
                                    );
                                    item.classList.add("active");
                                });
                            }
                        );
                    }
                }
            });

        document
            .querySelectorAll('[id^="patientSexDropdownBtn-"]')
            .forEach((btn) => {
                const index = btn.id.split("-")[1];
                const hiddenId = `patientSexHidden-${index}`;
                const hidden = document.getElementById(hiddenId);

                if (hidden) {
                    const menu = btn.nextElementSibling;
                    if (menu) {
                        menu.querySelectorAll(".dropdown-item").forEach(
                            (item) => {
                                item.addEventListener("click", (e) => {
                                    e.preventDefault();
                                    const value =
                                        item.getAttribute("data-value");
                                    const text = item.textContent.trim();
                                    btn.textContent = text;
                                    hidden.value = value;
                                    menu.querySelectorAll(
                                        ".dropdown-item"
                                    ).forEach((i) =>
                                        i.classList.remove("active")
                                    );
                                    item.classList.add("active");
                                });
                            }
                        );
                    }
                }
            });

        document
            .querySelectorAll('[id^="patientCategoryDropdownBtn-"]')
            .forEach((btn) => {
                const index = btn.id.split("-")[1];
                const hiddenId = `patientCategoryHidden-${index}`;
                const hidden = document.getElementById(hiddenId);

                if (hidden) {
                    const menu = btn.nextElementSibling;
                    if (menu) {
                        menu.querySelectorAll(".dropdown-item").forEach(
                            (item) => {
                                item.addEventListener("click", (e) => {
                                    e.preventDefault();
                                    const value =
                                        item.getAttribute("data-value");
                                    const text = item.textContent.trim();
                                    btn.textContent = text;
                                    hidden.value = value;
                                    menu.querySelectorAll(
                                        ".dropdown-item"
                                    ).forEach((i) =>
                                        i.classList.remove("active")
                                    );
                                    item.classList.add("active");
                                });
                            }
                        );
                    }
                }
            });
    }

    setupPatientDropdowns();

    function updateRemoveButtonsState() {
        const container = document.getElementById("patientsContainer");
        const patientSections = container.querySelectorAll(".patient-section");
        const removeButtons = container.querySelectorAll("#removePatientBtn");

        removeButtons.forEach((btn) => {
            if (patientSections.length <= 1) {
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        });
    }

    function setupRemovePatientButtons() {
        const container = document.getElementById("patientsContainer");
        const removeButtons = container.querySelectorAll("#removePatientBtn");

        removeButtons.forEach((btn) => {
            btn.replaceWith(btn.cloneNode(true));
        });

        const newRemoveButtons = container.querySelectorAll("#removePatientBtn");
        newRemoveButtons.forEach((btn) => {
            btn.addEventListener("click", function () {
                const patientSection = this.closest(".patient-section");
                const patientIndex = parseInt(patientSection.getAttribute("data-patient-index"));
                const allSections = container.querySelectorAll(".patient-section");

                if (allSections.length <= 1) {
                    return;
                }

                const isRemovingPatient1 = patientIndex === 1;

                if (isRemovingPatient1 && checkbox && checkbox.checked) {
                    checkbox.checked = false;
                }

                patientSection.remove();

                const remainingSections = container.querySelectorAll(".patient-section");
                remainingSections.forEach((section, idx) => {
                    const newIndex = idx + 1;
                    section.setAttribute("data-patient-index", newIndex);
                    section.querySelector(".header-title").textContent = `NAME OF PATIENT ${newIndex}`;

                    section.querySelectorAll("input, button").forEach((el) => {
                        if (el.name) {
                            el.name = el.name.replace(/\[\d+\]/, `[${newIndex}]`);
                        }
                        if (el.id) {
                            el.id = el.id.replace(/-\d+$/, `-${newIndex}`);
                        }
                    });
                });

                if (patientNumberInput) {
                    patientNumberInput.value = remainingSections.length;
                }

                updateRemoveButtonsState();
                setupPatientDropdowns();

                if (checkbox && checkbox.checked && remainingSections.length >= 1) {
                    copyApplicantToPatient1();
                }
            });
        });
    }

    updateRemoveButtonsState();
    setupRemovePatientButtons();

    if (patientNumberInput) {
        patientNumberInput.addEventListener("change", function () {
            let newCount = parseInt(this.value) || 1;
            if (newCount < 1) newCount = 1;
            if (newCount > 10) newCount = 10;
            this.value = newCount;

            const container = document.getElementById("patientsContainer");
            const currentSections =
                container.querySelectorAll(".patient-section");
            const currentCount = currentSections.length;

            if (newCount > currentCount) {
                for (let i = currentCount + 1; i <= newCount; i++) {
                    const section = createPatientSection(i);
                    container.insertAdjacentHTML("beforeend", section);
                }
                setupPatientDropdowns();
                setupDynamicDropdownsRotation("patientSuffixDropdownBtn-");
                setupDynamicDropdownsRotation("patientSexDropdownBtn-");
                setupDynamicDropdownsRotation("patientCategoryDropdownBtn-");

                document
                    .querySelectorAll(".patient-age-input")
                    .forEach((input) => {
                        input.addEventListener("input", function () {
                            if (this.value > 200) {
                                this.value = 200;
                            }
                        });
                    });

                setupRemovePatientButtons();
                updateRemoveButtonsState();
            } else if (newCount < currentCount) {
                for (let i = currentCount; i > newCount; i--) {
                    const section = container.querySelector(
                        `[data-patient-index="${i}"]`
                    );
                    if (section) section.remove();
                }
                updateRemoveButtonsState();
            }

            if (checkbox && checkbox.checked) {
                copyApplicantToPatient1();
            }
        });
    }

    function createPatientSection(index) {
        return `
            <div class="profile-container patient-section" data-patient-index="${index}">
                <div class="col-md-12">
                    <div class="row gx-3 gy-3 mb-3">
                        <legend class="form-legend">
                            <i class="fas fa-hospital-user fa-fw"></i><span class="header-title">NAME OF PATIENT ${index}</span>
                        </legend>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                            <input type="text" name="patients[${index}][last_name]" value="" class="form-control" id="patientLastNameInput-${index}" placeholder="Example: Dela Cruz">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                            <input type="text" name="patients[${index}][first_name]" value="" class="form-control" id="patientFirstNameInput-${index}" placeholder="Example: Juan">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Middle Name / Initial</label>
                            <input type="text" name="patients[${index}][middle_name]" value="" class="form-control" id="patientMiddleNameInput-${index}" placeholder="Example: Pablo / P.">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Suffix</label>
                            <input type="hidden" name="patients[${index}][suffix]" id="patientSuffixHidden-${index}" value="">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSuffixDropdownBtn-${index}">— Select —</button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Jr.">Jr.</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Sr.">Sr.</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="II">II</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="III">III</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="IV">IV</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="V">V</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row gx-3 gy-3">
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Gender / Sex <span class="required-asterisk">*</span></label>
                            <input type="hidden" name="patients[${index}][sex]" id="patientSexHidden-${index}" value="">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSexDropdownBtn-${index}">— Select —</button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Male">Male</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Female">Female</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Age <span class="required-asterisk">*</span></label>
                            <input type="number" name="patients[${index}][age]" value="" class="form-control patient-age-input" id="patientAgeInput-${index}" placeholder="0" min="0" max="200">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Category</label>
                            <input type="hidden" name="patients[${index}][patient_category]" id="patientCategoryHidden-${index}" value="">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientCategoryDropdownBtn-${index}">— Select —</button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="PWD">PWD</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Senior">Senior</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group col-md-3 d-flex justify-content-end" id="removePatientBtnContainer">
                            <button type="button" class="btn btn-danger" id="removePatientBtn" disabled>REMOVE PATIENT</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // ================= Existing Applicant Search & Mode Toggle =================
    const searchInput = document.getElementById('existingApplicantSearch');
    const resultsBox = document.getElementById('existingApplicantResults');
    const selectedApplicantIdInput = document.getElementById('selectedApplicantId');
    const banner = document.getElementById('existingApplicantBanner');
    const bannerText = document.getElementById('existingApplicantText');
    const clearBtn = document.getElementById('clearExistingApplicant');
    const form = document.getElementById('profileSection');
    const initialAction = form ? form.getAttribute('action') : '';

    // Wrappers to disable when existing applicant selected
    const wrappersToDisable = [
        'applicantDetailsWrapper',
        'personalInfoWrapper',
        'workInfoWrapper',
        'addressWrapper',
    ].map(id => document.getElementById(id)).filter(Boolean);

    function setDisabledOnWrapper(wrapper, disabled) {
        if (!wrapper) return;
        wrapper.querySelectorAll('input, select, textarea, button').forEach(el => {
            // Keep meta hidden inputs unaffected (CSRF etc.)
            if (el.name === '_token') return;
            el.disabled = !!disabled;
        });
        wrapper.style.opacity = disabled ? 0.6 : 1;
    }

    function setInputValue(id, value) {
        const el = document.getElementById(id);
        if (el) {
            el.value = value ?? '';
            const ev = new Event('input', { bubbles: true });
            el.dispatchEvent(ev);
        }
    }

    function setDropdown(btnId, hiddenId, value, displayText) {
        const btn = document.getElementById(btnId);
        const hidden = document.getElementById(hiddenId);
        if (hidden) hidden.value = value ?? '';
        if (btn) btn.textContent = (displayText ?? value ?? '') || '— Select —';
    }

    function clearInput(id) {
        setInputValue(id, '');
    }

    function clearDropdown(btnId, hiddenId) {
        const btn = document.getElementById(btnId);
        const hidden = document.getElementById(hiddenId);
        if (hidden) hidden.value = '';
        if (btn) btn.textContent = '— Select —';
    }

    function clearApplicantDetails() {
        // Names
        clearInput('applicantLastNameInput');
        clearInput('applicantFirstNameInput');
        clearInput('applicantMiddleNameInput');
        clearDropdown('applicantSuffixDropdownBtn', 'suffixHidden');

        // Personal
        clearInput('applicantBirthdateInput');
        const ageInput = document.getElementById('applicantAgeInput');
        const ageHidden = document.getElementById('applicantAgeHidden');
        if (ageInput) ageInput.value = '';
        if (ageHidden) ageHidden.value = '';
        clearDropdown('applicantSexDropdownBtn', 'sexHidden');
        clearDropdown('applicantCivilStatusDropdownBtn', 'civilStatusHidden');

        // Contact
        clearInput('applicantPhoneNumberInput');

        // Work
        clearDropdown('applicantJobStatusDropdownBtn', 'jobStatusHidden');
        clearDropdown('applicantOccupationDropdownBtn', 'occupationIdHidden');
        const monthlyDisplay = document.getElementById('monthlyIncomeDisplayInput');
        const monthlyHidden = document.getElementById('monthlyIncomeHiddenInput');
        if (monthlyHidden) monthlyHidden.value = '';
        if (monthlyDisplay) monthlyDisplay.value = '';

        // Address
        clearInput('applicantHouseNumberInput');
        clearInput('applicantBlockNumberInput');
        clearInput('applicantPhaseInput');
        clearInput('applicantStreetInput');
        clearInput('applicantSitioInput');
        clearInput('applicantPurokInput');
        clearDropdown('applicantBarangayDropdownBtn', 'barangayHidden');

        // Housing
        clearDropdown('applicantHouseStatusDropdownBtn', 'houseOccupStatusHidden');
        clearDropdown('applicantLotStatusDropdownBtn', 'lotOccupStatusHidden');

        // PhilHealth
        clearDropdown('applicantPhicAffiliationDropdownBtn', 'phicAffiliationHidden');
        const phicCatBtn = document.getElementById('applicantPhicCategoryDropdownBtn');
        if (phicCatBtn) phicCatBtn.disabled = true;
        clearDropdown('applicantPhicCategoryDropdownBtn', 'phicCategoryHidden');
    }

    function formatPeso(num) {
        if (num === null || num === undefined || num === '') return '';
        const n = Number(num);
        if (Number.isNaN(n)) return '';
        return Number(n).toLocaleString();
    }

    async function loadApplicantDetails(applicantId) {
        try {
            const res = await fetch(`/api/applicants/${encodeURIComponent(applicantId)}`);
            if (!res.ok) return;
            const d = await res.json();

            // Names
            setInputValue('applicantLastNameInput', d.last_name);
            setInputValue('applicantFirstNameInput', d.first_name);
            setInputValue('applicantMiddleNameInput', d.middle_name);
            setDropdown('applicantSuffixDropdownBtn', 'suffixHidden', d.suffix, d.suffix);

            // Personal
            setInputValue('applicantBirthdateInput', d.birthdate || '');
            // Age display/hidden
            if (d.age !== undefined && d.age !== null) {
                const ageInput = document.getElementById('applicantAgeInput');
                const ageHidden = document.getElementById('applicantAgeHidden');
                const ageValue = parseInt(d.age, 10);
                if (ageInput) ageInput.value = ageValue;
                if (ageHidden) ageHidden.value = ageValue;
            }
            setDropdown('applicantSexDropdownBtn', 'sexHidden', d.sex, d.sex);
            setDropdown('applicantCivilStatusDropdownBtn', 'civilStatusHidden', d.civil_status, d.civil_status);

            // Contact
            if (typeof formatPhoneForDisplay === 'function') {
                setInputValue('applicantPhoneNumberInput', formatPhoneForDisplay(d.phone_number || ''));
            } else {
                setInputValue('applicantPhoneNumberInput', d.phone_number || '');
            }

            // Work
            setDropdown('applicantJobStatusDropdownBtn', 'jobStatusHidden', d.job_status, d.job_status);
            setDropdown('applicantOccupationDropdownBtn', 'occupationIdHidden', d.occupation_id, d.occupation_name || '— Select —');
            const monthlyDisplay = document.getElementById('monthlyIncomeDisplayInput');
            const monthlyHidden = document.getElementById('monthlyIncomeHiddenInput');
            if (monthlyHidden) monthlyHidden.value = d.monthly_income || '';
            if (monthlyDisplay) monthlyDisplay.value = formatPeso(d.monthly_income || '');

            // Address
            setInputValue('applicantHouseNumberInput', d.house_number);
            setInputValue('applicantBlockNumberInput', d.block_number);
            setInputValue('applicantPhaseInput', d.phase);
            setInputValue('applicantStreetInput', d.street);
            setInputValue('applicantSitioInput', d.sitio);
            setInputValue('applicantPurokInput', d.purok);
            setDropdown('applicantBarangayDropdownBtn', 'barangayHidden', d.barangay, d.barangay);

            // Housing
            setDropdown('applicantHouseStatusDropdownBtn', 'houseOccupStatusHidden', d.house_occup_status, d.house_occup_status);
            setDropdown('applicantLotStatusDropdownBtn', 'lotOccupStatusHidden', d.lot_occup_status, d.lot_occup_status);

            // PhilHealth
            setDropdown('applicantPhicAffiliationDropdownBtn', 'phicAffiliationHidden', d.phic_affiliation, d.phic_affiliation);
            const phicCatBtn = document.getElementById('applicantPhicCategoryDropdownBtn');
            if (d.phic_affiliation === 'Affiliated' && phicCatBtn) phicCatBtn.disabled = false;
            setDropdown('applicantPhicCategoryDropdownBtn', 'phicCategoryHidden', d.phic_category, d.phic_category);
        } catch (e) {
            console.error('Failed to load applicant details', e);
        }
    }

    async function loadApplicantPatients(applicantId) {
        try {
            const sec = document.getElementById('existingPatientsSection');
            const list = document.getElementById('existingPatientsList');
            if (!sec || !list) return;
            list.innerHTML = '';
            const res = await fetch(`/api/applicants/${encodeURIComponent(applicantId)}/patients`);
            if (!res.ok) { sec.style.display = 'none'; return; }
            const data = await res.json();
            const items = data.items || [];
            if (items.length === 0) {
                sec.style.display = 'none';
                return;
            }
            // Create table
            const table = document.createElement('table');
            table.className = 'table table-sm table-striped';
            table.style.fontSize = '0.9rem';
            
            // Table header
            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr style="background-color: #e3f2fd;">
                    <th>Patient</th>
                    <th>Affiliate Partner</th>
                    <th>Service</th>
                    <th>Billed</th>
                    <th>Assisted</th>
                    <th>Applied At</th>
                </tr>
            `;
            table.appendChild(thead);
            
            // Table body
            const tbody = document.createElement('tbody');
            items.forEach(app => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${app.patient_name}</strong></td>
                    <td>${app.affiliate_partner}</td>
                    <td>${app.service}</td>
                    <td>₱ ${parseFloat(app.billed_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</td>
                    <td>₱ ${parseFloat(app.assistance_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</td>
                    <td>${app.applied_at}</td>
                `;
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);
            
            list.innerHTML = '';
            list.appendChild(table);
            
            // Update count badge
            const countBadge = document.getElementById('existingPatientsCount');
            if (countBadge) {
                countBadge.textContent = items.length;
                countBadge.title = `${items.length} application${items.length !== 1 ? 's' : ''}`;
            }
            
            sec.style.display = 'block';
        } catch (e) {
            console.error('Failed to load applicant patients', e);
        }
    }

    async function enterExistingMode(item) {
        if (!form) return;
        selectedApplicantIdInput.value = item.applicant_id;
        // Change form to patients store endpoint
        form.setAttribute('action', `/profiles/applicants/${item.applicant_id}/patients`);
        // Show banner
        if (banner && bannerText) {
            banner.classList.remove('d-none');
            bannerText.textContent = `Existing applicant selected: ${item.full_name}`;
        }
        // Update button text to "Add Patient"
        const addBtn = document.getElementById('addApplicantBtn');
        if (addBtn) {
            const btnTextEl = addBtn.querySelector('.nav-text');
            if (btnTextEl) {
                btnTextEl.innerHTML = 'Add<br>Patient';
            }
        }
        // Load details FIRST before disabling fields
        await loadApplicantDetails(item.applicant_id);
        // Show existing patients of the applicant
        loadApplicantPatients(item.applicant_id);
        // NOW disable applicant detail wrappers after data is loaded
        wrappersToDisable.forEach(w => setDisabledOnWrapper(w, true));
        // Ensure patients section remains enabled
        const medicalWrapper = document.getElementById('medicalInfoWrapper');
        setDisabledOnWrapper(medicalWrapper, false);
        // Hide results box
        hideResults();
    }

    function exitExistingMode() {
        if (!form) return;
        selectedApplicantIdInput.value = '';
        form.setAttribute('action', initialAction);
        wrappersToDisable.forEach(w => setDisabledOnWrapper(w, false));
        if (banner) banner.classList.add('d-none');
        // Restore button text to "Add Applicant"
        const addBtn = document.getElementById('addApplicantBtn');
        if (addBtn) {
            const btnTextEl = addBtn.querySelector('.nav-text');
            if (btnTextEl) {
                btnTextEl.innerHTML = 'Add<br>Applicant';
            }
        }
        // Clear search box and results
        const searchInputEl = document.getElementById('existingApplicantSearch');
        if (searchInputEl) searchInputEl.value = '';
        hideResults();
        // Clear applicant form values so New mode starts clean
        clearApplicantDetails();
        // Hide existing patients section
        const sec = document.getElementById('existingPatientsSection');
        const list = document.getElementById('existingPatientsList');
        if (sec) sec.style.display = 'none';
        if (list) list.innerHTML = '';
    }

    function hideResults() {
        if (resultsBox) {
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
        }
    }

    function showResults(items) {
        if (!resultsBox) return;
        resultsBox.innerHTML = '';
        if (!items || items.length === 0) {
            // Show "No results found" message instead of hiding
            const noResults = document.createElement('div');
            noResults.className = 'list-group-item text-muted';
            noResults.textContent = 'No applicants found matching your search';
            resultsBox.appendChild(noResults);
            resultsBox.style.display = 'block';
            return;
        }
        items.forEach(item => {
            const a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action';
            const phone = item.phone_number ? ` · ${item.phone_number}` : '';
            const bdate = item.birthdate ? ` · ${item.birthdate}` : '';
            a.textContent = `${item.full_name}${bdate}${phone}`;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                enterExistingMode(item);
            });
            resultsBox.appendChild(a);
        });
        resultsBox.style.display = 'block';
    }

    function debounce(fn, delay) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(null, args), delay);
        };
    }

    async function performSearch(q) {
        if (!q || q.trim().length < 2) {
            console.log('[Search] Query too short:', q);
            hideResults();
            return;
        }
        try {
            console.log('[Search] Searching for:', q);
            const res = await fetch(`/api/applicants/search?name=${encodeURIComponent(q.trim())}`);
            console.log('[Search] Response status:', res.status);
            const data = await res.json();
            console.log('[Search] Results count:', data.items?.length || 0);
            console.log('[Search] Results:', data.items);
            showResults(data.items || []);
        } catch (e) {
            console.error('[Search] Failed:', e);
            hideResults();
        }
    }

    if (searchInput) {
        const onInput = debounce((e) => performSearch(e.target.value), 250);
        searchInput.addEventListener('input', onInput);
        searchInput.addEventListener('blur', () => {
            // Delay hiding to allow clicking on results
            setTimeout(hideResults, 300);
        });
        searchInput.addEventListener('focus', () => {
            // Re-search on focus if there's already text
            if (searchInput.value.trim().length >= 2) {
                performSearch(searchInput.value);
            }
        });
    }

    // Prevent results box from closing when clicking inside it
    if (resultsBox) {
        resultsBox.addEventListener('mousedown', (e) => {
            e.preventDefault(); // Prevent blur event on search input
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            exitExistingMode();
        });
    }

    // Radio buttons to toggle modes
    const modeNew = document.getElementById('applicantModeNew');
    const modeOld = document.getElementById('applicantModeOld');
    const existingSearchSection = document.getElementById('existingSearchSection');

    function applyModeVisibility() {
        if (!modeNew || !modeOld) return;
        console.log('[ApplicantMode] modeNew.checked=', modeNew.checked, 'modeOld.checked=', modeOld.checked);
        if (modeOld.checked) {
            if (existingSearchSection) existingSearchSection.style.display = 'block';
        } else {
            if (existingSearchSection) existingSearchSection.style.display = 'none';
        }
    }

    if (modeNew && modeOld) {
        modeNew.addEventListener('change', () => {
            console.log('[ApplicantMode] Switched to NEW');
            if (modeNew.checked) {
                exitExistingMode();
                applyModeVisibility();
            }
        });
        modeOld.addEventListener('change', () => {
            console.log('[ApplicantMode] Switched to OLD');
            if (modeOld.checked) {
                // Entering old mode does not auto-select any applicant; just show search.
                applyModeVisibility();
            }
        });
        // Fallback click handlers (some UIs only trigger click)
        modeNew.addEventListener('click', () => {
            if (modeNew.checked) {
                exitExistingMode();
                applyModeVisibility();
            }
        });
        modeOld.addEventListener('click', () => {
            if (modeOld.checked) {
                applyModeVisibility();
            }
        });
        // Initial state
        applyModeVisibility();
    }

    // Pre-submit guard: when Old mode is selected, require an existing applicant selection
    if (form && modeOld && selectedApplicantIdInput) {
        form.addEventListener('submit', (e) => {
            if (modeOld.checked) {
                const val = (selectedApplicantIdInput.value || '').trim();
                if (!val) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select an existing applicant',
                            text: 'Please search and select an applicant before submitting in Old mode.',
                            confirmButtonColor: '#0d6efd'
                        });
                    } else {
                        alert('Please select an existing applicant before submitting in Old mode.');
                    }
                }
            }
        });
    }

    // Patient birthdate to age calculation
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('patient-birthdate-input')) {
            const birthdateInput = e.target;
            const index = birthdateInput.id.split('-')[1]; // Extract index from ID
            const ageDisplay = document.getElementById(`patientAgeDisplay-${index}`);
            const ageHidden = document.getElementById(`patientAgeInput-${index}`);
            const categoryBtn = document.getElementById(`patientCategoryDropdownBtn-${index}`);
            const categoryHidden = document.getElementById(`patientCategoryHidden-${index}`);
            
            if (birthdateInput.value && ageDisplay && ageHidden) {
                const age = calculateAge(birthdateInput.value);
                ageDisplay.value = age;
                ageHidden.value = age;
                
                // Get the dropdown menu
                const categoryDropdown = categoryBtn?.nextElementSibling;
                const seniorOption = categoryDropdown?.querySelector('[data-value="Senior"]')?.parentElement;
                
                // Auto-select category based on age
                if (categoryBtn && categoryHidden) {
                    if (age >= 60) {
                        // Automatically set to Senior if 60 or above
                        categoryBtn.textContent = 'Senior';
                        categoryHidden.value = 'Senior';
                        // Show Senior option
                        if (seniorOption) seniorOption.style.display = '';
                    } else {
                        // Clear category selection for younger ages
                        categoryBtn.textContent = '— Select —';
                        categoryHidden.value = '';
                        // Hide Senior option for under 60
                        if (seniorOption) seniorOption.style.display = 'none';
                    }
                }
            } else if (ageDisplay && ageHidden) {
                ageDisplay.value = '';
                ageHidden.value = '';
                // Clear category if no birthdate
                if (categoryBtn && categoryHidden) {
                    categoryBtn.textContent = '— Select —';
                    categoryHidden.value = '';
                    // Show all options when no age
                    const categoryDropdown = categoryBtn.nextElementSibling;
                    const seniorOption = categoryDropdown?.querySelector('[data-value="Senior"]')?.parentElement;
                    if (seniorOption) seniorOption.style.display = '';
                }
            }
        }
    });

    // Initialize age for existing patient birthdate values
    document.querySelectorAll('.patient-birthdate-input').forEach(function(input) {
        if (input.value) {
            const index = input.id.split('-')[1];
            const ageDisplay = document.getElementById(`patientAgeDisplay-${index}`);
            const ageHidden = document.getElementById(`patientAgeInput-${index}`);
            const categoryBtn = document.getElementById(`patientCategoryDropdownBtn-${index}`);
            const categoryHidden = document.getElementById(`patientCategoryHidden-${index}`);
            
            if (ageDisplay && ageHidden) {
                const age = calculateAge(input.value);
                ageDisplay.value = age;
                ageHidden.value = age;
                
                // Get the dropdown menu
                const categoryDropdown = categoryBtn?.nextElementSibling;
                const seniorOption = categoryDropdown?.querySelector('[data-value="Senior"]')?.parentElement;
                
                // Auto-select category based on age
                if (categoryBtn && categoryHidden) {
                    if (age >= 60) {
                        if (!categoryHidden.value) {
                            categoryBtn.textContent = 'Senior';
                            categoryHidden.value = 'Senior';
                        }
                        // Show Senior option
                        if (seniorOption) seniorOption.style.display = '';
                    } else {
                        // Hide Senior option for under 60
                        if (seniorOption) seniorOption.style.display = 'none';
                    }
                }
            }
        }
    });
});
