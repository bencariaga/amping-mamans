document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.querySelector("#editExpRangesBtn");
    const saveBtn = document.querySelector("#saveExpRangesBtn");
    const form = document.getElementById("tariffForm");
    const container = document.getElementById("container");
    const sortForm = document.getElementById("sortForm");
    const sortSelect = document.getElementById("sortSelect");

    let isEditMode = false;
    let hasOverlapError = false;

    const overlapWarning = document.createElement("div");

    overlapWarning.className = "overlap-warning";
    overlapWarning.innerHTML = "There are overlapping expense ranges. Please correct them before saving.";
    overlapWarning.style.display = "none";
    form.insertBefore(overlapWarning, form.firstChild);

    if (container && editBtn) {
        const hasData = container.getAttribute('data-has-data') === '1';
        if (!hasData) {
            const icon = editBtn.querySelector('.nav-icon i');
            const label = editBtn.querySelector('.nav-text');
            if (icon) {
                icon.classList.remove('fa-edit');
                icon.classList.add('fa-plus');
            }
            if (label) {
                label.innerHTML = 'Add Tariff<br>List Data';
            }
        }
    }

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

                if (!isNaN(min) && !isNaN(max) && (min > 0 || max > 0)) {
                    ranges.push({ min, max, row, minInput, maxInput });
                }
            }
        });

        for (let i = 0; i < ranges.length; i++) {
            for (let j = i + 1; j < ranges.length; j++) {
                if (ranges[i].min === ranges[j].min && ranges[i].max === ranges[j].max) {
                    localHasError = true;
                    ranges[i].row.classList.add("table-danger");
                    ranges[j].row.classList.add("table-danger");
                    ranges[i].minInput.classList.add("is-invalid");
                    ranges[i].maxInput.classList.add("is-invalid");
                    ranges[j].minInput.classList.add("is-invalid");
                    ranges[j].maxInput.classList.add("is-invalid");
                }
            }
        }

        for (let i = 0; i < ranges.length; i++) {
            for (let j = i + 1; j < ranges.length; j++) {
                if (ranges[i].row.classList.contains("table-danger") && ranges[j].row.classList.contains("table-danger")) {
                    continue;
                }

                if (ranges[i].min === ranges[j].min || ranges[i].max === ranges[j].max) {
                    localHasError = true;
                    ranges[i].row.classList.add("table-danger");
                    ranges[j].row.classList.add("table-danger");

                    if (ranges[i].min === ranges[j].min) {
                        ranges[i].minInput.classList.add("is-invalid");
                        ranges[j].minInput.classList.add("is-invalid");
                    }
                    if (ranges[i].max === ranges[j].max) {
                        ranges[i].maxInput.classList.add("is-invalid");
                        ranges[j].maxInput.classList.add("is-invalid");
                    }
                }

                if (ranges[i].min === ranges[j].max || ranges[i].max === ranges[j].min) {
                    localHasError = true;
                    ranges[i].row.classList.add("table-danger");
                    ranges[j].row.classList.add("table-danger");

                    if (ranges[i].min === ranges[j].max) {
                        ranges[i].minInput.classList.add("is-invalid");
                        ranges[j].maxInput.classList.add("is-invalid");
                    }
                    if (ranges[i].max === ranges[j].min) {
                        ranges[i].maxInput.classList.add("is-invalid");
                        ranges[j].minInput.classList.add("is-invalid");
                    }
                }
            }
        }

        for (let i = 0; i < ranges.length; i++) {
            const current = ranges[i];
            if (current.min >= current.max) {
                current.minInput.classList.add("is-invalid");
                current.maxInput.classList.add("is-invalid");
                current.row.classList.add("table-danger");
                localHasError = true;
            }
        }

        ranges.sort((a, b) => a.min - b.min);

        for (let i = 0; i < ranges.length - 1; i++) {
            const current = ranges[i];
            const next = ranges[i + 1];
            if (current.min >= current.max || next.min >= next.max) {
                continue;
            }

            if (current.max >= next.min) {
                localHasError = true;
                current.row.classList.add("table-danger");
                next.row.classList.add("table-danger");
                current.maxInput.classList.add("is-invalid");
                next.minInput.classList.add("is-invalid");
            }
        }

        hasOverlapError = localHasError;
        overlapWarning.style.display = hasOverlapError ? "block" : "none";

        if (hasOverlapError) {
            overlapWarning.innerHTML = "There are duplicate values or overlapping expense ranges. Please correct them before saving.";
        }

        const addButtons = tabPane.querySelectorAll(".btn-add-row");
        addButtons.forEach((btn) => {
            if (hasOverlapError) {
                btn.disabled = true;
                btn.title = "Fix duplicates or overlaps before adding a new row";
            } else {
                btn.disabled = false;
                btn.title = "Add a row below.";
            }
        });

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

        let formatted = formatNumber(cleaned);

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

                const row = input.closest("tr");
                const minInput = row.querySelector(".range-min");
                const maxInput = row.querySelector(".range-max");
                const coverageInput = row.querySelector(".coverage-percent");

                if (minInput && maxInput && coverageInput) {
                    const minValue = parseInt(cleanNumberInput(minInput.value) || "0");
                    const maxValue = parseInt(cleanNumberInput(maxInput.value) || "0");

                    if (minValue > 0 && maxValue > 0) {
                        coverageInput.disabled = false;
                        coverageInput.removeAttribute("readonly");
                    } else {
                        coverageInput.disabled = true;
                        coverageInput.setAttribute("readonly", "readonly");
                    }
                }
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

    function updateInputEnabling(row) {
        if (!isEditMode) return;

        const minInput = row.querySelector(".range-min");
        const maxInput = row.querySelector(".range-max");
        const coverageInput = row.querySelector(".coverage-percent");

        if (!minInput || !maxInput || !coverageInput) return;

        const minValue = parseInt(cleanNumberInput(minInput.value) || "0");
        const maxValue = parseInt(cleanNumberInput(maxInput.value) || "0");
        const coverageValue = parseInt(cleanNumberInput(coverageInput.value) || "0");

        const tabPane = row.closest(".tab-pane");
        const hasOverlap = tabPane ? checkOverlap(tabPane) : false;

        if (minValue >= 0 && !hasOverlap) {
            maxInput.disabled = false;
            maxInput.removeAttribute("readonly");
        } else {
            maxInput.disabled = true;
            maxInput.setAttribute("readonly", "readonly");
        }

        if (maxValue > 0 && minValue >= 0 && !hasOverlap) {
            coverageInput.disabled = false;
            coverageInput.removeAttribute("readonly");
        } else {
            coverageInput.disabled = true;
            coverageInput.setAttribute("readonly", "readonly");
        }
    }

    function updateAllInputEnabling() {
        document.querySelectorAll("tbody tr").forEach((row) => {
            updateInputEnabling(row);
        });
    }


    function addRow(button) {
        if (!isEditMode) return;

        const row = button.closest("tr");
        const tbody = row.parentNode;

        const tabPane = row.closest(".tab-pane");
        if (tabPane && checkOverlap(tabPane)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fix overlaps first',
                    text: 'Please correct duplicate or overlapping values before adding a new row.'
                });
            } else {
                alert("Please fix duplicate or overlapping values before adding a new row.");
            }
            return;
        }

        const newRow = row.cloneNode(true);
        const newId = generateUniqueId();
        const serviceId = button.getAttribute("data-service-id");

        const currentMaxInput = row.querySelector(".range-max");
        const currentMaxValue = currentMaxInput ? parseInt(cleanNumberInput(currentMaxInput.value) || "0") : 0;
        const newMinValue = currentMaxValue > 0 ? currentMaxValue + 1 : 0;

        const inputs = newRow.querySelectorAll('input[type="text"]');

        inputs.forEach((input) => {
            input.value = "";
            const nameParts = input.name.match(/^(range_min|range_max|tariff_amount)\[([^\]]+)\]\[([^\]]+)\]$/);
            if (nameParts) {
                input.name = `${nameParts[1]}[${serviceId}][${newId}]`;
            }
            input.dataset.originalValue = "";

            if (input.classList.contains("range-min")) {
                input.removeAttribute("readonly");
                input.disabled = false;
                if (newMinValue > 0) {
                    input.value = formatNumber(newMinValue.toString());
                }
            } else if (input.classList.contains("range-max")) {
                input.removeAttribute("readonly");
                input.disabled = false;
            } else {
                input.setAttribute("readonly", "readonly");
                input.disabled = true;
            }

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

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Row added',
                showConfirmButton: false,
                timer: 1200
            });
        }
    }

    async function removeRow(button) {
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

        let proceed = true;
        if (typeof Swal !== 'undefined') {
            const res = await Swal.fire({
                icon: 'warning',
                title: 'Remove this row?',
                text: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Remove',
            });
            proceed = res.isConfirmed;
        } else {
            proceed = confirm('Remove this row?');
        }
        if (!proceed) return;

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

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Row removed',
                showConfirmButton: false,
                timer: 1200
            });
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

        document.querySelectorAll(".range-min").forEach((input) => {
            input.removeAttribute("readonly");
            input.disabled = false;
            input.dataset.originalValue = input.value;
            input.addEventListener("input", handleNumberInput);
        });

        document.querySelectorAll(".range-max").forEach((input) => {
            input.removeAttribute("readonly");
            input.disabled = false;
            input.dataset.originalValue = input.value;
            input.addEventListener("input", handleNumberInput);
        });

        document.querySelectorAll(".coverage-percent").forEach((input) => {
            input.dataset.originalValue = input.value;
            input.addEventListener("blur", () => validateCoverage(input));
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

        document.querySelectorAll(".tab-pane").forEach((tabPane) => {
            checkOverlap(tabPane);
        });

        const activeTabPane = document.querySelector(".tab-pane.active");
        if (activeTabPane) {
            checkOverlap(activeTabPane);
        }

        document.querySelectorAll("tbody tr").forEach((row) => {
            const minInput = row.querySelector(".range-min");
            const maxInput = row.querySelector(".range-max");
            const coverageInput = row.querySelector(".coverage-percent");

            if (minInput && maxInput && coverageInput) {
                const minValue = parseInt(cleanNumberInput(minInput.value) || "0");
                const maxValue = parseInt(cleanNumberInput(maxInput.value) || "0");

                if (minValue > 0 && maxValue > 0) {
                    coverageInput.disabled = false;
                    coverageInput.removeAttribute("readonly");
                }
            }
        });
    }

    function saveChanges() {
        let hasOverlap = false;

        document.querySelectorAll(".tab-pane").forEach((pane) => {
            if (checkOverlap(pane)) {
                hasOverlap = true;
            }
        });

        if (hasOverlap) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Overlapping ranges',
                    text: 'There are duplicate or overlapping values. Please correct them before saving.'
                });
            } else {
                alert("Warning: There are overlapping expense ranges. Please correct them before saving.");
            }
            return false;
        }

        const serviceTabs = document.querySelectorAll('#serviceTabs .nav-item[data-service-type]');
        if (serviceTabs.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'No services', text: 'Please add at least one service before saving.' });
            } else {
                alert('Please add at least one service before saving.');
            }
            return false;
        }

        const tabPanes = document.querySelectorAll('.tab-pane');
        for (const pane of tabPanes) {
            const rows = pane.querySelectorAll('tbody tr');
            if (rows.length < 2) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'Incomplete ranges', text: 'Each service needs at least two ranges.' });
                } else { alert('Each service needs at least two ranges.'); }
                return false;
            }

            for (const row of rows) {
                const minInput = row.querySelector('.range-min');
                const maxInput = row.querySelector('.range-max');
                const covInput = row.querySelector('.coverage-percent');
                const min = parseInt(cleanNumberInput(minInput?.value || '0') || '0', 10);
                const max = parseInt(cleanNumberInput(maxInput?.value || '0') || '0', 10);
                const cov = parseInt(cleanNumberInput(covInput?.value || '0') || '0', 10);
                if (!min || !max || !cov || cov < 1 || cov > 100 || min >= max) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: 'Invalid values', text: 'Ensure all ranges have Min < Max and Coverage between 1 and 100.' });
                    } else { alert('Ensure all ranges have Min < Max and Coverage between 1 and 100.'); }
                    return false;
                }
            }
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
            if (isEditMode) {
                tabPane.querySelectorAll(".range-min").forEach((input) => {
                    input.removeAttribute("readonly");
                    input.disabled = false;
                });

                tabPane.querySelectorAll(".range-max").forEach((input) => {
                    input.removeAttribute("readonly");
                    input.disabled = false;
                });

                tabPane.querySelectorAll("tbody tr").forEach((row) => {
                    const minInput = row.querySelector(".range-min");
                    const maxInput = row.querySelector(".range-max");
                    const coverageInput = row.querySelector(".coverage-percent");

                    if (minInput && maxInput && coverageInput) {
                        const minValue = parseInt(cleanNumberInput(minInput.value) || "0");
                        const maxValue = parseInt(cleanNumberInput(maxInput.value) || "0");

                        if (minValue > 0 && maxValue > 0) {
                            coverageInput.disabled = false;
                            coverageInput.removeAttribute("readonly");
                        }
                    }
                });
            }

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
