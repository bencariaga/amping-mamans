<div>
    <form wire:submit="save" class="profile-section" id="profileSection">
        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fas fa-user fa-fw"></i><span class="header-title">APPLICANT'S NAME</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="last_name" class="form-control" id="applicantLastNameInput" placeholder="Example: Dela Cruz">
                    @error('last_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="first_name" class="form-control" id="applicantFirstNameInput" placeholder="Example: Juan">
                    @error('first_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Middle Name / Initial</label>
                    <input type="text" wire:model.live="middle_name" class="form-control" id="applicantMiddleNameInput" placeholder="Example: Pablo / P.">
                    @error('middle_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Suffix</label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantSuffixDropdownBtn">
                            {{ $suffix ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($suffix == '') active @endif" href="#" wire:click.prevent="setSuffix('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($suffix == 'Sr.') active @endif" href="#" wire:click.prevent="setSuffix('Sr.')">Sr.</a></li>
                            <li><a class="dropdown-item @if($suffix == 'Jr.') active @endif" href="#" wire:click.prevent="setSuffix('Jr.')">Jr.</a></li>
                            <li><a class="dropdown-item @if($suffix == 'II') active @endif" href="#" wire:click.prevent="setSuffix('II')">II</a></li>
                            <li><a class="dropdown-item @if($suffix == 'III') active @endif" href="#" wire:click.prevent="setSuffix('III')">III</a></li>
                            <li><a class="dropdown-item @if($suffix == 'IV') active @endif" href="#" wire:click.prevent="setSuffix('IV')">IV</a></li>
                            <li><a class="dropdown-item @if($suffix == 'V') active @endif" href="#" wire:click.prevent="setSuffix('V')">V</a></li>
                        </ul>
                    </div>
                    @error('suffix') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fa fa-info-circle fa-fw"></i><span class="header-title">PERSONAL INFORMATION</span>
                </legend>

                <div class="form-group col-md-2">
                    <label class="form-label fw-bold">Gender / Sex <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantSexDropdownBtn">
                            {{ $sex ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($sex == '') active @endif" href="#" wire:click.prevent="setSex('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($sex == 'Male') active @endif" href="#" wire:click.prevent="setSex('Male')">Male</a></li>
                            <li><a class="dropdown-item @if($sex == 'Female') active @endif" href="#" wire:click.prevent="setSex('Female')">Female</a></li>
                        </ul>
                    </div>
                    @error('sex') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label fw-bold">Birthdate <span class="required-asterisk">*</span></label>
                    <input type="date" wire:model.live="birth_date" class="form-control" id="applicantBirthdateInput">
                    @error('birth_date') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label fw-bold">Age (Read-Only)</label>
                    <input type="text" class="form-control" id="applicantAgeInput" readonly>
                    <input type="hidden" id="applicantAgeHidden" wire:model="applicant_age">
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Phone Number <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="phone_number" class="form-control" id="applicantPhoneNumberInput" placeholder="&quot;09...&quot; or &quot;+639...&quot;" maxlength="15" inputmode="tel">
                    @error('phone_number') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Civil Status <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantCivilStatusDropdownBtn">
                            {{ $civil_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($civil_status == '') active @endif" href="#" wire:click.prevent="setCivilStatus('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($civil_status == 'Single') active @endif" href="#" wire:click.prevent="setCivilStatus('Single')">Single</a></li>
                            <li><a class="dropdown-item @if($civil_status == 'Married') active @endif" href="#" wire:click.prevent="setCivilStatus('Married')">Married</a></li>
                            <li><a class="dropdown-item @if($civil_status == 'Separated') active @endif" href="#" wire:click.prevent="setCivilStatus('Separated')">Separated</a></li>
                            <li><a class="dropdown-item @if($civil_status == 'Widowed') active @endif" href="#" wire:click.prevent="setCivilStatus('Widowed')">Widowed</a></li>
                        </ul>
                    </div>
                    @error('civil_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fa fa-briefcase fa-fw"></i><span class="header-title">WORK INFORMATION</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Job Status <span class="fw-normal">(if applicable)</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantJobStatusDropdownBtn">
                            {{ $job_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($job_status == '') active @endif" href="#" wire:click.prevent="setJobStatus('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($job_status == 'Permanent') active @endif" href="#" wire:click.prevent="setJobStatus('Permanent')">Permanent</a></li>
                            <li><a class="dropdown-item @if($job_status == 'Contractual') active @endif" href="#" wire:click.prevent="setJobStatus('Contractual')">Contractual</a></li>
                            <li><a class="dropdown-item @if($job_status == 'Casual') active @endif" href="#" wire:click.prevent="setJobStatus('Casual')">Casual</a></li>
                        </ul>
                    </div>
                    @error('job_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label">Occupation <span class="fw-normal">(if applicable)</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantOccupationDropdownBtn" @if($custom_occupation) disabled @endif>
                            @if($occupation_id && $occupations->find($occupation_id))
                                {{ $occupations->find($occupation_id)->occupation }}
                            @else
                                @if($custom_occupation)
                                    Remove Occupation text.
                                @else
                                    — Select —
                                @endif
                            @endif
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($occupation_id == '') active @endif" href="#" wire:click.prevent="setOccupation('')">— Select —</a></li>
                            @foreach($occupations as $occupation)
                                <li>
                                    <a class="dropdown-item @if($occupation_id == $occupation->occupation_id) active @endif" href="#" wire:click.prevent="setOccupation('{{ $occupation->occupation_id }}')">{{ $occupation->occupation }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label">Occupation <span class="fw-normal">(if other, please specify)</span></label>
                    <input type="text" wire:model.live="custom_occupation" class="form-control" id="applicantCustomOccupationInput" placeholder="{{ $occupation_id ? 'Unselect in Occupation.' : 'If none in existing occupations.' }}" @if($occupation_id) disabled @endif>
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label">Monthly Income <span class="fw-normal">(in ₱)</span></label>
                    <input type="text" id="monthlyIncomeDisplayInput" class="form-control" placeholder="0" inputmode="numeric" maxlength="7" wire:ignore>
                    <input type="hidden" name="monthly_income" id="monthlyIncomeHiddenInput" wire:model="monthly_income">
                    @error('monthly_income') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fa fa-home fa-fw"></i><span class="header-title">HOME ADDRESS</span>
                </legend>

                <div class="form-group col-md-2">
                    <label class="form-label">House # <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="house_number" class="form-control" id="applicantHouseNumberInput" placeholder="Either room or lot #">
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label">Block # <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="block_number" class="form-control" id="applicantBlockNumberInput" placeholder="Ex: Block 1">
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label">Phase <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="phase" class="form-control" id="applicantPhaseInput" placeholder="Ex: Phase 1-A">
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label">Street <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="street" class="form-control" id="applicantStreetInput" placeholder="Ex: Matalam St.">
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label">Sitio <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="sitio" class="form-control" id="applicantSitioInput" placeholder="Ex: Sitio Corazon">
                </div>
                <div class="form-group col-md-2">
                    <label class="form-label">Purok <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="purok" class="form-control" id="applicantPurokInput" placeholder="Ex: Purok Maunlad">
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label">Subdivision <span class="fw-normal">(if applicable)</span></label>
                    <input type="text" wire:model.live="subdivision" class="form-control" id="applicantSubdivisionInput" placeholder="Ex: Doña Soledad">
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label">Barangay <span class="fw-normal">(if applicable)</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantBarangayDropdownBtn">
                            {{ $barangay ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($barangay == '') active @endif" href="#" wire:click.prevent="setBarangay('')">— Select —</a></li>
                            @foreach(['Apopong','Baluan','Batomelong','Buayan','Bula','Calumpang','City Heights','Conel','Dadiangas East','Dadiangas North','Dadiangas South','Dadiangas West','Fatima','Katangawan','Labangal','Lagao','Ligaya','Mabuhay','Olympog','San Isidro','San Jose','Siguel','Sinawal','Tambler','Tinagacan','Upper Labay'] as $b)
                                <li><a class="dropdown-item @if($barangay == $b) active @endif" href="#" wire:click.prevent="setBarangay('{{ $b }}')">{{ $b }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">House Occupancy Status <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantHouseStatusDropdownBtn">
                            {{ $house_occup_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($house_occup_status == '') active @endif" href="#" wire:click.prevent="setHouseOccupStatus('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($house_occup_status == 'Owner') active @endif" href="#" wire:click.prevent="setHouseOccupStatus('Owner')">Owner</a></li>
                            <li><a class="dropdown-item @if($house_occup_status == 'Renter') active @endif" href="#" wire:click.prevent="setHouseOccupStatus('Renter')">Renter</a></li>
                            <li><a class="dropdown-item @if($house_occup_status == 'House Sharer') active @endif" href="#" wire:click.prevent="setHouseOccupStatus('House Sharer')">House Sharer</a></li>
                        </ul>
                    </div>
                    @error('house_occup_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Lot Occupancy Status <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantLotStatusDropdownBtn">
                            {{ $lot_occup_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($lot_occup_status == '') active @endif" href="#" wire:click.prevent="setLotOccupStatus('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($lot_occup_status == 'Owner') active @endif" href="#" wire:click.prevent="setLotOccupStatus('Owner')">Owner</a></li>
                            <li><a class="dropdown-item @if($lot_occup_status == 'Renter') active @endif" href="#" wire:click.prevent="setLotOccupStatus('Renter')">Renter</a></li>
                            <li><a class="dropdown-item @if($lot_occup_status == 'Lot Sharer') active @endif" href="#" wire:click.prevent="setLotOccupStatus('Lot Sharer')">Lot Sharer</a></li>
                            <li><a class="dropdown-item @if($lot_occup_status == 'Informal Settler') active @endif" href="#" wire:click.prevent="setLotOccupStatus('Informal Settler')">Informal Settler</a></li>
                        </ul>
                    </div>
                    @error('lot_occup_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fa-solid fa-file-medical fa-fw"></i><span class="header-title">MEDICAL INFORMATION</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Number of Patients <span class="required-asterisk">*</span></label>
                    <input type="number" wire:model.live="patient_number" class="form-control" id="patientNumberInput" placeholder="1" min="1" max="10" inputmode="numeric">
                    @error('patient_number') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3 d-flex flex-row align-items-end" style="height: 5rem;">
                    <input class="form-check-input" type="checkbox" wire:model.live="include_applicant_as_patient" id="checkbox">
                    <label class="form-check-label ms-3">Include the applicant<br>(oneself) as a patient.</label>
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">PhilHealth Affiliation <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantPhicAffiliationDropdownBtn">
                            {{ $phic_affiliation ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($phic_affiliation == '') active @endif" href="#" wire:click.prevent="setPhicAffiliation('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($phic_affiliation == 'Affiliated') active @endif" href="#" wire:click.prevent="setPhicAffiliation('Affiliated')">Affiliated</a></li>
                            <li><a class="dropdown-item @if($phic_affiliation == 'Unaffiliated') active @endif" href="#" wire:click.prevent="setPhicAffiliation('Unaffiliated')">Unaffiliated</a></li>
                        </ul>
                    </div>
                    @error('phic_affiliation') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">PhilHealth Category</label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantPhicCategoryDropdownBtn" @if($phic_affiliation !== 'Affiliated') disabled @endif>
                            {{ $phic_category ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($phic_category == '') active @endif" href="#" wire:click.prevent="setPhicCategory('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Self-Employed') active @endif" href="#" wire:click.prevent="setPhicCategory('Self-Employed')">Self-Employed</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Sponsored / Indigent') active @endif" href="#" wire:click.prevent="setPhicCategory('Sponsored / Indigent')">Sponsored / Indigent</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Employed') active @endif" href="#" wire:click.prevent="setPhicCategory('Employed')">Employed</a></li>
                        </ul>
                    </div>
                    @error('phic_category') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        @foreach($patients as $index => $patient)
            @php
                $isDisabled = ($index == 1 && $include_applicant_as_patient);
            @endphp

            <div class="profile-container">
                <div class="col-md-12">
                    <div class="row gx-3 gy-3 mb-3">
                        <legend class="form-legend">
                            <i class="fas fa-hospital-user fa-fw"></i><span class="header-title">NAME OF PATIENT {{ $index }}</span>
                        </legend>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                            <input type="text" wire:model.live="patients.{{ $index }}.last_name" class="form-control" id="patientLastNameInput-{{ $index }}" placeholder="Example: Dela Cruz" @if($isDisabled) disabled @endif>
                            @error("patients.{$index}.last_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                            <input type="text" wire:model.live="patients.{{ $index }}.first_name" class="form-control" id="patientFirstNameInput-{{ $index }}" placeholder="Example: Juan" @if($isDisabled) disabled @endif>
                            @error("patients.{$index}.first_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Middle Name / Initial</label>
                            <input type="text" wire:model.live="patients.{{ $index }}.middle_name" class="form-control" id="patientMiddleNameInput-{{ $index }}" placeholder="Example: Pablo / P." @if($isDisabled) disabled @endif>
                            @error("patients.{$index}.middle_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Suffix</label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSuffixDropdownBtn-{{ $index }}" @if($isDisabled) disabled @endif>
                                    {{ $patient['suffix'] ?: '— Select —' }}
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item @if($patient['suffix'] == '') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, '')">— Select —</a></li>
                                    <li><a class="dropdown-item @if($patient['suffix'] == 'Jr.') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, 'Jr.')">Jr.</a></li>
                                    <li><a class="dropdown-item @if($patient['suffix'] == 'Sr.') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, 'Sr.')">Sr.</a></li>
                                    <li><a class="dropdown-item @if($patient['suffix'] == 'II') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, 'II')">II</a></li>
                                    <li><a class="dropdown-item @if($patient['suffix'] == 'III') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, 'III')">III</a></li>
                                    <li><a class="dropdown-item @if($patient['suffix'] == 'IV') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, 'IV')">IV</a></li>
                                    <li><a class="dropdown-item @if($patient['suffix'] == 'V') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $index }}, 'V')">V</a></li>
                                </ul>
                            </div>
                            @error("patients.{$index}.suffix") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="row gx-3 gy-3">
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Gender / Sex <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSexDropdownBtn-{{ $index }}" @if($isDisabled) disabled @endif>
                                    {{ $patient['sex'] ?: '— Select —' }}
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item @if($patient['sex'] == '') active @endif" href="#" wire:click.prevent="setPatientSex({{ $index }}, '')">— Select —</a></li>
                                    <li><a class="dropdown-item @if($patient['sex'] == 'Male') active @endif" href="#" wire:click.prevent="setPatientSex({{ $index }}, 'Male')">Male</a></li>
                                    <li><a class="dropdown-item @if($patient['sex'] == 'Female') active @endif" href="#" wire:click.prevent="setPatientSex({{ $index }}, 'Female')">Female</a></li>
                                </ul>
                            </div>
                            @error("patients.{$index}.sex") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Age <span class="required-asterisk">*</span></label>
                            <input type="number" wire:model.live="patients.{{ $index }}.age" class="form-control patient-age-input" id="patientAgeInput-{{ $index }}" placeholder="0" min="0" max="200" @if($isDisabled) disabled @endif>
                            @error("patients.{$index}.age") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label fw-bold">Category</label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientCategoryDropdownBtn-{{ $index }}">
                                    {{ $patient['patient_category'] ?: '— Select —' }}
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item @if($patient['patient_category'] == '') active @endif" href="#" wire:click.prevent="setPatientCategory({{ $index }}, '')">— Select —</a></li>
                                    <li><a class="dropdown-item @if($patient['patient_category'] == 'PWD') active @endif" href="#" wire:click.prevent="setPatientCategory({{ $index }}, 'PWD')">PWD</a></li>
                                    <li><a class="dropdown-item @if($patient['patient_category'] == 'Senior') active @endif" href="#" wire:click.prevent="setPatientCategory({{ $index }}, 'Senior')">Senior</a></li>
                                </ul>
                            </div>
                            @error("patients.{$index}.patient_category") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @include('components.layouts.footer.add-applicant')
    </form>
</div>
