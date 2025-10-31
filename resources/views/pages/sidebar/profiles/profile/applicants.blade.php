@extends('layouts.personal-pages')

@section('title', 'Applicant Profile')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/profile/applicants.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/profile/applicants.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.list') }}" class="text-decoration-none text-reset">Applicants</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.show', ['applicant' => $applicant->applicant_id]) }}" class="text-decoration-none text-reset">Applicant Profile</a>
@endsection

@section('content')
    <div class="container-fluid mt-4">
        <form action="{{ route('profiles.applicants.update', ['applicant' => $applicant->applicant_id]) }}" method="POST" class="profile-section" id="profileSection">
        @csrf
        @method('PUT')

            <div class="profile-container">
                <div class="row gx-3 gy-3 mb-3">
                    <legend class="form-legend">
                        <i class="fas fa-user fa-fw"></i><span class="header-title">APPLICANT'S NAME</span>
                    </legend>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $applicantData['last_name']) }}" class="form-control" id="applicantLastNameInput" placeholder="Example: Dela Cruz">
                        @error('last_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $applicantData['first_name']) }}" class="form-control" id="applicantFirstNameInput" placeholder="Example: Juan">
                        @error('first_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">Middle Name / Initial</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $applicantData['middle_name']) }}" class="form-control" id="applicantMiddleNameInput" placeholder="Example: Pablo / P.">
                        @error('middle_name') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">Suffix</label>
                        <input type="hidden" name="suffix" id="suffixHidden" value="{{ old('suffix', $applicantData['suffix']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantSuffixDropdownBtn">
                                {{ old('suffix', $applicantData['suffix']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Sr.">Sr.</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Jr.">Jr.</a></li>
                                <li><a class="dropdown-item" href="#" data-value="II">II</a></li>
                                <li><a class="dropdown-item" href="#" data-value="III">III</a></li>
                                <li><a class="dropdown-item" href="#" data-value="IV">IV</a></li>
                                <li><a class="dropdown-item" href="#" data-value="V">V</a></li>
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
                        <input type="hidden" name="sex" id="sexHidden" value="{{ old('sex', $applicantData['sex']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantSexDropdownBtn">
                                {{ old('sex', $applicantData['sex']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Male">Male</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Female">Female</a></li>
                            </ul>
                        </div>
                        @error('sex') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label fw-bold">Birthdate <span class="required-asterisk">*</span></label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $applicantData['birth_date']) }}" class="form-control" id="applicantBirthdateInput">
                        @error('birth_date') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label fw-bold">Age (Read-Only)</label>
                        <input type="text" class="form-control" id="applicantAgeInput" value="{{ old('applicant_age', $applicantData['applicant_age']) }}" readonly>
                        <input type="hidden" name="applicant_age" id="applicantAgeHidden" value="{{ old('applicant_age', $applicantData['applicant_age']) }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">Phone Number <span class="required-asterisk">*</span></label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $applicantData['phone_number']) }}" class="form-control" id="applicantPhoneNumberInput" placeholder="&quot;09...&quot; or &quot;+639...&quot;" maxlength="15" inputmode="tel">
                        @error('phone_number') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">Civil Status <span class="required-asterisk">*</span></label>
                        <input type="hidden" name="civil_status" id="civilStatusHidden" value="{{ old('civil_status', $applicantData['civil_status']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantCivilStatusDropdownBtn">
                                {{ old('civil_status', $applicantData['civil_status']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Single">Single</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Married">Married</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Separated">Separated</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Widowed">Widowed</a></li>
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
                        <input type="hidden" name="job_status" id="jobStatusHidden" value="{{ old('job_status', $applicantData['job_status']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantJobStatusDropdownBtn">
                                {{ old('job_status', $applicantData['job_status']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Retired">Retired</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Permanent">Permanent</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Contractual">Contractual</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Casual">Casual</a></li>
                            </ul>
                        </div>
                        @error('job_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Occupation <span class="fw-normal">(if applicable)</span></label>
                        <input type="hidden" name="occupation_id" id="occupationIdHidden" value="{{ old('occupation_id', $applicantData['occupation_id']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantOccupationDropdownBtn">
                                @if(old('occupation_id', $applicantData['occupation_id']))
                                    {{ $occupations->find(old('occupation_id', $applicantData['occupation_id']))->occupation ?? '— Select —' }}
                                @else
                                    — Select —
                                @endif
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="">Other</a></li>
                                @foreach($occupations as $occupation)
                                    <li>
                                        <a class="dropdown-item" href="#" data-value="{{ $occupation->occupation_id }}">{{ $occupation->occupation }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Occupation <span class="fw-normal">(if other, please specify)</span></label>
                        <input type="text" name="custom_occupation" value="{{ old('custom_occupation', $applicantData['custom_occupation']) }}" class="form-control" id="applicantCustomOccupationInput" placeholder="{{ old('occupation_id', $applicantData['occupation_id']) === '' ? 'If none in existing occupations.' : 'Select "Other" in Occupation dropdown.' }}" {{ old('occupation_id', $applicantData['occupation_id']) === '' ? '' : 'disabled' }}>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Monthly Income <span class="fw-normal">(in ₱)</span></label>
                        <input type="text" id="monthlyIncomeDisplayInput" class="form-control" placeholder="0" inputmode="numeric" maxlength="7">
                        <input type="hidden" name="monthly_income" id="monthlyIncomeHiddenInput" value="{{ old('monthly_income', $applicantData['monthly_income']) }}">
                        @error('monthly_income') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="profile-container">
                <div class="row gx-3 gy-3 mb-3">
                    <legend class="form-legend">
                        <i class="fa fa-home fa-fw"></i><span class="header-title">HOME ADDRESS</span>
                    </legend>
                    <input type="hidden" name="province" value="South Cotabato">
                    <input type="hidden" name="city" value="General Santos">
                    <input type="hidden" name="municipality" value="N / A">
                    <div class="form-group col-md-2">
                        <label class="form-label">House # <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="house_number" value="{{ old('house_number', $applicantData['house_number']) }}" class="form-control" id="applicantHouseNumberInput" placeholder="Either room or lot #">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label">Block # <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="block_number" value="{{ old('block_number', $applicantData['block_number']) }}" class="form-control" id="applicantBlockNumberInput" placeholder="Ex: Block 1">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label">Phase <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="phase" value="{{ old('phase', $applicantData['phase']) }}" class="form-control" id="applicantPhaseInput" placeholder="Ex: Phase 1-A">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label">Street <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="street" value="{{ old('street', $applicantData['street']) }}" class="form-control" id="applicantStreetInput" placeholder="Ex: Matalam St.">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label">Sitio <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="sitio" value="{{ old('sitio', $applicantData['sitio']) }}" class="form-control" id="applicantSitioInput" placeholder="Ex: Sitio Corazon">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="form-label">Purok <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="purok" value="{{ old('purok', $applicantData['purok']) }}" class="form-control" id="applicantPurokInput" placeholder="Ex: Purok Maunlad">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Subdivision <span class="fw-normal">(if applicable)</span></label>
                        <input type="text" name="subdivision" value="{{ old('subdivision', $applicantData['subdivision']) }}" class="form-control" id="applicantSubdivisionInput" placeholder="Ex: Doña Soledad">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Barangay <span class="fw-normal">(if applicable)</span></label>
                        <input type="hidden" name="barangay" id="barangayHidden" value="{{ old('barangay', $applicantData['barangay']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantBarangayDropdownBtn">
                                {{ old('barangay', $applicantData['barangay']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                @foreach(['Apopong', 'Baluan', 'Batomelong', 'Buayan', 'Bula', 'Calumpang', 'City Heights', 'Conel', 'Dadiangas East', 'Dadiangas North', 'Dadiangas South', 'Dadiangas West', 'Fatima', 'Katangawan', 'Labangal', 'Lagao', 'Ligaya', 'Mabuhay', 'Olympog', 'San Isidro', 'San Jose', 'Siguel', 'Sinawal', 'Tambler', 'Tinagacan', 'Upper Labay'] as $b)
                                    <li><a class="dropdown-item" href="#" data-value="{{ $b }}">{{ $b }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">House Occupancy Status <span class="required-asterisk">*</span></label>
                        <input type="hidden" name="house_occup_status" id="houseOccupStatusHidden" value="{{ old('house_occup_status', $applicantData['house_occup_status']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantHouseStatusDropdownBtn">
                                {{ old('house_occup_status', $applicantData['house_occup_status']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Owner">Owner</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Renter">Renter</a></li>
                                <li><a class="dropdown-item" href="#" data-value="House Sharer">House Sharer</a></li>
                            </ul>
                        </div>
                        @error('house_occup_status') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">Lot Occupancy Status <span class="required-asterisk">*</span></label>
                        <input type="hidden" name="lot_occup_status" id="lotOccupStatusHidden" value="{{ old('lot_occup_status', $applicantData['lot_occup_status']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantLotStatusDropdownBtn">
                                {{ old('lot_occup_status', $applicantData['lot_occup_status']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Owner">Owner</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Renter">Renter</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Lot Sharer">Lot Sharer</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Informal Settler">Informal Settler</a></li>
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
                        <input type="number" name="patient_number" value="{{ old('patient_number', $applicantData['patient_number']) }}" class="form-control" id="patientNumberInput" placeholder="1" min="1" max="10" inputmode="numeric">
                        @error('patient_number') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3 d-flex flex-row align-items-end" style="height: 5rem;">
                        <input class="form-check-input" type="checkbox" name="include_applicant_as_patient" value="1" id="checkbox" {{ old('include_applicant_as_patient', $applicantData['include_applicant_as_patient']) ? 'checked' : '' }}>
                        <label class="form-check-label ms-3">Include the applicant<br>(oneself) as a patient.</label>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">PhilHealth Affiliation <span class="required-asterisk">*</span></label>
                        <input type="hidden" name="phic_affiliation" id="phicAffiliationHidden" value="{{ old('phic_affiliation', $applicantData['phic_affiliation']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantPhicAffiliationDropdownBtn">
                                {{ old('phic_affiliation', $applicantData['phic_affiliation']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Affiliated">Affiliated</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Unaffiliated">Unaffiliated</a></li>
                            </ul>
                        </div>
                        @error('phic_affiliation') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label fw-bold">PhilHealth Category</label>
                        <input type="hidden" name="phic_category" id="phicCategoryHidden" value="{{ old('phic_category', $applicantData['phic_category']) }}">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="applicantPhicCategoryDropdownBtn" {{ old('phic_affiliation', $applicantData['phic_affiliation']) !== 'Affiliated' ? 'disabled' : '' }}>
                                {{ old('phic_category', $applicantData['phic_category']) ?: '— Select —' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Self-Employed">Self-Employed</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Sponsored / Indigent">Sponsored / Indigent</a></li>
                                <li><a class="dropdown-item" href="#" data-value="Employed">Employed</a></li>
                            </ul>
                        </div>
                        @error('phic_category') <span class="text-danger mt-3">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div id="patientsContainer">
                @php
                    $patientCount = old('patient_number', $applicantData['patient_number']);
                    $oldPatients = old('patients', $applicantData['patients']);
                @endphp

                @for($index = 1; $index <= $patientCount; $index++)
                    @php
                        $defaultPatient = ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => ''];
                        $patient = isset($oldPatients[$index]) ? array_merge($defaultPatient, $oldPatients[$index]) : $defaultPatient;
                    @endphp

                    <div class="profile-container patient-section" data-patient-index="{{ $index }}">
                        <div class="col-md-12">
                            <div class="row gx-3 gy-3 mb-3">
                                <legend class="form-legend">
                                    <i class="fas fa-hospital-user fa-fw"></i><span class="header-title">NAME OF PATIENT {{ $index }}</span>
                                </legend>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Last Name <span class="required-asterisk">*</span></label>
                                    <input type="text" name="patients[{{ $index }}][last_name]" value="{{ $patient['last_name'] ?? '' }}" class="form-control" id="patientLastNameInput-{{ $index }}" placeholder="Example: Dela Cruz">
                                    @error("patients.{$index}.last_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">First Name <span class="required-asterisk">*</span></label>
                                    <input type="text" name="patients[{{ $index }}][first_name]" value="{{ $patient['first_name'] ?? '' }}" class="form-control" id="patientFirstNameInput-{{ $index }}" placeholder="Example: Juan">
                                    @error("patients.{$index}.first_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Middle Name / Initial</label>
                                    <input type="text" name="patients[{{ $index }}][middle_name]" value="{{ $patient['middle_name'] ?? '' }}" class="form-control" id="patientMiddleNameInput-{{ $index }}" placeholder="Example: Pablo / P.">
                                    @error("patients.{$index}.middle_name") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Suffix</label>
                                    <input type="hidden" name="patients[{{ $index }}][suffix]" id="patientSuffixHidden-{{ $index }}" value="{{ $patient['suffix'] ?? '' }}">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSuffixDropdownBtn-{{ $index }}">
                                            {{ ($patient['suffix'] ?? '') ?: '— Select —' }}
                                        </button>
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
                                    @error("patients.{$index}.suffix") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row gx-3 gy-3">
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Gender / Sex <span class="required-asterisk">*</span></label>
                                    <input type="hidden" name="patients[{{ $index }}][sex]" id="patientSexHidden-{{ $index }}" value="{{ $patient['sex'] ?? '' }}">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientSexDropdownBtn-{{ $index }}">
                                            {{ ($patient['sex'] ?? '') ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu w-100">
                                            <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Male">Male</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Female">Female</a></li>
                                        </ul>
                                    </div>
                                    @error("patients.{$index}.sex") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Age <span class="required-asterisk">*</span></label>
                                    <input type="number" name="patients[{{ $index }}][age]" value="{{ $patient['age'] ?? '' }}" class="form-control patient-age-input" id="patientAgeInput-{{ $index }}" placeholder="0" min="0" max="200">
                                    @error("patients.{$index}.age") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="form-label fw-bold">Category</label>
                                    <input type="hidden" name="patients[{{ $index }}][patient_category]" id="patientCategoryHidden-{{ $index }}" value="{{ $patient['patient_category'] ?? '' }}">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="patientCategoryDropdownBtn-{{ $index }}">
                                            {{ ($patient['patient_category'] ?? '') ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu w-100">
                                            <li><a class="dropdown-item" href="#" data-value="">— Select —</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="PWD">PWD</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Senior">Senior</a></li>
                                        </ul>
                                    </div>
                                    @error("patients.{$index}.patient_category") <span class="text-danger mt-3">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group col-md-3 d-flex justify-content-end" id="removePatientBtnContainer">
                                    <button type="button" class="btn btn-danger" id="removePatientBtn" disabled>REMOVE PATIENT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </form>

        <div class="modal fade" id="deleteAccountModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('profiles.applicants.destroy', ['applicant' => $applicant->applicant_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body" id="modal-body">
                            <label class="modal-label">This action cannot be undone.<br>To confirm account deletion, type the following.</label>
                            <div class="mb-2">
                                <input type="text" name="deleteConfirmationText" class="form-control" id="formControl" required placeholder="{{ $applicantData['first_name'] . ' ' . $applicantData['last_name'] }}">
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
@endsection

@section('footer')
    @include('components.layouts.footer.edit-applicant')
    @include('components.layouts.footer.profile-buttons-4')
@endsection
