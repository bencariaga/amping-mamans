document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.querySelector("#editBtn");
    const saveBtn = document.querySelector("#saveBtn");
    const form = document.getElementById("tariffForm");
    const container = document.getElementById("container");
    const sortForm = document.getElementById("sortForm");
    const sortSelect = document.getElementById("sortSelect");
    const addServiceDropdown = document.getElementById("addServiceDropdown");
    const addServiceDropdownContainer = document.getElementById("addServiceDropdownContainer");

    let isEditMode = false;
    let hasOverlapError = false;

    const overlapWarning = document.createElement("div");
    overlapWarning.className = "overlap-warning";
    overlapWarning.innerHTML = "There are overlapping expense ranges. Please correct them before saving.";
    overlapWarning.style.display = "none";
    form.insertBefore(overlapWarning, form.firstChild);

    // If this tariff version has no data yet, present the action as "Add" instead of "Edit".
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
        // Check only within the current tab
        const rows = tabPane.querySelectorAll("tbody tr");
        const ranges = [];
        let localHasError = false;

        // Clear error states only in current tab
        rows.forEach((row) => {
            row.classList.remove("table-danger");
            const inputs = row.querySelectorAll("input");
            inputs.forEach((input) => input.classList.remove("is-invalid"));
        });

        // Collect ranges only from current tab
        rows.forEach((row) => {
            const minInput = row.querySelector('input[name*="range_min"]');
            const maxInput = row.querySelector('input[name*="range_max"]');

            if (minInput && maxInput) {
                const min = parseInt(cleanNumberInput(minInput.value) || "0");
                const max = parseInt(cleanNumberInput(maxInput.value) || "0");

                // Include ranges if at least min OR max has a value (not both need to be filled)
                // This allows validation to trigger even when editing partial ranges
                if (!isNaN(min) && !isNaN(max) && (min > 0 || max > 0)) {
                    ranges.push({ min, max, row, minInput, maxInput });
                }
            }
        });

        // Check for exact duplicate ranges first (complete range duplicates)
        for (let i = 0; i < ranges.length; i++) {
            for (let j = i + 1; j < ranges.length; j++) {
                // Check if both min and max values are exactly the same (exact duplicate)
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

        // Check for duplicate individual values (min or max duplicates)
        for (let i = 0; i < ranges.length; i++) {
            for (let j = i + 1; j < ranges.length; j++) {
                // Skip if already marked as duplicate from exact range check
                if (ranges[i].row.classList.contains("table-danger") && ranges[j].row.classList.contains("table-danger")) {
                    continue;
                }
                
                // Check if min or max values are the same
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
                
                // Check if one row's min equals another row's max (boundary duplicates)
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

        // Check for invalid ranges (min >= max)
        for (let i = 0; i < ranges.length; i++) {
            const current = ranges[i];
            if (current.min >= current.max) {
                current.minInput.classList.add("is-invalid");
                current.maxInput.classList.add("is-invalid");
                current.row.classList.add("table-danger");
                localHasError = true;
            }
        }

        // Sort ranges by minimum value for overlap checking
        ranges.sort((a, b) => a.min - b.min);

        // Check for overlapping ranges and sequential validation
        for (let i = 0; i < ranges.length - 1; i++) {
            const current = ranges[i];
            const next = ranges[i + 1];

            // Skip invalid ranges
            if (current.min >= current.max || next.min >= next.max) {
                continue;
            }

            // Check if current range overlaps or touches next range
            // This catches: overlap (max > min) AND boundary duplicate (max >= min)
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
        
        // Update warning message to be more specific
        if (hasOverlapError) {
            overlapWarning.innerHTML = "There are duplicate values or overlapping expense ranges. Please correct them before saving.";
        }
        
        // Disable/enable add row buttons only in current tab based on overlap status
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
                
                // Enable coverage input if both min and max have values
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

    // Progressive input enabling logic
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

        // Minimum is always enabled in edit mode
        // Maximum is enabled if minimum has value (including 0) and no overlap
        if (minValue >= 0 && !hasOverlap) {
            maxInput.disabled = false;
            maxInput.removeAttribute("readonly");
        } else {
            maxInput.disabled = true;
            maxInput.setAttribute("readonly", "readonly");
        }

        // Coverage is enabled if maximum has non-zero value and no overlap
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

    function updateServiceDropdownVisibility() {
        if (!addServiceDropdown || !addServiceDropdownContainer) return;

        const availableOptions = addServiceDropdown.querySelectorAll("option:not([value=''])");

        if (availableOptions.length > 0) {
            addServiceDropdownContainer.style.display = "";
        } else {
            addServiceDropdownContainer.style.display = "none";
        }
    }

    function updateRemoveServiceButtons() {
        const serviceItems = document.querySelectorAll("#serviceTabs .nav-item[data-service-type]");
        const removeButtons = document.querySelectorAll(".btn-remove-service");

        // Allow removal of all service types - no minimum restriction
        removeButtons.forEach((btn) => {
            btn.disabled = false;
        });
    }

    function removeServiceType(serviceType, serviceId) {
        if (!isEditMode) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'info', title: 'Edit required', text: 'Click the Edit/Add button to modify services.' });
            }
            return false;
        }

        const tabsUl = document.getElementById('serviceTabs');
        const navItem = document.querySelector(`#serviceTabs .nav-item[data-service-type="${serviceType}"]`);
        let tabPane = null;
        if (navItem) {
            const btn = navItem.querySelector('button.nav-link');
            const target = btn ? btn.getAttribute('data-bs-target') : null;
            tabPane = target ? document.querySelector(target) : null;
        }

        if (!navItem || !tabPane) return false;

        // Check if this tab is active
        const isActive = navItem.querySelector(".nav-link.active") !== null;

        // Remove the tab pane and nav item
        tabPane.remove();
        navItem.remove();

        // If the removed tab was active, activate the first remaining tab
        if (isActive) {
            const firstBtn = tabsUl ? tabsUl.querySelector('button.nav-link') : null;
            if (firstBtn) firstBtn.click();
        }

        // Add the service back to the dropdown
        if (addServiceDropdown) {
            const option = document.createElement("option");
            option.value = serviceId;
            option.textContent = serviceType;
            addServiceDropdown.appendChild(option);
        }

        // Update service count
        const serviceCount = document.querySelector(".service-count");
        if (serviceCount) {
            const count = document.querySelectorAll("#serviceTabs .nav-item[data-service-type]").length;
            serviceCount.textContent = `TL Version's Number of Services: ${count}`;
        }

        updateServiceDropdownVisibility();
        updateRemoveServiceButtons();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Service "${serviceType}" added`,
                showConfirmButton: false,
                timer: 1400
            });
        }
    }

    function addServiceType(serviceId, serviceType) {
        if (!isEditMode) return;

        const serviceTabs = document.getElementById("serviceTabs");
        const tabContent = document.getElementById("serviceTabsContent");

        if (!serviceTabs || !tabContent) return;

        // Get current service count
        const existingTabs = document.querySelectorAll("#serviceTabs .nav-item[data-service-type]");
        const newIndex = existingTabs.length;

        // Create new nav item
        const navItem = document.createElement("li");
        navItem.className = "nav-item";
        navItem.setAttribute("role", "presentation");
        navItem.setAttribute("data-service-type", serviceType);
        navItem.setAttribute("data-service-id", serviceId);

        const tabWrapper = document.createElement("div");
        tabWrapper.className = "service-tab-wrapper";

        const navLink = document.createElement("button");
        navLink.className = "nav-link";
        navLink.id = `tab-${newIndex}`;
        navLink.setAttribute("data-bs-toggle", "tab");
        navLink.setAttribute("data-bs-target", `#service-${newIndex}`);
        navLink.setAttribute("type", "button");
        navLink.setAttribute("role", "tab");
        navLink.setAttribute("aria-controls", `service-${newIndex}`);
        navLink.setAttribute("aria-selected", "false");
        navLink.textContent = serviceType;

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.className = "btn-remove-service";
        removeBtn.setAttribute("data-service-type", serviceType);
        removeBtn.setAttribute("data-service-id", serviceId);
        removeBtn.setAttribute("title", "Remove this service type");
        removeBtn.innerHTML = '<i class="fas fa-trash"></i>';

        tabWrapper.appendChild(navLink);
        tabWrapper.appendChild(removeBtn);
        navItem.appendChild(tabWrapper);

        // Insert before dropdown container
        serviceTabs.insertBefore(navItem, addServiceDropdownContainer);

        // Create new tab pane with one empty row
        const tabPane = document.createElement("div");
        tabPane.className = "tab-pane fade";
        tabPane.id = `service-${newIndex}`;
        tabPane.setAttribute("role", "tabpanel");
        tabPane.setAttribute("aria-labelledby", `tab-${newIndex}`);

        const newRowId = generateUniqueId();
        tabPane.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="shadow-sm tariff-section p-3 mx-auto">
                        <div class="table-responsive">
                            <table class="expense-table w-100">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="money-amount-header text-center" id="money-amount-header-1">Expense Range</th>
                                        <th rowspan="2" class="money-amount-header text-center" id="money-amount-header-2">Coverage<br>(%)</th>
                                        <th rowspan="2" class="money-amount-header text-center" id="money-amount-header-3">Actions</th>
                                    </tr>
                                    <tr>
                                        <th class="money-amount-header text-center">Minimum</th>
                                        <th class="money-amount-header text-center">Maximum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="money-amount-cell">
                                            <div class="money-amount-container">
                                                <span class="money-currency fw-bold pe-2">₱</span>
                                                <input type="text" name="range_min[${serviceId}][${newRowId}]" class="form-control form-control-sm range-input range-min text-end money-value" value="" placeholder="0" maxlength="8">
                                            </div>
                                        </td>
                                        <td class="money-amount-cell">
                                            <div class="money-amount-container">
                                                <span class="money-currency fw-bold pe-2">₱</span>
                                                <input type="text" name="range_max[${serviceId}][${newRowId}]" class="form-control form-control-sm range-input range-max text-end money-value" value="" maxlength="8" disabled>
                                            </div>
                                        </td>
                                        <td class="money-amount-cell">
                                            <div class="money-amount-container">
                                                <input type="text" name="tariff_amount[${serviceId}][${newRowId}]" class="form-control form-control-sm tariff-input coverage-percent text-end money-value" value="" maxlength="4" disabled>
                                                <span class="money-currency fw-bolder pe-1">%</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-primary btn-add-row" data-service-id="${serviceId}" title="Add a row below.">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger btn-remove-row" data-service-id="${serviceId}" title="Remove this row." disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <input type="hidden" name="row_ids[]" value="${newRowId}">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;

        tabContent.appendChild(tabPane);

        // Attach event listeners to new inputs
        const newInputs = tabPane.querySelectorAll(".range-input, .tariff-input");
        newInputs.forEach((input) => {
            if (input.classList.contains("range-input")) {
                input.addEventListener("input", handleNumberInput);
            } else if (input.classList.contains("tariff-input")) {
                input.addEventListener("blur", () => validateCoverage(input));
            }
        });

        // Update tab button listener
        navLink.addEventListener("shown.bs.tab", function (event) {
            updateCurrentService(event.target);
        });

        // Remove option from dropdown
        const optionToRemove = addServiceDropdown.querySelector(`option[value="${serviceId}"]`);
        if (optionToRemove) {
            optionToRemove.remove();
        }

        // Reset dropdown
        addServiceDropdown.value = "";

        // Update service count
        const serviceCount = document.querySelector(".service-count");
        if (serviceCount) {
            const count = document.querySelectorAll("#serviceTabs .nav-item[data-service-type]").length;
            serviceCount.textContent = `TL Version's Number of Services: ${count}`;
        }

        // Switch to the new tab
        navLink.click();

        updateServiceDropdownVisibility();
        updateRemoveServiceButtons();

        return true;
    }

    function addRow(button) {
        if (!isEditMode) return;

        const row = button.closest("tr");
        const tbody = row.parentNode;
        
        // Check for duplicates or overlaps before adding new row
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
        
        // Get the current row's maximum value to set as new row's minimum + 1
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

            // Set the new row's minimum to current row's maximum + 1
            if (input.classList.contains("range-min")) {
                input.removeAttribute("readonly");
                input.disabled = false;
                if (newMinValue > 0) {
                    input.value = formatNumber(newMinValue.toString());
                }
            } else if (input.classList.contains("range-max")) {
                // Maximum should be enabled but empty for manual input
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

        // Confirm removal
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

        // Enable minimum inputs
        document.querySelectorAll(".range-min").forEach((input) => {
            input.removeAttribute("readonly");
            input.disabled = false;
            input.dataset.originalValue = input.value;
            input.addEventListener("input", handleNumberInput);
        });

        // Enable maximum inputs - allow full editing
        document.querySelectorAll(".range-max").forEach((input) => {
            input.removeAttribute("readonly");
            input.disabled = false;
            input.dataset.originalValue = input.value;
            input.addEventListener("input", handleNumberInput);
        });

        // Set up coverage inputs
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

        // Show remove service buttons
        document.querySelectorAll(".btn-remove-service").forEach((btn) => {
            btn.style.display = "";
        });

        editBtn.style.display = "none";
        saveBtn.style.display = "flex";

        // Check all tab panes for duplicates/overlaps when entering edit mode
        document.querySelectorAll(".tab-pane").forEach((tabPane) => {
            checkOverlap(tabPane);
        });
        
        const activeTabPane = document.querySelector(".tab-pane.active");
        if (activeTabPane) {
            checkOverlap(activeTabPane);
        }

        // Enable coverage inputs based on current values
        document.querySelectorAll("tbody tr").forEach((row) => {
            const minInput = row.querySelector(".range-min");
            const maxInput = row.querySelector(".range-max");
            const coverageInput = row.querySelector(".coverage-percent");
            
            if (minInput && maxInput && coverageInput) {
                const minValue = parseInt(cleanNumberInput(minInput.value) || "0");
                const maxValue = parseInt(cleanNumberInput(maxInput.value) || "0");
                
                // Enable coverage only if both min and max have values
                if (minValue > 0 && maxValue > 0) {
                    coverageInput.disabled = false;
                    coverageInput.removeAttribute("readonly");
                }
            }
        });

        // Show dropdown if there are available service types
        updateServiceDropdownVisibility();
        updateRemoveServiceButtons();
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

        // Additional validations
        // 1) At least one service
        const serviceTabs = document.querySelectorAll('#serviceTabs .nav-item[data-service-type]');
        if (serviceTabs.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'No services', text: 'Please add at least one service before saving.' });
            } else {
                alert('Please add at least one service before saving.');
            }
            return false;
        }

        // 2) Each service must have at least two rows and complete values
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
            // If in edit mode, ensure all inputs in this tab are enabled
            if (isEditMode) {
                tabPane.querySelectorAll(".range-min").forEach((input) => {
                    input.removeAttribute("readonly");
                    input.disabled = false;
                });
                
                tabPane.querySelectorAll(".range-max").forEach((input) => {
                    input.removeAttribute("readonly");
                    input.disabled = false;
                });
                
                // Enable coverage inputs that have both min and max values
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

    // Service dropdown change handler
    if (addServiceDropdown) {
        addServiceDropdown.addEventListener("change", function () {
            const selectedServiceId = this.value;
            if (selectedServiceId) {
                const selectedOption = this.options[this.selectedIndex];
                const serviceType = selectedOption.textContent;
                addServiceType(selectedServiceId, serviceType);
            }
        });
    }

    // Remove service button handler
    document.addEventListener("click", async function (e) {
        if (e.target.closest(".btn-remove-service")) {
            const button = e.target.closest(".btn-remove-service");
            const serviceType = button.getAttribute("data-service-type");
            const serviceId = button.getAttribute("data-service-id");

            let proceed = true;
            if (typeof Swal !== 'undefined') {
                const res = await Swal.fire({
                    icon: 'warning',
                    title: `Remove "${serviceType}"?`,
                    text: 'All rows under this service will be removed from this draft.',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Remove'
                });
                proceed = res.isConfirmed;
            } else {
                proceed = confirm(`Remove "${serviceType}" from this tariff list?`);
            }

            if (proceed) {
                const removed = removeServiceType(serviceType, serviceId);
                if (removed && typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `Service "${serviceType}" removed`,
                        showConfirmButton: false,
                        timer: 1400
                    });
                }
            }
        }
    });
});
