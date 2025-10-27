document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.querySelector("#editBtn");
    const saveBtn = document.querySelector("#saveBtn");
    const form = document.getElementById("tariffForm");
    const sortForm = document.getElementById("sortForm");
    const sortSelect = document.getElementById("sortSelect");
    const tariffInputs = document.querySelectorAll(
        ".tariff-input, .range-input",
    );

    let isEditMode = false;
    let hasOverlapError = false;

    const overlapWarning = document.createElement("div");
    overlapWarning.className = "overlap-warning";
    overlapWarning.innerHTML = "There are overlapping expense ranges. Please correct them before saving.";
    overlapWarning.style.display = "none";
    form.insertBefore(overlapWarning, form.firstChild);

    function formatNumber(num) {
        if (!num) return "";
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function cleanNumberInput(value) {
        return value.replace(/[^\d]/g, "");
    }

    function formatInput(input) {
        const value = cleanNumberInput(input.value);
        input.value = formatNumber(value);
    }

    function checkOverlap(tabPane) {
        const rows = tabPane.querySelectorAll("tbody tr");
        const ranges = [];
        let localHasError = false;

        rows.forEach((row) => {
            row.classList.remove("table-danger");
            const inputs = row.querySelectorAll("input");
            inputs.forEach((input) => input.classList.remove("is-invalid"));
        });

        rows.forEach((row) => {
            const minInput = row.querySelector('input[name*="range_min"]');
            const maxInput = row.querySelector('input[name*="range_max"]');

            if (minInput && maxInput) {
                const min = parseInt(cleanNumberInput(minInput.value) || "0");
                const max = parseInt(cleanNumberInput(maxInput.value) || "0");

                if (!isNaN(min) && !isNaN(max)) {
                    ranges.push({ min, max, row, minInput, maxInput });
                }
            }
        });

        ranges.sort((a, b) => a.min - b.min);

        for (let i = 0; i < ranges.length; i++) {
            const current = ranges[i];

            if (current.min >= current.max) {
                current.minInput.classList.add("is-invalid");
                current.maxInput.classList.add("is-invalid");
                localHasError = true;
                continue;
            }

            if (i < ranges.length - 1 && current.max > ranges[i + 1].min) {
                localHasError = true;
                current.row.classList.add("table-danger");
                ranges[i + 1].row.classList.add("table-danger");
                current.maxInput.classList.add("is-invalid");
                ranges[i + 1].minInput.classList.add("is-invalid");
            }
        }

        hasOverlapError = localHasError;
        overlapWarning.style.display = hasOverlapError ? "block" : "none";
        return hasOverlapError;
    }

    function handleNumberInput(e) {
        const input = e.target;
        const cursorPosition = input.selectionStart;
        let value = input.value;

        if (
            value.length > 1 &&
            value.startsWith("0") &&
            !value.startsWith("0.")
        ) {
            value = value.replace(/^0+/, "");
            if (value === "") value = "0";
        }

        const nonDigitsBeforeCursor = value
            .substring(0, cursorPosition)
            .replace(/[\d,]/g, "").length;

        const cleaned = cleanNumberInput(value);

        if (input.classList.contains("range-input") && cleaned.length > 7) {
            input.value = formatNumber(cleaned.substring(0, 7));
            return;
        }

        if (input.classList.contains("tariff-input") && cleaned.length > 3) {
            input.value = formatNumber(cleaned.substring(0, 3));
            return;
        }

        const formatted = formatNumber(cleaned);
        input.value = formatted;

        const newCursorPosition = Math.max(
            0,
            cursorPosition +
                (formatted.length - value.length) -
                nonDigitsBeforeCursor,
        );
        input.setSelectionRange(newCursorPosition, newCursorPosition);

        if (input.name.includes("range_")) {
            const tabPane = input.closest(".tab-pane");
            if (tabPane) {
                checkOverlap(tabPane);
            }
        }
    }

    function validateCoverage(input) {
        let value = cleanNumberInput(input.value);
        value = Math.min(parseInt(value || "0"), 100);
        input.value = value || "0";
    }

    function generateUniqueId() {
        return "new-" + Math.random().toString(36).substring(2, 11);
    }

    function addRow(button) {
        if (!isEditMode) return;

        const row = button.closest("tr");
        const tbody = row.parentNode;
        const newRow = row.cloneNode(true);
        const newId = generateUniqueId();
        const serviceId = button.getAttribute("data-service-id");
        const inputs = newRow.querySelectorAll('input[type="text"]');

        inputs.forEach((input) => {
            input.value = "";
            const nameParts = input.name.match(/^(range_min|range_max|tariff_amount)\[([^\]]+)\]\[([^\]]+)\]$/);
            if (nameParts) {
                input.name = `${nameParts[1]}[${serviceId}][${newId}]`;
            }
            input.removeAttribute("readonly");
            input.dataset.originalValue = "";

            if (input.classList.contains("range-input")) {
                input.addEventListener("input", handleNumberInput);
            } else if (input.classList.contains("tariff-input")) {
                input.addEventListener("blur", () => validateCoverage(input));
            }
        });

        const hiddenInput = newRow.querySelector('input[name="row_ids[]"]');
        if (hiddenInput) {
            hiddenInput.value = newId;
        }

        const removeBtn = newRow.querySelector(".btn-remove-row");
        removeBtn.disabled = false;
        removeBtn.setAttribute("data-service-id", serviceId);

        const addBtn = newRow.querySelector(".btn-add-row");
        if (addBtn) {
            addBtn.setAttribute("data-service-id", serviceId);
        }

        tbody.insertBefore(newRow, row.nextSibling);
        const firstInput = newRow.querySelector('input[type="text"]');

        if (firstInput) {
            firstInput.focus();
        }

        updateRemoveButtonsState(tbody);
    }

    function removeRow(button) {
        if (!isEditMode) return;

        const row = button.closest("tr");
        const tbody = row.parentNode;
        const allRows = Array.from(tbody.querySelectorAll("tr"));

        if (allRows.length <= 1) return;

        const rowIndex = allRows.indexOf(row);
        let nextRowToFocus = null;

        if (rowIndex < allRows.length - 1) {
            nextRowToFocus = allRows[rowIndex + 1];
        }

        else if (rowIndex > 0) {
            nextRowToFocus = allRows[rowIndex - 1];
        }

        row.remove();
        updateRemoveButtonsState(tbody);

        if (nextRowToFocus) {
            const firstInput =
                nextRowToFocus.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }
        }

        const activeTab = document.querySelector(".nav-link.active");
        if (activeTab) {
            const serviceId = activeTab
                .getAttribute("data-bs-target")
                .replace("#service-", "");
            checkOverlap(serviceId);
        }
    }

    function updateRemoveButtonsState(tbody) {
        const rows = tbody.querySelectorAll("tr");
        const removeButtons = tbody.querySelectorAll(".btn-remove-row");

        if (rows.length <= 1) {
            removeButtons.forEach((btn) => {
                btn.disabled = true;
            });
        } else {
            removeButtons.forEach((btn) => {
                btn.disabled = false;
            });
        }
    }

    function enableEditing() {
        isEditMode = true;

        document.querySelector(".view-mode").style.display = "none";
        document.querySelector(".edit-mode").style.display = "inline";

        const currentService =
            document.getElementById("currentService").textContent;
        document.getElementById("editingService").textContent = currentService;

        tariffInputs.forEach((input) => {
            input.removeAttribute("readonly");
            input.dataset.originalValue = input.value;

            if (input.classList.contains("range-input")) {
                input.addEventListener("input", handleNumberInput);
            } else if (input.classList.contains("tariff-input")) {
                input.addEventListener("blur", () => validateCoverage(input));
            }
        });

        document
            .querySelectorAll(".btn-add-row, .btn-remove-row")
            .forEach((btn) => {
                btn.disabled = false;
            });

        document.querySelectorAll("tbody").forEach((tbody) => {
            updateRemoveButtonsState(tbody);
        });

        editBtn.style.display = "none";
        saveBtn.style.display = "flex";

        const activeTabPane = document.querySelector(".tab-pane.active");
        if (activeTabPane) {
            checkOverlap(activeTabPane);
        }
    }

    function saveChanges() {
        let hasOverlap = false;

        document.querySelectorAll(".tab-pane").forEach((pane) => {
            if (checkOverlap(pane)) {
                hasOverlap = true;
            }
        });

        if (hasOverlap) {
            alert("Warning: There are overlapping expense ranges. Please correct them before saving.",);

            return false;
        }

        document.querySelectorAll('input[type="text"]').forEach((input) => {
            if (
                input.classList.contains("range-input") ||
                input.classList.contains("tariff-input")
            ) {
                const value = input.value.replace(/[^\d]/g, "");
                input.value = value || "0";
            }
        });

        form.submit();
    }

    function updateCurrentService(tabElement) {
        const serviceName = tabElement.textContent.trim();
        document.getElementById("currentService").textContent = serviceName;

        if (isEditMode) {
            document.getElementById("editingService").textContent = serviceName;
        }

        const targetId = tabElement.getAttribute("data-bs-target");
        const tabPane = document.querySelector(targetId);
        if (tabPane) {
            checkOverlap(tabPane);
        }
    }

    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach((button) => {
        button.addEventListener("shown.bs.tab", function (event) {
            updateCurrentService(event.target);
        });
    });

    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-add-row")) {
            addRow(e.target.closest(".btn-add-row"));
        } else if (e.target.closest(".btn-remove-row")) {
            removeRow(e.target.closest(".btn-remove-row"));
        }
    });

    if (editBtn) {
        editBtn.addEventListener("click", enableEditing);

        document
            .querySelectorAll(".btn-add-row, .btn-remove-row")
            .forEach((btn) => {
                btn.disabled = true;
            });
    }

    if (saveBtn) {
        saveBtn.addEventListener("click", saveChanges);
    }

    if (sortSelect && sortForm) {
        sortSelect.addEventListener("change", function () {
            sortForm.submit();
        });
    }
});
