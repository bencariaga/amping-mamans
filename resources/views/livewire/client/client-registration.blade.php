<div>
    <form wire:submit="save" class="profile-section" id="profileSection">
        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fas fa-user fa-fw"></i><span class="header-title">APPLICANT'S NAME</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="first_name" class="form-control" id="applicantFirstNameInput" placeholder="Example: Juan">
                    @error('first_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Middle Name</label>
                    <input type="text" wire:model.live="middle_name" class="form-control" id="applicantMiddleNameInput" placeholder="Example: Pablo">
                    @error('middle_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="last_name" class="form-control" id="applicantLastNameInput" placeholder="Example: Dela Cruz">
                    @error('last_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
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

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Sex <span class="required-asterisk">*</span></label>
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
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Birthdate <span class="required-asterisk">*</span></label>
                    <input type="date" wire:model.live="birth_date" class="form-control" id="applicantBirthdateInput">
                    @error('birth_date') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Phone Number <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="phone_number" class="form-control" id="applicantPhoneNumberInput" placeholder="Format: &quot;09...&quot; or &quot;+639...&quot;" maxlength="15" inputmode="tel">
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
                    <i class="fa fa-briefcase fa-fw"></i><span class="header-title">WORK INFORMATION</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Job Status <span class="required-asterisk">*</span></label>
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
                                    Remove custom Occup. text
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
                    <input type="text" wire:model.live="custom_occupation" class="form-control" id="applicantCustomOccupationInput" placeholder="{{ $occupation_id ? 'Unselect in Occupation' : 'If none in existing occupations' }}" @if($occupation_id) disabled @endif>
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label">Monthly Income <span class="fw-normal">(in ₱, if applicable)</span></label>
                    <input type="text" id="monthlyIncomeDisplayInput" class="form-control" placeholder="0" inputmode="numeric" wire:ignore>
                    <input type="hidden" name="monthly_income" id="monthlyIncomeHiddenInput" wire:model="monthly_income">
                    @error('monthly_income') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fa-solid fa-file-medical fa-fw"></i><span class="header-title">MEDICAL INFORMATION</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Representing Patient <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantRepresentingPatientDropdownBtn">
                            {{ $representing_patient ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item @if($representing_patient == '') active @endif" href="#" wire:click.prevent="setRepresentingPatient('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($representing_patient == 'Self') active @endif" href="#" wire:click.prevent="setRepresentingPatient('Self')">Self</a></li>
                            <li><a class="dropdown-item @if($representing_patient == 'Other Individual') active @endif" href="#" wire:click.prevent="setRepresentingPatient('Other Individual')">Other Individual</a></li>
                        </ul>
                    </div>
                    @error('representing_patient') <span class="text-danger mt-3">{{ $message }}</span> @enderror
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
                            <li><a class="dropdown-item @if($phic_category == 'Sponsored') active @endif" href="#" wire:click.prevent="setPhicCategory('Sponsored')">Sponsored</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Employed') active @endif" href="#" wire:click.prevent="setPhicCategory('Employed')">Employed</a></li>
                        </ul>
                    </div>
                    @error('phic_category') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <span style="line-height: 1.8;"><b>NOTE: </b>PhilHealth is also known as the Philippine Health Insurance Corporation (PHIC)</span>
                </div>

                @if($representing_patient !== '')
                    <div class="patient-profile-section mb-4">
                        <hr class="pt-2 pb-2">
                        <legend class="form-legend">
                            <i class="fas fa-hospital-user fa-fw"></i><span class="header-title">PATIENT'S NAME</span>
                        </legend>

                        <div class="row gx-3 gy-3">
                            <div class="form-group col-md-3">
                                <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                                <input type="text" wire:model.live="patients.1.first_name" class="form-control" id="patientFirstNameInput-1" placeholder="@if($representing_patient === 'Self') Example: Juan @else Example: Maria @endif" @if($representing_patient === 'Self') readonly @endif>
                                @error('patients.1.first_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label fw-bold">Middle Name</label>
                                <input type="text" wire:model.live="patients.1.middle_name" class="form-control" id="patientMiddleNameInput-1" placeholder="@if($representing_patient === 'Self') Example: Pablo @else Example: Clara @endif" @if($representing_patient === 'Self') readonly @endif>
                                @error('patients.1.middle_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                                <input type="text" wire:model.live="patients.1.last_name" class="form-control" id="patientLastNameInput-1" placeholder="@if($representing_patient === 'Self') Example: Dela Cruz @else Example: Delos Santos @endif" @if($representing_patient === 'Self') readonly @endif>
                                @error('patients.1.last_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label fw-bold">Suffix</label>
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSuffixDropdownBtn-1"
                                            @if($representing_patient === 'Self') disabled @endif>
                                        {{ ($patients[1]['suffix'] ?? '') ?: '— Select —' }}
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == '') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, '')">— Select —</a></li>
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == 'Sr.') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, 'Sr.')">Sr.</a></li>
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == 'Jr.') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, 'Jr.')">Jr.</a></li>
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == 'II') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, 'II')">II</a></li>
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == 'III') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, 'III')">III</a></li>
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == 'IV') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, 'IV')">IV</a></li>
                                        <li><a class="dropdown-item @if(($patients[1]['suffix'] ?? '') == 'V') active @endif" href="#" wire:click.prevent="setPatientSuffix(1, 'V')">V</a></li>
                                    </ul>
                                </div>
                                @error('patients.1.suffix') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                            </div>

                            @if($representing_patient === 'Other Individual')
                                @php
                                    $appFull = trim("{$first_name} {$middle_name} {$last_name} {$suffix}");
                                    $patientFirst = $patients[1]['first_name'] ?? '';
                                    $patientMiddle = $patients[1]['middle_name'] ?? '';
                                    $patientLast = $patients[1]['last_name'] ?? '';
                                    $patientSuffix = $patients[1]['suffix'] ?? '';
                                    $patientFull = trim("{$patientFirst} {$patientMiddle} {$patientLast} {$patientSuffix}");
                                @endphp

                                @if($appFull === $patientFull && $patientFull !== '')
                                    <div class="col-12">
                                        <span class="text-danger mt-3">The patient's name must not be the same as the applicant's name.</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @include('components.layouts.footer.add-applicant')
    </form>
</div>
