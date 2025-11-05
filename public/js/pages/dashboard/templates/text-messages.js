document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(buttonId, inputId, formId = null) {
        const dropdownButton = document.getElementById(buttonId);
        const hiddenInput = document.getElementById(inputId);
        const dropdownItems = document.querySelectorAll(`#${buttonId} + .dropdown-menu .dropdown-item`);

        if (!dropdownButton || !hiddenInput) return;

        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const value = this.getAttribute('data-value');
                const text = this.textContent.trim();
                hiddenInput.value = value;
                dropdownButton.textContent = text;
                dropdownItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                if (formId) {
                    const form = document.getElementById(formId);
                    if (form) form.submit();
                }
            });
        });

        const initialValue = hiddenInput.value;
        const initialTextElement = Array.from(dropdownItems).find(item => item.getAttribute('data-value') === initialValue);

        if (initialTextElement) {
            dropdownButton.textContent = initialTextElement.textContent.trim();
            initialTextElement.classList.add('active');
        } else if (dropdownItems.length > 0) {
            dropdownButton.textContent = dropdownItems[0].textContent.trim();
            dropdownItems[0].classList.add('active');
            hiddenInput.value = dropdownItems[0].getAttribute('data-value');
        }

        dropdownButton.addEventListener('show.bs.dropdown', function () {
            this.classList.add('rotated');
        });

        dropdownButton.addEventListener('hide.bs.dropdown', function () {
            this.classList.remove('rotated');
        });
    }

    setupDropdown('sortDropdownBtn', 'filter-sort-by', 'template-filter-form');
    setupDropdown('perPageDropdownBtn', 'filter-per-page', 'template-filter-form');

    const templateTextarea = document.getElementById('msg_tmp_text');
    const titleInput = document.getElementById('msg_tmp_title');
    const previewOutput = document.getElementById('output-text-preview');
    const placeholderButtons = document.querySelectorAll('.placeholder-btn');
    const smsLengthChar = document.getElementById('sms-length-char');
    const charCount = document.getElementById('char-count');
    const titleCount = document.getElementById('title-count');
    const undoButton = document.getElementById('undo-button');
    const redoButton = document.getElementById('redo-button');
    const form = document.getElementById('sms-template-form');
    const titleErrorDiv = document.getElementById('title-error');
    const textErrorDiv = document.getElementById('text-error');
    const addSmsTmpBtn = document.getElementById('addSmsTmpBtn');
    const updateSmsTmpBtn = document.getElementById('updateSmsTmpBtn');
    const initialMsgText = document.getElementById('initial-msg-text');
    const isEditPage = initialMsgText !== null;

    const applicationDate = new Date();
    const reapplicationDate = new Date();

    reapplicationDate.setDate(applicationDate.getDate() + 90);
    const dateOptions = { month: 'long', day: 'numeric', year: 'numeric' };

    const applicationDateFormatted = applicationDate.toLocaleDateString('en-US', dateOptions);
    const reapplicationDateFormatted = reapplicationDate.toLocaleDateString('en-US', dateOptions);

    if (!templateTextarea || !previewOutput || !undoButton || !redoButton || !form) return;

    const exampleData = {
        "applicant->client->member->first_name": "Elizabeth",
        "applicant->client->member->middle_name": "Alexandra",
        "applicant->client->member->last_name": "Mary",
        "applicant->client->member->suffix": "II",
        "patient->client->member->first_name": "Juan",
        "patient->client->member->middle_name": "Cruz",
        "patient->client->member->last_name": "Santos",
        "patient->client->member->suffix": "Jr.",
        "application->service": "Hospital Bill",
        "application->affiliate_partner->affiliate_partner_name": "St. Elizabeth Hospital, Inc.",
        "application->billed_amount": "100,000",
        "application->assistance_amount": "80,000",
        "application->application_date": applicationDateFormatted,
        "application->reapplication_date": reapplicationDateFormatted
    };

    const labelMap = {
        "[$application->applicant->client->member->first_name]": "[Applicant's First Name]",
        "[$application->applicant->client->member->middle_name]": "[Applicant's Middle Name]",
        "[$application->applicant->client->member->last_name]": "[Applicant's Last Name]",
        "[$application->applicant->client->member->suffix]": "[Applicant's Suffix Name]",
        "[$application->patient->client->member->first_name]": "[Patient's First Name]",
        "[$application->patient->client->member->middle_name]": "[Patient's Middle Name]",
        "[$application->patient->client->member->last_name]": "[Patient's Last Name]",
        "[$application->patient->client->member->suffix]": "[Patient's Suffix Name]",
        "[$application->service]": "[Service]",
        "[$application->affiliate_partner->affiliate_partner_name]": "[Affiliate Partner]",
        "[$application->billed_amount]": "[Billed Amount]",
        "[$application->assistance_amount]": "[Assistance Amount]",
        "[$application->application_date]": "[Application Date]",
        "[$application->reapplication_date]": "[Reapplication Date]",
    };

    const backendMap = Object.fromEntries(Object.entries(labelMap).map(([key, value]) => [value, key]));
    const readablePlaceholderRegex = new RegExp(Object.keys(backendMap).map(label => label.replace(/[\[\]]/g, '\\$&')).join('|'), 'g');

    const labelToExampleMap = {
        "[Applicant's First Name]": exampleData["applicant->client->member->first_name"],
        "[Applicant's Middle Name]": exampleData["applicant->client->member->middle_name"],
        "[Applicant's Last Name]": exampleData["applicant->client->member->last_name"],
        "[Applicant's Suffix Name]": exampleData["applicant->client->member->suffix"],
        "[Patient's First Name]": exampleData["patient->client->member->first_name"],
        "[Patient's Middle Name]": exampleData["patient->client->member->middle_name"],
        "[Patient's Last Name]": exampleData["patient->client->member->last_name"],
        "[Patient's Suffix Name]": exampleData["patient->client->member->suffix"],
        "[Service]": exampleData["application->service"],
        "[Affiliate Partner]": exampleData["application->affiliate_partner->affiliate_partner_name"],
        "[Billed Amount]": exampleData["application->billed_amount"],
        "[Assistance Amount]": exampleData["application->assistance_amount"],
        "[Application Date]": exampleData["application->application_date"],
        "[Reapplication Date]": exampleData["application->reapplication_date"]
    };

    let history = [];
    let historyIndex = -1;

    const historyLimit = 50;

    function saveState() {
        let text = templateTextarea.value;
        const submittedText = text.replace(/[\n\r]/g, ';');

        const backendText = submittedText.replace(readablePlaceholderRegex, (match) => {
            return backendMap[match] || match;
        });

        if (history.length > 0 && history[historyIndex] === backendText) return;

        if (historyIndex < history.length - 1) {
            history = history.slice(0, historyIndex + 1);
        }

        if (history.length >= historyLimit) {
            history.shift();
        } else {
            historyIndex++;
        }

        history[historyIndex] = backendText;
        updateUndoRedoButtons();
    }

    function loadState(text) {
        const readableTextWithNewlines = text.replace(/;/g, '\n');
        const backendPlaceholderRegex = new RegExp(Object.keys(labelMap).map(key => key.replace(/[\[\]\$\-]/g, '\\$&')).join('|'), 'g');

        const readableText = readableTextWithNewlines.replace(backendPlaceholderRegex, (match) => {
            return labelMap[match] || match;
        });

        templateTextarea.value = readableText;
        updatePreview();
        updateUndoRedoButtons();
        templateTextarea.focus();
    }

    function undo() {
        if (historyIndex > 0) {
            historyIndex--;
            loadState(history[historyIndex]);
        }
    }

    function redo() {
        if (historyIndex < history.length - 1) {
            historyIndex++;
            loadState(history[historyIndex]);
        }
    }

    function updateUndoRedoButtons() {
        const isUndoDisabled = historyIndex <= 0;
        const isRedoDisabled = historyIndex >= history.length - 1;

        undoButton.disabled = isUndoDisabled;
        redoButton.disabled = isRedoDisabled;

        const undoIcon = undoButton.querySelector('.fas');
        const redoIcon = redoButton.querySelector('.fas');

        if (undoIcon) {
            undoIcon.classList.toggle('text-muted', isUndoDisabled);
        }

        if (redoIcon) {
            redoIcon.classList.toggle('text-muted', isRedoDisabled);
        }
    }

    function updatePreview() {
        const templateWithLabels = templateTextarea.value;
        const messageLimits = [160, 320, 480, 640, 800];
        let substitutedPreview = '';

        if (!templateWithLabels.trim()) {
            previewOutput.innerHTML = `
                <div class="preview-placeholder">
                    <div class="muted-text fw-semibold w-100">Live preview of text content goes here.</div>
                </div>
            `;

            if (smsLengthChar) smsLengthChar.textContent = '0 / 160 characters';
            if (charCount) charCount.textContent = '0';
            return;
        }

        let textToSubstitute = templateWithLabels.replace(/[\n\r]/g, ';');

        substitutedPreview = textToSubstitute.replace(readablePlaceholderRegex, (match) => {
            return labelToExampleMap[match] || match;
        });

        const previewLength = substitutedPreview.length;
        if (charCount) charCount.textContent = templateWithLabels.length;

        let limit = Math.ceil(previewLength / 160) * 160;
        limit = Math.min(limit, 800);

        if (smsLengthChar) smsLengthChar.textContent = `${previewLength} / ${limit} characters`;

        let outputHtml = '';
        let currentPos = 0;
        let messageIndex = 1;

        for (const splitLimit of messageLimits) {
            if (currentPos < previewLength) {
                const segmentEnd = Math.min(splitLimit, previewLength);
                let segment = substitutedPreview.substring(currentPos, segmentEnd);

                if (messageIndex > 1) {
                    outputHtml += ``;
                }

                segment = segment.replace(/;/g, '<br>');
                outputHtml += segment;
                currentPos = splitLimit;
                messageIndex++;
            }
        }

        previewOutput.innerHTML = outputHtml;
    }

    function updateTitleCount() {
        if (titleInput && titleCount) {
            titleCount.textContent = titleInput.value.length;
        }
    }

    function displayValidationErrors(errors) {
        clearValidationErrors();

        if (errors.msg_tmp_title) {
            titleErrorDiv.textContent = errors.msg_tmp_title[0];
            titleErrorDiv.style.display = 'block';
            titleInput.classList.add('is-invalid');
        }

        if (errors.msg_tmp_text) {
            textErrorDiv.textContent = errors.msg_tmp_text[0];
            textErrorDiv.style.display = 'block';
            templateTextarea.classList.add('is-invalid');
        }
    }

    function clearValidationErrors() {
        titleErrorDiv.style.display = 'none';
        textErrorDiv.style.display = 'none';
        titleInput.classList.remove('is-invalid');
        templateTextarea.classList.remove('is-invalid');
    }

    function handleSubmission(e) {
        e.preventDefault();
        clearValidationErrors();

        const backendText = history[historyIndex] || '';
        const title = titleInput.value;

        const hiddenTextInput = form.querySelector('input[name="msg_tmp_text_hidden"]');
        if (!hiddenTextInput) {
            const newHiddenInput = document.createElement('input');
            newHiddenInput.type = 'hidden';
            newHiddenInput.name = 'msg_tmp_text_hidden';
            newHiddenInput.id = 'msg_tmp_text_hidden';
            form.appendChild(newHiddenInput);
        }

        form.querySelector('input[name="msg_tmp_text_hidden"]').value = backendText;
        titleInput.value = title;

        form.submit();
    }

    function insertPlaceholder(key, label) {
        const readablePlaceholder = `[${label}]`;

        if (!readablePlaceholder) return;

        const start = templateTextarea.selectionStart;
        const end = templateTextarea.selectionEnd;
        const text = templateTextarea.value;

        if (text.length + readablePlaceholder.length > 1000) {
            return;
        }

        const newText = text.substring(0, start) + readablePlaceholder + text.substring(end);
        templateTextarea.value = newText;

        const newCursorPosition = start + readablePlaceholder.length;

        templateTextarea.setSelectionRange(
            newCursorPosition,
            newCursorPosition
        );

        saveState();
        updatePreview();
        templateTextarea.focus();
        clearValidationErrors();

        const btn = Array.from(placeholderButtons).find(
            (b) => b.getAttribute("data-key") === key && b.getAttribute("data-label") === label
        );

        if (btn) {
            let originalClass = "";

            if (btn.classList.contains("btn-outline-primary")) {
                originalClass = "btn-outline-primary";
            } else if (btn.classList.contains("btn-outline-success")) {
                originalClass = "btn-outline-success";
            } else if (btn.classList.contains("btn-outline-info")) {
                originalClass = "btn-outline-info";
            }

            btn.classList.remove(
                "btn-outline-primary",
                "btn-outline-success",
                "btn-outline-info"
            );

            btn.classList.add("btn-success", "placeholder-flash");

            setTimeout(() => {
                btn.classList.remove("btn-success", "placeholder-flash");

                if (originalClass) {
                    btn.classList.add(originalClass);
                }

                btn.blur();
            }, 500);
        }
    }

    if (placeholderButtons.length > 0) {
        placeholderButtons.forEach(button => {
            button.addEventListener('click', function () {
                const key = this.getAttribute('data-key');
                const label = this.getAttribute('data-label');
                insertPlaceholder(key, label);
            });
        });
    }

    if (templateTextarea) {
        templateTextarea.addEventListener('input', function () {
            if (this.value.length > 1000) {
                this.value = this.value.substring(0, 1000);
            }

            if (this.timeout) clearTimeout(this.timeout);
            this.timeout = setTimeout(saveState, 500);

            updatePreview();
            clearValidationErrors();
        });
    }

    if (titleInput) {
        titleInput.addEventListener('input', function () {
            if (this.value.length > 30) {
                this.value = this.value.substring(0, 30);
            }

            updateTitleCount();
            clearValidationErrors();
        });
    }

    if (undoButton) {
        undoButton.addEventListener('click', undo);
    }

    if (redoButton) {
        redoButton.addEventListener('click', redo);
    }

    if (addSmsTmpBtn) {
        addSmsTmpBtn.addEventListener('click', handleSubmission);
    }

    if (updateSmsTmpBtn) {
        updateSmsTmpBtn.addEventListener('click', handleSubmission);
    }

    if (isEditPage) {
        const initialText = initialMsgText.value;

        if (initialText) {
            history.push(initialText);
            historyIndex = 0;
            loadState(initialText);
        } else {
            saveState();
            loadState(history[historyIndex] || '');
        }
    } else {
        saveState();
        loadState(history[historyIndex] || '');
    }

    updatePreview();
    updateTitleCount();
});
