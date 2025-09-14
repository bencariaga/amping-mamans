<div class="mt-4" x-data="{ open: false }">
    <form wire:submit.prevent="update" class="profile-section" id="profileSection">
        <div class="profile-container">
            <div class="row gx-3 gy-3 mb-3">
                <legend class="form-legend">
                    <i class="fas fa-user fa-fw"></i><span class="header-title">APPLICANT'S NAME</span>
                </legend>

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="first_name" id="applicantFirstNameInput" class="form-control">
                    @error('first_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Middle Name</label>
                    <input type="text" wire:model.live="middle_name" id="applicantMiddleNameInput" class="form-control">
                    @error('middle_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="last_name" id="applicantLastNameInput" class="form-control">
                    @error('last_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Suffix</label>
                    <div class="dropdown">
                        <button id="applicantSuffixDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $suffix ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantSuffixDropdownBtn">
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
                        <button id="applicantSexDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $sex ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantSexDropdownBtn">
                            <li><a class="dropdown-item @if($sex == '') active @endif" href="#" wire:click.prevent="setSex('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($sex == 'Male') active @endif" href="#" wire:click.prevent="setSex('Male')">Male</a></li>
                            <li><a class="dropdown-item @if($sex == 'Female') active @endif" href="#" wire:click.prevent="setSex('Female')">Female</a></li>
                        </ul>
                    </div>
                    @error('sex') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Birthdate <span class="required-asterisk">*</span></label>
                    <input type="date" wire:model.live="birth_date" id="applicantBirthdateInput" class="form-control">
                    @error('birth_date') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Phone Number <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="phone_number" id="applicantPhoneNumberInput" class="form-control">
                    @error('phone_number') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Civil Status <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button id="applicantCivilStatusDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $civil_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantCivilStatusDropdownBtn">
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

                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Barangay <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button id="applicantBarangayDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $barangay ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantBarangayDropdownBtn">
                            <li><a class="dropdown-item @if($barangay == '') active @endif" href="#" wire:click.prevent="setBarangay('')">— Select —</a></li>
                            @foreach(['Apopong','Baluan','Batomelong','Buayan','Bula','Calumpang','City Heights','Conel','Dadiangas East','Dadiangas North','Dadiangas South','Dadiangas West','Fatima','Katangawan','Labangal','Lagao','Ligaya','Mabuhay','Olympog','San Isidro','San Jose','Siguel','Sinawal','Tambler','Tinagacan','Upper Labay','Other'] as $b)
                                <li><a class="dropdown-item @if($barangay == $b) active @endif" href="#" wire:click.prevent="setBarangay('{{ $b }}')">{{ $b }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @error('barangay') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Street <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="street" id="applicantStreetInput" class="form-control">
                    @error('street') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">House Occupancy Status <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button id="applicantHouseStatusDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $house_occup_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantHouseStatusDropdownBtn">
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
                        <button id="applicantLotStatusDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $lot_occup_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantLotStatusDropdownBtn">
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
                        <button id="applicantJobStatusDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $job_status ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantJobStatusDropdownBtn">
                            <li><a class="dropdown-item @if($job_status == '') active @endif" href="#" wire:click.prevent="setJobStatus('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($job_status == 'Permanent') active @endif" href="#" wire:click.prevent="setJobStatus('Permanent')">Permanent</a></li>
                            <li><a class="dropdown-item @if($job_status == 'Contractual') active @endif" href="#" wire:click.prevent="setJobStatus('Contractual')">Contractual</a></li>
                            <li><a class="dropdown-item @if($job_status == 'Casual') active @endif" href="#" wire:click.prevent="setJobStatus('Casual')">Casual</a></li>
                        </ul>
                    </div>
                    @error('job_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
				</div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Occupation</label>
                    <div class="dropdown">
                        <button id="applicantOccupationDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" @if($custom_occupation) disabled @endif>
                            {{ ($occupation_id && $occupations->find($occupation_id)) ? $occupations->find($occupation_id)->occupation : '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantOccupationDropdownBtn">
                            <li><a class="dropdown-item @if($occupation_id == '') active @endif" href="#" wire:click.prevent="setOccupation('')">— Select —</a></li>
                            @foreach($occupations as $occupation)
                                <li>
                                    <a class="dropdown-item @if($occupation_id == $occupation->occupation_id) active @endif" href="#" wire:click.prevent="setOccupation('{{ $occupation->occupation_id }}')">{{ $occupation->occupation }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @error('occupation_id') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">Occupation (Custom)</label>
                    <input type="text" wire:model.live="custom_occupation" id="applicantCustomOccupationInput" class="form-control" @if($occupation_id) disabled @endif>
                    @error('custom_occupation') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
				<div class="form-group col-md-3">
					<label class="form-label fw-bold">Monthly Income <span class="required-asterisk">*</span></label>
					<input type="number" wire:model.live="monthly_income" id="applicantMonthlyIncomeInput" class="form-control">
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
                        <button id="applicantRepresentingPatientDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $representing_patient ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantRepresentingPatientDropdownBtn">
                            <li><a class="dropdown-item @if($representing_patient == '') active @endif" href="#" wire:click.prevent="setRepresentingPatient('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($representing_patient == 'Self') active @endif" href="#" wire:click.prevent="setRepresentingPatient('Self')">Self</a></li>
                            <li><a class="dropdown-item @if($representing_patient == 'Other Individual/s') active @endif" href="#" wire:click.prevent="setRepresentingPatient('Other Individual/s')">Other Individual/s</a></li>
                            <li><a class="dropdown-item @if($representing_patient == 'Self and Other Individual/s') active @endif" href="#" wire:click.prevent="setRepresentingPatient('Self and Other Individual/s')">Self and Other Individual/s</a></li>
                        </ul>
                    </div>
                    @error('representing_patient') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">If not Self, how many? (Max: 3) <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button id="applicantPatientCountDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" @if($representing_patient === 'Self' || !$representing_patient) disabled @endif>
                            {{ $patient_count ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantPatientCountDropdownBtn">
                            <li><a class="dropdown-item @if($patient_count == '') active @endif" href="#" wire:click.prevent="setPatientCount('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($patient_count == '1') active @endif" href="#" wire:click.prevent="setPatientCount('1')">1</a></li>
                            <li><a class="dropdown-item @if($patient_count == '2') active @endif" href="#" wire:click.prevent="setPatientCount('2')">2</a></li>
                            <li><a class="dropdown-item @if($patient_count == '3') active @endif" href="#" wire:click.prevent="setPatientCount('3')">3</a></li>
                        </ul>
                    </div>
                    @error('patient_count') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">PhilHealth Affiliation <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button id="applicantPhicAffiliationDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            {{ $phic_affiliation ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantPhicAffiliationDropdownBtn">
                            <li><a class="dropdown-item @if($phic_affiliation == '') active @endif" href="#" wire:click.prevent="setPhicAffiliation('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($phic_affiliation == 'Affiliated') active @endif" href="#" wire:click.prevent="setPhicAffiliation('Affiliated')">Affiliated</a></li>
                            <li><a class="dropdown-item @if($phic_affiliation == 'Unaffiliated') active @endif" href="#" wire:click.prevent="setPhicAffiliation('Unaffiliated')">Unaffiliated</a></li>
                        </ul>
                    </div>
                    @error('phic_affiliation') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="form-label fw-bold">PhilHealth Category <span class="required-asterisk">*</span></label>
                    <div class="dropdown">
                        <button id="applicantPhicCategoryDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" @if($phic_affiliation !== 'Affiliated') disabled @endif>
                            {{ $phic_category ?: '— Select —' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="applicantPhicCategoryDropdownBtn">
                            <li><a class="dropdown-item @if($phic_category == '') active @endif" href="#" wire:click.prevent="setPhicCategory('')">— Select —</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Self-Employed') active @endif" href="#" wire:click.prevent="setPhicCategory('Self-Employed')">Self-Employed</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Sponsored') active @endif" href="#" wire:click.prevent="setPhicCategory('Sponsored')">Sponsored</a></li>
                            <li><a class="dropdown-item @if($phic_category == 'Employed') active @endif" href="#" wire:click.prevent="setPhicCategory('Employed')">Employed</a></li>
                        </ul>
                    </div>
                    @error('phic_category') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                </div>

                <div id="patientContainer">
                    @for ($i = 0; $i < ($patient_count ?? 0); $i++)
                        <div class="patient-profile-section mb-4" id="patient-profile-section-{{$i + 1}}">
                            <hr class="pt-2 pb-2">
                            <legend class="form-legend">
                                <i class="fas fa-hospital-user fa-fw"></i><span class="header-title">NAME OF PATIENT {{ $i + 1 }}</span>
                            </legend>
                            <div class="row gx-3 gy-3">
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                                    <input type="text" wire:model.live="patients.{{ $i }}.first_name" id="patientFirstNameInput-{{$i + 1}}" class="form-control" @if($patients[$i]['is_applicant'] ?? false) readonly @endif>
                                    @error("patients.{$i}.first_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Middle Name</label>
                                    <input type="text" wire:model.live="patients.{{ $i }}.middle_name" id="patientMiddleNameInput-{{$i + 1}}" class="form-control" @if($patients[$i]['is_applicant'] ?? false) readonly @endif>
                                    @error("patients.{$i}.middle_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                                    <input type="text" wire:model.live="patients.{{ $i }}.last_name" id="patientLastNameInput-{{$i + 1}}" class="form-control" @if($patients[$i]['is_applicant'] ?? false) readonly @endif>
                                    @error("patients.{$i}.last_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Suffix</label>
                                    <div class="dropdown">
                                        <button id="patientSuffixDropdownBtn-{{$i + 1}}" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" @if($patients[$i]['is_applicant'] ?? false) disabled @endif>
                                            {{ $patients[$i]['suffix'] ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="patientSuffixDropdownBtn-{{$i + 1}}">
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == '') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, '')">— Select —</a></li>
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == 'Sr.') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, 'Sr.')">Sr.</a></li>
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == 'Jr.') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, 'Jr.')">Jr.</a></li>
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == 'II') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, 'II')">II</a></li>
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == 'III') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, 'III')">III</a></li>
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == 'IV') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, 'IV')">IV</a></li>
                                            <li><a class="dropdown-item @if(($patients[$i]['suffix'] ?? '') == 'V') active @endif" href="#" wire:click.prevent="setPatientSuffix({{ $i }}, 'V')">V</a></li>
                                        </ul>
                                    </div>
                                    @error("patients.{$i}.suffix") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        @include('components.layouts.footer.edit-applicant')

    </form>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('profiles.applicants.destroy', ['applicant' => $applicantId]) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <label class="modal-label">This action cannot be undone.<br>To confirm account deletion, type the following.</label>
                        <div class="mb-3">
                            <input type="text" name="deleteConfirmationText" class="form-control" id="formControl" required placeholder="{{ Str::of($first_name . ' ' . $middle_name . ' ' . $last_name . ' ' . $suffix)->trim() }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="commonBtn" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-danger" id="commonBtn">DELETE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
