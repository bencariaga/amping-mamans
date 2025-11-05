document.addEventListener("DOMContentLoaded", function () {
    const editableSection = document.getElementById("editable-letter-section");
    const glContentInput = document.getElementById("gl_content");
    const signersInput = document.getElementById("signers");
    const titleInput = document.getElementById("gl_tmp_title");
    const useProgramHeadCheckbox = document.getElementById("use-program-head");
    const placeholderButtons = document.querySelectorAll(".placeholder-btn");
    const formatButtons = document.querySelectorAll(".format-btn");
    const form = document.getElementById("gl-template-form");
    const titleErrorDiv = document.getElementById("title-error");
    const addGlTmpBtn = document.getElementById("addGlTmpBtn");
    const updateGlTmpBtn = document.getElementById("updateGlTmpBtn");

    const signer1First = document.getElementById("signer-1-first");
    const signer1Middle = document.getElementById("signer-1-middle");
    const signer1Last = document.getElementById("signer-1-last");
    const signer1Suffix = document.getElementById("signer-1-suffix");
    const signer1Pnl = document.getElementById("signer-1-pnl");

    const signer2First = document.getElementById("signer-2-first");
    const signer2Middle = document.getElementById("signer-2-middle");
    const signer2Last = document.getElementById("signer-2-last");
    const signer2Suffix = document.getElementById("signer-2-suffix");
    const signer2Pnl = document.getElementById("signer-2-pnl");

    const initialGlContent = document.getElementById("initial-gl-content");
    const initialSigners = document.getElementById("initial-signers");
    const oldGlContent = document.getElementById("old-gl-content");
    const oldSigners = document.getElementById("old-signers");
    const existingSignature1 = document.getElementById("existing-signature-1");
    const existingSignature2 = document.getElementById("existing-signature-2");
    const programHeadDataElement = document.getElementById("program-head-data");

    const isEditPage = initialGlContent !== null;
    const undoButton = document.querySelector('.format-btn[data-label="Undo"]');
    const redoButton = document.querySelector('.format-btn[data-label="Redo"]');

    if (!editableSection || !glContentInput || !signersInput || !form) return;

    let history = [];
    let historyIndex = -1;
    const historyLimit = 50;

    let programHeadData = {
        first: "",
        middle: "",
        last: "",
        suffix: "",
        pnl: "MMPA"
    };

    if (programHeadDataElement) {
        try {
            const data = JSON.parse(programHeadDataElement.value);
            programHeadData = {
                first: data.first_name || "",
                middle: data.middle_name || "",
                last: data.last_name || "",
                suffix: data.suffix || "",
                pnl: "MMPA"
            };
        } catch (e) {
            console.error("Error parsing program head data:", e);
        }
    }

    const applicationDate = new Date();
    const reapplicationDate = new Date();
    reapplicationDate.setDate(applicationDate.getDate() + 90);
    const dateOptions = { month: 'long', day: 'numeric', year: 'numeric' };

    const applicationDateFormatted = applicationDate.toLocaleDateString('en-US', dateOptions);
    const reapplicationDateFormatted = reapplicationDate.toLocaleDateString('en-US', dateOptions);

    const exampleData = {
        "application->applicant->client->member->first_name": "Elizabeth",
        "application->applicant->client->member->middle_name": "Alexandra",
        "application->applicant->client->member->last_name": "Mary",
        "application->applicant->client->member->suffix": "II",
        "application->patient->client->member->first_name": "Juan",
        "application->patient->client->member->middle_name": "Cruz",
        "application->patient->client->member->last_name": "Santos",
        "application->patient->client->member->suffix": "Jr.",
        "application->service": "Hospital Bill",
        "application->affiliate_partner->affiliate_partner_name": "St. Elizabeth Hospital, Inc.",
        "application->billed_amount": "100,000",
        "application->assistance_amount": "80,000",
        "application->assistance_amount_spelled_out": "EIGHTY THOUSAND",
        "application->application_date": applicationDateFormatted,
        "application->reapplication_date": reapplicationDateFormatted,
        "application->applicant->barangay": "Labangal"
    };

    const labelMap = {
        "[$application->applicant->client->member->first_name]": "[Applicant's First Name]",
        "[$application->applicant->client->member->middle_name]": "[Applicant's Middle Initial]",
        "[$application->applicant->client->member->last_name]": "[Applicant's Last Name]",
        "[$application->applicant->client->member->suffix]": "[Applicant's Suffix Name]",
        "[$application->patient->client->member->first_name]": "[Patient's First Name]",
        "[$application->patient->client->member->middle_name]": "[Patient's Middle Initial]",
        "[$application->patient->client->member->last_name]": "[Patient's Last Name]",
        "[$application->patient->client->member->suffix]": "[Patient's Suffix Name]",
        "[$application->service]": "[Service]",
        "[$application->affiliate_partner->affiliate_partner_name]": "[Affiliate Partner]",
        "[$application->billed_amount]": "[Billed Amount]",
        "[$application->assistance_amount]": "[Assistance Amount]",
        "[$application->assistance_amount_spelled_out]": "[Assistance Amount (Spelled Out)]",
        "[$application->application_date]": "[Application Date]",
        "[$application->reapplication_date]": "[Reapplication Date]",
        "[$application->applicant->barangay]": "[Barangay]"
    };

    const backendMap = Object.fromEntries(Object.entries(labelMap).map(([key, value]) => [value, key]));
    const readablePlaceholderRegex = new RegExp(Object.keys(backendMap).map(label => label.replace(/[\[\]]/g, '\\$&')).join('|'), 'g');

    const labelToExampleMap = {
        "[Applicant's First Name]": exampleData["application->applicant->client->member->first_name"],
        "[Applicant's Middle Initial]": exampleData["application->applicant->client->member->middle_name"].charAt(0) + ".",
        "[Applicant's Last Name]": exampleData["application->applicant->client->member->last_name"],
        "[Applicant's Suffix Name]": exampleData["application->applicant->client->member->suffix"],
        "[Patient's First Name]": exampleData["application->patient->client->member->first_name"],
        "[Patient's Middle Initial]": exampleData["application->patient->client->member->middle_name"].charAt(0) + ".",
        "[Patient's Last Name]": exampleData["application->patient->client->member->last_name"],
        "[Patient's Suffix Name]": exampleData["application->patient->client->member->suffix"],
        "[Service]": exampleData["application->service"],
        "[Affiliate Partner]": exampleData["application->affiliate_partner->affiliate_partner_name"],
        "[Billed Amount]": exampleData["application->billed_amount"],
        "[Assistance Amount]": exampleData["application->assistance_amount"],
        "[Assistance Amount (Spelled Out)]": exampleData["application->assistance_amount_spelled_out"],
        "[Application Date]": exampleData["application->application_date"],
        "[Reapplication Date]": exampleData["application->reapplication_date"],
        "[Barangay]": exampleData["application->applicant->barangay"]
    };

    function saveState() {
        const currentContent = editableSection.innerHTML;
        if (history.length > 0 && history[historyIndex] === currentContent) return;

        if (historyIndex < history.length - 1) {
            history = history.slice(0, historyIndex + 1);
        }

        if (history.length >= historyLimit) {
            history.shift();
        } else {
            historyIndex++;
        }

        history[historyIndex] = currentContent;
        updateUndoRedoButtons();
    }

    function loadState(content) {
        editableSection.innerHTML = content;
        updateGlContent();
        updateUndoRedoButtons();
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
        if (!undoButton || !redoButton) return;

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

    function setupSignatureUploader(boxId, fileInputId, imgId, plusId, existingPath) {
        const signatureBox = document.getElementById(boxId);
        const fileInput = document.getElementById(fileInputId);
        const signatureImage = document.getElementById(imgId);
        const signaturePlus = document.getElementById(plusId);
        const signatureCircle = signatureBox.querySelector('.signature-rectangle');

        if (!signatureBox || !fileInput || !signatureImage || !signaturePlus || !signatureCircle) {
            return;
        }

        if (existingPath && existingPath.trim() !== '') {
            signatureImage.src = '/' + existingPath;
            signatureCircle.classList.add('has-signature');
        }

        signatureBox.addEventListener("click", () => {
            fileInput.click();
        });

        fileInput.addEventListener("change", (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    signatureImage.src = e.target.result;
                    signatureCircle.classList.add('has-signature');
                };

                reader.readAsDataURL(file);
            }
        });
    }

    function updateSignersData() {
        const signersData = {
            signer1: {
                first: signer1First.value.trim(),
                middle: signer1Middle.value.trim(),
                last: signer1Last.value.trim(),
                suffix: signer1Suffix.value.trim(),
                pnl: signer1Pnl.value.trim()
            },
            signer2: {
                first: signer2First.value.trim(),
                middle: signer2Middle.value.trim(),
                last: signer2Last.value.trim(),
                suffix: signer2Suffix.value.trim(),
                pnl: signer2Pnl.value.trim()
            }
        };

        signersInput.value = JSON.stringify(signersData);
    }

    function fillProgramHead() {
        if (useProgramHeadCheckbox.checked) {
            signer2First.value = programHeadData.first;
            signer2Middle.value = programHeadData.middle;
            signer2Last.value = programHeadData.last;
            signer2Suffix.value = programHeadData.suffix;
            signer2Pnl.value = programHeadData.pnl;

            signer2First.disabled = true;
            signer2Middle.disabled = true;
            signer2Last.disabled = true;
            signer2Suffix.disabled = true;
            signer2Pnl.disabled = true;
        } else {
            signer2First.value = "";
            signer2Middle.value = "";
            signer2Last.value = "";
            signer2Suffix.value = "";
            signer2Pnl.value = "";

            signer2First.disabled = false;
            signer2Middle.disabled = false;
            signer2Last.disabled = false;
            signer2Suffix.disabled = false;
            signer2Pnl.disabled = false;
        }
        updateSignersData();
    }

    function updateGlContent() {
        if (editableSection) {
            glContentInput.value = editableSection.innerHTML;
        }
    }

    function updatePreview() {
        if (!editableSection) return;
        const contentWithPlaceholders = editableSection.innerHTML;
        let previewContent = contentWithPlaceholders;

        Object.keys(labelToExampleMap).forEach(placeholder => {
            const exampleValue = labelToExampleMap[placeholder];
            const escapedPlaceholder = placeholder.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(escapedPlaceholder, 'g');
            previewContent = previewContent.replace(regex, exampleValue);
        });
    }

    function insertPlaceholderIntoEditable(label) {
        const readablePlaceholder = `[${label}]`;

        if (!editableSection) return;

        editableSection.focus();

        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();

            const textNode = document.createTextNode(readablePlaceholder);
            range.insertNode(textNode);

            range.setStartAfter(textNode);
            range.setEndAfter(textNode);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            editableSection.innerHTML += readablePlaceholder;
        }

        updateGlContent();
        updatePreview();
    }

    function insertTab() {
        if (!editableSection) return;

        editableSection.focus();
        const tabSpace = "&emsp;&emsp;&emsp;";

        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = tabSpace;
            const nodeToInsert = tempDiv.firstChild;

            range.insertNode(nodeToInsert);

            range.setStartAfter(nodeToInsert);
            range.setEndAfter(nodeToInsert);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            editableSection.innerHTML += tabSpace;
        }

        updateGlContent();
    }

    function insertPesoSign() {
        if (!editableSection) return;

        editableSection.focus();
        const pesoSign = "₱";

        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();

            const tempDiv = document.createElement("div");
            tempDiv.innerHTML = pesoSign;
            const nodeToInsert = tempDiv.firstChild;

            range.insertNode(nodeToInsert);

            range.setStartAfter(nodeToInsert);
            range.setEndAfter(nodeToInsert);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            editableSection.innerHTML += pesoSign;
        }

        updateGlContent();
    }

    function applyFormat(command) {
        if (!editableSection) return;

        editableSection.focus();

        if (command === "insertTab") {
            insertTab();
        } else if (command === "pesoSign") {
            insertPesoSign();
        } else {
            document.execCommand(command, false, null);
            updateGlContent();
            updateFormatButtons();
        }
    }

    function updateFormatButtons() {
        formatButtons.forEach(button => {
            const command = button.getAttribute('data-command');

            if (['bold', 'italic', 'underline', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'].includes(command)) {
                const isActive = document.queryCommandState(command);

                if (isActive) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            }
        });
    }

    function insertPlaceholderIntoEditable(label) {
        const readablePlaceholder = `[${label}]`;

        if (!editableSection) return;

        editableSection.focus();

        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();

            const textNode = document.createTextNode(readablePlaceholder);
            range.insertNode(textNode);

            range.setStartAfter(textNode);
            range.setEndAfter(textNode);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            editableSection.innerHTML += readablePlaceholder;
        }

        updateGlContent();
    }

    if (editableSection) {
        const contentSource = (isEditPage && initialGlContent && initialGlContent.value) ? initialGlContent.value : (oldGlContent && oldGlContent.value ? oldGlContent.value : null);
        
        if (contentSource) {
            editableSection.innerHTML = contentSource;
        }

        saveState();

        editableSection.addEventListener("input", function() {
            updateGlContent();
            updatePreview();
            saveState();
        });

        editableSection.addEventListener("blur", function() {
            updateGlContent();
            updatePreview();
        });

        editableSection.addEventListener("mouseup", updateFormatButtons);
        editableSection.addEventListener("keyup", function(e) {
            updateFormatButtons();

            if (e.key === 'Tab') {
                e.preventDefault();
                insertTab();
                saveState();
            }
        });

        editableSection.addEventListener("keydown", function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
            }

            if (e.ctrlKey && e.key === 'z') {
                e.preventDefault();
                undo();
            }

            if (e.ctrlKey && e.key === 'y') {
                e.preventDefault();
                redo();
            }
        });

        updatePreview();
    }

    if (formatButtons.length > 0) {
        formatButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const command = this.getAttribute('data-command');
                const label = this.getAttribute('data-label');

                if (label === 'Undo') {
                    undo();
                } else if (label === 'Redo') {
                    redo();
                } else {
                    applyFormat(command);
                    saveState();
                }
            });
        });
    }

    if (placeholderButtons.length > 0) {
        placeholderButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const label = this.getAttribute('data-label');
                insertPlaceholderIntoEditable(label);
                saveState();
            });
        });
    }

    if (useProgramHeadCheckbox) {
        useProgramHeadCheckbox.addEventListener("change", fillProgramHead);
        fillProgramHead();
    }

    [signer1First, signer1Middle, signer1Last, signer1Suffix, signer1Pnl, signer2First, signer2Middle, signer2Last, signer2Suffix, signer2Pnl].forEach(input => {
        if (input) {
            input.addEventListener("input", updateSignersData);
        }
    });

    const signersSource = (isEditPage && initialSigners && initialSigners.value) ? initialSigners.value : (oldSigners && oldSigners.value ? oldSigners.value : null);
    
    if (signersSource) {
        try {
            const signersData = JSON.parse(signersSource);
            if (signersData.signer1) {
                signer1First.value = signersData.signer1.first || "";
                signer1Middle.value = signersData.signer1.middle || "";
                signer1Last.value = signersData.signer1.last || "";
                signer1Suffix.value = signersData.signer1.suffix || "";
                signer1Pnl.value = signersData.signer1.pnl || "";
            }
            if (signersData.signer2) {
                signer2First.value = signersData.signer2.first || "";
                signer2Middle.value = signersData.signer2.middle || "";
                signer2Last.value = signersData.signer2.last || "";
                signer2Suffix.value = signersData.signer2.suffix || "";
                signer2Pnl.value = signersData.signer2.pnl || "";

                if (useProgramHeadCheckbox && programHeadData.first && programHeadData.last) {
                    const isProgramHead = 
                        signersData.signer2.first === programHeadData.first &&
                        signersData.signer2.middle === programHeadData.middle &&
                        signersData.signer2.last === programHeadData.last &&
                        signersData.signer2.suffix === programHeadData.suffix;
                    
                    if (isProgramHead) {
                        useProgramHeadCheckbox.checked = true;
                        signer2First.disabled = true;
                        signer2Middle.disabled = true;
                        signer2Last.disabled = true;
                        signer2Suffix.disabled = true;
                        signer2Pnl.disabled = true;
                    }
                }
            }
        } catch (e) {
            console.error("Error parsing signers data:", e);
        }
    }

    const existingSig1 = existingSignature1 ? existingSignature1.value : '';
    const existingSig2 = existingSignature2 ? existingSignature2.value : '';

    setupSignatureUploader("signature-box-1", "signature-file-1", "sig-img-1", "sig-plus-1", existingSig1);
    setupSignatureUploader("signature-box-2", "signature-file-2", "sig-img-2", "sig-plus-2", existingSig2);

    updateGlContent();
    updateSignersData();

    function setupPreviewToggle() {
        const togglePreviewBtn = document.getElementById('toggle-preview');
        const previewSection = document.querySelector('.preview-section');
        const editableSection = document.getElementById('editable-letter-section');

        if (!togglePreviewBtn || !previewSection) return;

        togglePreviewBtn.addEventListener('click', function() {
            if (previewSection.style.display === 'none') {
                previewSection.style.display = 'block';
                editableSection.style.display = 'none';
                togglePreviewBtn.innerHTML = '<i class="fas fa-edit"></i> Show Editor';

                updateFullPreview();
            } else {
                previewSection.style.display = 'none';
                editableSection.style.display = 'block';
                togglePreviewBtn.innerHTML = '<i class="fas fa-eye"></i> Toggle Preview';
            }
        });
    }

    function updateFullPreview() {
        const previewLetterSection = document.getElementById('preview-letter-section');
        if (!previewLetterSection) return;

        const contentWithPlaceholders = editableSection.innerHTML;
        let previewContent = contentWithPlaceholders;

        Object.keys(labelToExampleMap).forEach(placeholder => {
            const exampleValue = labelToExampleMap[placeholder];
            const escapedPlaceholder = placeholder.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(escapedPlaceholder, 'g');
            previewContent = previewContent.replace(regex, exampleValue);
        });

        previewLetterSection.innerHTML = previewContent;
    }

    setupPreviewToggle();

    function clearValidationErrors() {
        if (titleErrorDiv) {
            titleErrorDiv.style.display = 'none';
            titleErrorDiv.textContent = '';
        }
        if (titleInput) {
            titleInput.classList.remove('is-invalid');
        }
    }

    function handleSubmission(e) {
        e.preventDefault();
        clearValidationErrors();

        updateGlContent();
        updateSignersData();

        const title = titleInput ? titleInput.value.trim() : '';
        const content = glContentInput ? glContentInput.value : '';
        const signers = signersInput ? signersInput.value : '';

        if (!title) {
            if (titleErrorDiv) {
                titleErrorDiv.textContent = 'The gl tmp title field is required.';
                titleErrorDiv.style.display = 'block';
            }
            if (titleInput) {
                titleInput.classList.add('is-invalid');
            }
            return;
        }

        if (!content) {
            alert('Please add content to the guarantee letter.');
            return;
        }

        form.submit();
    }

    if (titleInput) {
        titleInput.addEventListener('input', function () {
            if (this.value.length > 30) {
                this.value = this.value.substring(0, 30);
            }
            clearValidationErrors();
        });
    }

    if (addGlTmpBtn) {
        addGlTmpBtn.addEventListener('click', handleSubmission);
    }

    if (updateGlTmpBtn) {
        updateGlTmpBtn.addEventListener('click', handleSubmission);
    }
});
