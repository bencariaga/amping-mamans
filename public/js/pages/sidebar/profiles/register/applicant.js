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

    if (applicantBirthdateInput && applicantAgeInput && applicantAgeHidden) {
        applicantBirthdateInput.addEventListener("change", updateApplicantAge);
        applicantBirthdateInput.addEventListener("input", updateApplicantAge);

        if (applicantBirthdateInput.value) {
            updateApplicantAge();
        }
    }

    const checkbox = document.getElementById("checkbox");
    const patientNumberInput = document.getElementById("patientNumberInput");

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

        document
            .querySelectorAll(
                "#patientLastNameInput-1, #patientFirstNameInput-1, #patientMiddleNameInput-1"
            )
            .forEach((field) => {
                field.readOnly = true;
                field.style.backgroundColor = "#e9ecef";
                field.style.cursor = "not-allowed";
            });

        document
            .querySelectorAll(
                "#patientSuffixDropdownBtn-1, #patientSexDropdownBtn-1"
            )
            .forEach((btn) => {
                btn.disabled = true;
                btn.style.pointerEvents = "none";
            });
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
        document.getElementById("patientAgeInput-1").value = "";

        document
            .querySelectorAll(
                "#patientLastNameInput-1, #patientFirstNameInput-1, #patientMiddleNameInput-1, #patientAgeInput-1"
            )
            .forEach((field) => {
                field.readOnly = false;
                field.style.backgroundColor = "";
                field.style.cursor = "";
            });

        document
            .querySelectorAll(
                "#patientSuffixDropdownBtn-1, #patientSexDropdownBtn-1"
            )
            .forEach((btn) => {
                btn.disabled = false;
                btn.style.pointerEvents = "";
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
});
