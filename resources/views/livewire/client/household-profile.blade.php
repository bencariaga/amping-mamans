<div>
    @php
        $members = $members ?? [];
        $occupations = $occupations ?? [];
    @endphp

    <form wire:submit.prevent="update" id="householdProfileForm">
        <div class="household-header-section py-0 d-flex flex-row align-items-center">
            <div class="d-flex flex-row align-items-center" id="hh-form-left">
                <input type="text" id="household-name" name="household_name" class="form-control fw-semibold" wire:model.defer="household_name" required>
                <label class="fw-bold ms-4 fs-4">Family / Household</label>
            </div>
            <div class="d-flex flex-row" id="hh-form-right">
                <p class="ms-4 pt-3"><b>NOTE:</b> In the first row of the table for household members, type in the first row the first applicant registered into this system in order for it to show up the <a href="{{ route('profiles.households.list') }}" class="hyperlink">Households</a> page.</p>
            </div>
        </div>

        <div class="household-form-section">
            <h3 class="m-0">Family Composition (Household Counts)</h3>

            <div class="table-responsive">
                <table class="household-table">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Last Name <label id="household-required-asterisk">*</label></th>
                            <th>First Name <label id="household-required-asterisk">*</label></th>
                            <th>Middle Name</th>
                            <th>Suffix</th>
                            <th>Birthdate</th>
                            <th>Age</th>
                            <th>Civil Status</th>
                            <th>Relationship with Applicant <label id="household-required-asterisk">*</label></th>
                            <th>Educational Attainment</th>
                            <th>Occupation</th>
                            <th>Estimated Monthly Income</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $index => $member)
                            <tr wire:key="member-{{ $index }}" data-client-type="{{ $member['client_type'] ?? 'HOUSEHOLD_MEMBER' }}">
                                <td class="align-top">
                                    <input type="hidden" name="client_id[]" class="client-id-input" value="{{ $member['client_id'] ?? '' }}">
                                    <div class="action-buttons-container d-flex flex-column align-items-center gap-2">
                                        <div class="w-100">
                                            <button type="button" class="btn btn-danger p-3 remove-member-btn" wire:click="removeMember({{ $index }})" @if(count($members) === 1) disabled @endif>
                                                <i class="fas fa-trash fs-5"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary p-3 add-member-btn" wire:click="addMember" @if($member['is_client']) @endif>
                                                <i class="fas fa-plus fs-5"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-primary px-4 w-100 select-client-btn" data-bs-toggle="modal" data-bs-target="#selectClientModal" data-index="{{ $index }}" @if($member['is_client']) disabled @endif>
                                                Select<br>A Client
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <input type="text" class="form-control last-name-input" name="last_name[]" wire:model.defer="members.{{ $index }}.last_name" @if(in_array('last_name', $member['read_only_fields'])) readonly @endif required>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" onchange="useHouseholdName(this, {{ $index }})" @if($member['is_client']) disabled @endif>
                                        <label class="form-check-label ms-2">The same as the family<br>or household name</label>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <input type="text" class="form-control first-name-input" name="first_name[]" wire:model.defer="members.{{ $index }}.first_name" @if(in_array('first_name', $member['read_only_fields'])) readonly @endif required>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary px-3 w-100 verify-first-name-btn" onclick="verifyName(this, {{ $index }})" @if($member['is_client']) disabled @endif>Verify First Name<br>If Existing</button>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <input type="text" class="form-control middle-name-input" name="middle_name[]" wire:model.defer="members.{{ $index }}.middle_name" @if(in_array('middle_name', $member['read_only_fields'])) readonly @endif>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary px-3 w-100 verify-middle-name-btn" onclick="verifyFullName(this, {{ $index }})" @if($member['is_client']) disabled @endif>Verify Middle Name<br>If Existing</button>
                                    </div>
                                </td>

                                <td class="align-top custom-select-wrapper">
                                    <div class="dropdown custom-dropdown suffix-dropdown" data-input-name="suffix[]" @if(in_array('suffix', $member['read_only_fields'])) data-readonly="true" @endif>
                                        <button class="btn dropdown-toggle" id="suffix-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" @if(in_array('suffix', $member['read_only_fields'])) disabled @endif>
                                            {{ $members[$index]['suffix'] ?: '—' }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-value="">—</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Sr.">Sr.</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Jr.">Jr.</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="II">II</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="III">III</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="IV">IV</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="V">V</a></li>
                                        </ul>
                                        <input type="hidden" name="suffix[]" class="hidden-input suffix-hidden-input" wire:model.defer="members.{{ $index }}.suffix" data-index="{{ $index }}">
                                    </div>
                                </td>

                                <td class="align-top">
                                    <input type="date" class="form-control birthdate-input" name="birthdate[]" wire:model.defer="members.{{ $index }}.birthdate" onchange="calculateAge(this)" @if(in_array('birthdate', $member['read_only_fields'])) readonly @endif>
                                </td>

                                <td class="align-top">
                                    <input type="text" class="form-control age-input" id="age-input" name="age[]" wire:model.defer="members.{{ $index }}.age" readonly>
                                    <div class="mt-3">
                                        <label class="note-1"><span class="fst-italic">Read-only,<br>based on<br>birthdate.</span></label>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <div class="dropdown custom-dropdown civil-status-dropdown" data-input-name="civil_status[]" @if(in_array('civil_status', $member['read_only_fields'])) data-readonly="true" @endif>
                                        <button class="btn dropdown-toggle" id="civil-status-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" @if(in_array('civil_status', $member['read_only_fields'])) disabled @endif>
                                            {{ $members[$index]['civil_status'] ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-value="Single">Single</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Married">Married</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Widowed">Widowed</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Separated">Separated</a></li>
                                        </ul>
                                        <input type="hidden" name="civil_status[]" class="hidden-input civil-status-hidden-input" wire:model.defer="members.{{ $index }}.civil_status" data-index="{{ $index }}">
                                    </div>
                                </td>

                                <td class="align-top">
                                    <div class="dropdown custom-dropdown relation-to-head-dropdown" data-input-name="relation_to_head[]">
                                        <button class="btn dropdown-toggle" id="relation-to-head-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $members[$index]['relation_to_head'] ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-value="Self">Self</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Friend">Friend</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Wife">Wife</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Husband">Husband</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Daughter">Daughter</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Son">Son</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Sister">Sister</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Brother">Brother</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Mother">Mother</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Father">Father</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Granddaughter">Granddaughter</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Grandson">Grandson</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Grandmother">Grandmother</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Grandfather">Grandfather</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Aunt">Aunt</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Uncle">Uncle</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Nephew">Nephew</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Niece">Niece</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Cousin">Cousin</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Other">Other</a></li>
                                        </ul>
                                        <input type="hidden" name="relation_to_head[]" class="hidden-input relation-to-head-hidden-input" wire:model.defer="members.{{ $index }}.relation_to_head" required data-index="{{ $index }}">
                                    </div>
                                    <div class="mt-3">
                                        <label class="note-1"><span class="fst-italic">This is for the relationship between<br>the household member in this row<br>
                                            and the household's first applicant.</span></label>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <div class="dropdown custom-dropdown education-dropdown" data-input-name="education[]">
                                        <button class="btn dropdown-toggle" id="education-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $members[$index]['education'] ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-value="Elementary">Elementary</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="High School">High School</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="College">College</a></li>
                                        </ul>
                                        <input type="hidden" name="education[]" class="hidden-input education-hidden-input" wire:model.defer="members.{{ $index }}.education" data-index="{{ $index }}">
                                    </div>
                                    <div class="mt-3">
                                        <label class="note-2"><span class="fst-italic">If not applicable,<br>leave this blank.</span></label>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <div class="dropdown custom-dropdown occupation-dropdown" data-input-name="occupation[]" @if(in_array('occupation', $member['read_only_fields'])) data-readonly="true" @endif>
                                        <button class="btn dropdown-toggle" id="occupation-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" @if(in_array('occupation', $member['read_only_fields'])) disabled @endif>
                                            {{ $members[$index]['occupation'] ?: '— Select —' }}
                                        </button>
                                        <ul class="dropdown-menu occupation-list">
                                            @foreach($occupations as $occ)
                                                <li><a class="dropdown-item" href="#" data-value="{{ $occ }}">{{ $occ }}</a></li>
                                            @endforeach
                                        </ul>
                                        <input type="hidden" name="occupation[]" class="hidden-input occupation-hidden-input" wire:model.defer="members.{{ $index }}.occupation" data-index="{{ $index }}">
                                    </div>
                                    <div class="mt-3">
                                        <label class="note-1"><span class="fst-italic">This is for existing occupations<br>only. For new ones, save this profile<br>first and then go to
                                            <a onclick="window.openOccupationsModal()" class="hyperlink">Occupations</a>.</span></label>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <input type="text" class="form-control monthly-income-input" name="monthly_income[]" wire:model.defer="members.{{ $index }}.monthly_income" oninput="window.formatMonthlyIncome(this)" @if(in_array('monthly_income', $member['read_only_fields'])) readonly @endif>
                                    <div class="mt-3">
                                        <label class="note-2"><span class="fst-italic">In Philippine pesos (₱).<br>Between 0 and 999,999.</span></label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @error('members') <span class="text-danger mt-2 d-block">{{ $message }}</span> @enderror
            @error('household_name') <span class="text-danger mt-2 d-block">{{ $message }}</span> @enderror

            @foreach($errors->get('members.*') as $messages)
                @foreach($messages as $message)
                    <span class="text-danger mt-2 d-block">{{ $message }}</span>
                @endforeach
            @endforeach
        </div>
    </form>
</div>
