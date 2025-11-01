<tr data-client-type="{{ $member['client_type'] ?? 'HOUSEHOLD_MEMBER' }}" data-index="{{ $index }}">
    <td class="align-top">
        <input type="hidden" name="members[{{ $index }}][client_id]" class="client-id-input" value="{{ $member['client_id'] ?? '' }}">
        <input type="hidden" name="members[{{ $index }}][client_type]" value="{{ $member['client_type'] ?? 'HOUSEHOLD_MEMBER' }}">
        <input type="hidden" name="members[{{ $index }}][is_client]" value="{{ $member['is_client'] ? '1' : '0' }}">
        <div class="action-buttons-container d-flex flex-column align-items-center gap-2">
            <div class="w-100">
                <button type="button" class="btn btn-danger p-3 remove-member-btn" data-index="{{ $index }}" @if(count($clients ?? []) === 1) disabled @endif>
                    <i class="fas fa-trash fs-5"></i>
                </button>
                <button type="button" class="btn btn-primary p-3 add-member-btn" data-index="{{ $index }}">
                    <i class="fas fa-plus fs-5"></i>
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-primary px-4 w-100 select-client-btn" data-bs-toggle="modal" data-bs-target="#selectClientModal" data-index="{{ $index }}" {{ $member['is_client'] ? 'disabled' : '' }}>
                    Select<br>A Client
                </button>
            </div>
        </div>
    </td>

    <td class="align-top">
        <input type="text" class="form-control last-name-input" name="members[{{ $index }}][last_name]" value="{{ old('members.'.$index.'.last_name', $member['last_name'] ?? '') }}" {{ in_array('last_name', $member['read_only_fields'] ?? []) ? 'readonly' : '' }} required>
        <div class="form-check mt-3">
            <input class="form-check-input use-household-name-checkbox" type="checkbox" {{ $member['is_client'] ? 'disabled' : '' }}>
            <label class="form-check-label ms-2">The same as the family<br>or household name</label>
        </div>
    </td>

    <td class="align-top">
        <input type="text" class="form-control first-name-input" name="members[{{ $index }}][first_name]" value="{{ old('members.'.$index.'.first_name', $member['first_name'] ?? '') }}" {{ in_array('first_name', $member['read_only_fields'] ?? []) ? 'readonly' : '' }} required>
        <div class="mt-2">
            <button type="button" class="btn btn-primary px-3 w-100 verify-first-name-btn" {{ $member['is_client'] ? 'disabled' : '' }}>Verify First Name<br>If Existing</button>
        </div>
    </td>

    <td class="align-top">
        <input type="text" class="form-control middle-name-input" name="members[{{ $index }}][middle_name]" value="{{ old('members.'.$index.'.middle_name', $member['middle_name'] ?? '') }}" {{ in_array('middle_name', $member['read_only_fields'] ?? []) ? 'readonly' : '' }}>
        <div class="mt-2">
            <button type="button" class="btn btn-primary px-3 w-100 verify-middle-name-btn" {{ $member['is_client'] ? 'disabled' : '' }}>Verify Middle Name<br>If Existing</button>
        </div>
    </td>

    <td class="align-top custom-select-wrapper">
        <div class="dropdown custom-dropdown suffix-dropdown" data-input-name="members[{{ $index }}][suffix]" {{ in_array('suffix', $member['read_only_fields'] ?? []) ? 'data-readonly="true"' : '' }}>
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" {{ in_array('suffix', $member['read_only_fields'] ?? []) ? 'disabled' : '' }}>
                {{ $member['suffix'] ?: '—' }}
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
            <input type="hidden" name="members[{{ $index }}][suffix]" class="hidden-input suffix-hidden-input" value="{{ old('members.'.$index.'.suffix', $member['suffix'] ?? '') }}">
        </div>
    </td>

    <td class="align-top">
        <input type="date" class="form-control birthdate-input" name="members[{{ $index }}][birthdate]" value="{{ old('members.'.$index.'.birthdate', $member['birthdate'] ?? '') }}" {{ in_array('birthdate', $member['read_only_fields'] ?? []) ? 'readonly' : '' }}>
    </td>

    <td class="align-top">
        <input type="text" class="form-control age-input" name="members[{{ $index }}][age]" value="{{ old('members.'.$index.'.age', $member['age'] ?? '') }}" readonly>
        <div class="mt-3">
            <label class="note-1"><span class="fst-italic">Read-only,<br>based on<br>birthdate.</span></label>
        </div>
    </td>

    <td class="align-top">
        <div class="dropdown custom-dropdown civil-status-dropdown" data-input-name="members[{{ $index }}][civil_status]" {{ in_array('civil_status', $member['read_only_fields'] ?? []) ? 'data-readonly="true"' : '' }}>
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" {{ in_array('civil_status', $member['read_only_fields'] ?? []) ? 'disabled' : '' }}>
                {{ $member['civil_status'] ?: '— Select —' }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-value="Single">Single</a></li>
                <li><a class="dropdown-item" href="#" data-value="Married">Married</a></li>
                <li><a class="dropdown-item" href="#" data-value="Widowed">Widowed</a></li>
                <li><a class="dropdown-item" href="#" data-value="Separated">Separated</a></li>
            </ul>
            <input type="hidden" name="members[{{ $index }}][civil_status]" class="hidden-input civil-status-hidden-input" value="{{ old('members.'.$index.'.civil_status', $member['civil_status'] ?? '') }}">
        </div>
    </td>

    <td class="align-top">
        <div class="dropdown custom-dropdown relation-to-head-dropdown" data-input-name="members[{{ $index }}][relation_to_head]">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $member['relation_to_head'] ?: '— Select —' }}
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
            </ul>
            <input type="hidden" name="members[{{ $index }}][relation_to_head]" class="hidden-input relation-to-head-hidden-input" value="{{ old('members.'.$index.'.relation_to_head', $member['relation_to_head'] ?? '') }}" required>
        </div>
        <div class="mt-3">
            <label class="note-1"><span class="fst-italic">This is for the relationship between<br>the household member in this row<br>and the household's first applicant.</span></label>
        </div>
    </td>

    <td class="align-top">
        <div class="dropdown custom-dropdown education-dropdown" data-input-name="members[{{ $index }}][education]">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $member['education'] ?: '— Select —' }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-value="Elementary">Elementary</a></li>
                <li><a class="dropdown-item" href="#" data-value="High School">High School</a></li>
                <li><a class="dropdown-item" href="#" data-value="College">College</a></li>
            </ul>
            <input type="hidden" name="members[{{ $index }}][education]" class="hidden-input education-hidden-input" value="{{ old('members.'.$index.'.education', $member['education'] ?? '') }}">
        </div>
        <div class="mt-3">
            <label class="note-2"><span class="fst-italic">If not applicable,<br>leave this blank.</span></label>
        </div>
    </td>

    <td class="align-top">
        <div class="dropdown custom-dropdown occupation-dropdown" data-input-name="members[{{ $index }}][occupation]" {{ in_array('occupation', $member['read_only_fields'] ?? []) ? 'data-readonly="true"' : '' }}>
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" {{ in_array('occupation', $member['read_only_fields'] ?? []) ? 'disabled' : '' }}>
                {{ $member['occupation'] ?: '— Select —' }}
            </button>
            <ul class="dropdown-menu occupation-list">
                @foreach($occupations as $occ)
                    <li><a class="dropdown-item" href="#" data-value="{{ $occ }}">{{ $occ }}</a></li>
                @endforeach
            </ul>
            <input type="hidden" name="members[{{ $index }}][occupation]" class="hidden-input occupation-hidden-input" value="{{ old('members.'.$index.'.occupation', $member['occupation'] ?? '') }}">
        </div>
        <div class="mt-3">
            <label class="note-1"><span class="fst-italic">This is for existing occupations<br>only. For new ones, save this profile<br>first and then go to
                <a onclick="window.openOccupationsModal()" class="hyperlink">Occupations</a>.</span></label>
        </div>
    </td>

    <td class="align-top">
        <input type="text" class="form-control monthly-income-input" name="members[{{ $index }}][monthly_income]" value="{{ old('members.'.$index.'.monthly_income', $member['monthly_income'] ?? '') }}" {{ in_array('monthly_income', $member['read_only_fields'] ?? []) ? 'readonly' : '' }}>
        <div class="mt-3">
            <label class="note-2"><span class="fst-italic">In Philippine pesos (₱).<br>Between 0 and 999,999.</span></label>
        </div>
    </td>
</tr>
