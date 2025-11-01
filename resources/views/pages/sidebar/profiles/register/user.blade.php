@extends('layouts.personal-pages')

@section('title', 'Add User')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/register/user.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/register/user.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.users.list') }}" class="text-decoration-none text-reset">Users</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.users.create') }}" class="text-decoration-none text-reset">Add User</a>
@endsection

@section('content')
    <div class="container-fluid mt-4" wire:id="{{ \Illuminate\Support\Str::random(20) }}" data-roles-json="{{ json_encode($roles->keyBy('role_id')) }}">
        <div class="profile-container">
            <form method="POST" action="{{ route('profiles.users.store') }}" enctype="multipart/form-data" class="profile-section">
                @csrf

                <div class="row gx-3 gy-3 mb-3">
                    <div class="form-group col-md-3">
                        <label class="form-label">Last Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Example: Dela Cruz" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="form-label">First Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="Example: Juan" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}" placeholder="Example: Pablo">
                    </div>

                    <div class="form-group col-md-3">
                        <label class="form-label">Suffix</label>
                        <div class="dropdown">
                            <button id="suffixDropdownBtn" class="btn dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ old('suffix') ?: 'Select a suffix (optional).' }}
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="suffixDropdownBtn">
                                <li><a class="dropdown-item {{ old('suffix') == '' ? 'active' : '' }}" href="#" data-value="">Select a suffix (optional).</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == 'Sr.' ? 'active' : '' }}" href="#" data-value="Sr.">Sr.</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == 'Jr.' ? 'active' : '' }}" href="#" data-value="Jr.">Jr.</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == 'II' ? 'active' : '' }}" href="#" data-value="II">II</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == 'III' ? 'active' : '' }}" href="#" data-value="III">III</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == 'IV' ? 'active' : '' }}" href="#" data-value="IV">IV</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == 'V' ? 'active' : '' }}" href="#" data-value="V">V</a></li>
                            </ul>
                            <input type="hidden" name="suffix" id="suffixInput" value="{{ old('suffix') }}">
                        </div>
                    </div>
                </div>

                <div class="row gx-3 gy-3 mb-3">
                    <div class="form-group col-md-3">
                        <label class="form-label">Role <span class="required-asterisk">*</span></label>
                        <div class="dropdown">
                            <button id="roleDropdownBtn" class="btn dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ optional($roles->firstWhere('role_id', old('role_id')))->role ?: 'Select a role.' }}
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="roleDropdownBtn">
                                <li><a class="dropdown-item {{ old('suffix') == '' ? 'active' : '' }}" href="#" data-value="">Select a role.</a></li>
                                <li><a class="dropdown-item {{ old('suffix') == '' ? 'active' : '' }}" href="#" data-value="">Other</a></li>
                                @foreach($roles as $role)
                                    <li><a class="dropdown-item {{ old('role_id') === $role->role_id ? 'active' : '' }}" href="#" data-value="{{ $role->role_id }}">{{ $role->role }}</a></li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="role_id" id="roleInput" value="{{ old('role_id') }}" required>
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="form-label">Role <span class="fw-normal">(if "Other", please specify)</span></label>
                        <input id="customRoleInput" class="form-control" type="text" name="custom_role" value="{{ old('custom_role') }}" placeholder="Type to add a new role.">
                    </div>

                    <div class="form-group col-md-3">
                        <label class="form-label">Set Password <span class="required-asterisk">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Write with at least 8 characters." required>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="form-label">Confirm Password <span class="required-asterisk">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Write the password again." required>
                    </div>
                </div>

                <div class="row gx-3 gy-3 mt-3 mb-3">
                    <div class="col-md-6">
                        <div class="row gx-3 gy-3">
                            <div class="form-group col-md-6 button-container">
                                <button type="submit" class="btn btn-primary btn-action-update-profile">Confirm to Add User</button>
                            </div>

                            <div class="form-group col-md-6 button-container">
                                <button type="button" class="btn btn-secondary" id="removeProfilePictureBtn">Remove Picture</button>
                            </div>

                            <div class="col-12">
                                <p class="form-text">Acceptable file extensions: JPG, JPEG, JFIF, PNG, WEBP; maximum file size: 8 MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6 ps-2 pe-2 pb-2">
                        <div class="image-upload-wrap" id="imageUploadWrap">
                            <input type="file" name="profile_picture" id="profilePictureActualInput" class="file-upload-input" accept=".jpg,.jpeg,.jfif,.png,.webp" style="display: none;">
                            <div class="drag-text fw-bold">
                                <i class="fa-solid fa-image image-placeholder-icon" aria-hidden="true"></i><br>
                                <label class="image-placeholder-label">For your profile picture, click to upload an image file from your local file manager, drag an image file here, or paste an image file from your clipboard.</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="role-info-container mt-4 p-3">
            <div class="role-info-content row">
                <div class="col-md-3 text-center">
                    <div class="role-avatar-wrap mx-auto d-flex align-items-center justify-content-center">
                        <img id="roleAvatarImg" class="role-avatar-image d-none" src="#">

                        <div id="roleAvatarPlaceholder" class="role-avatar-placeholder d-flex justify-content-center">
                            <div id="wrapPlaceholder" class="wrap-placeholder">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div id="roleInfoBox" class="role-info-box">
                        <p id="roleInfoName" class="fw-bold fs-5 mb-4">Select a role to view details</p>

                        <div id="roleAllowedActionsArea" class="mb-3">
                            <p class="fw-semibold mb-1">Allowed Actions:</p>
                            <div id="roleAllowedActionsList" class="role-info-list"><span class="text-muted">N/A</span></div>
                        </div>

                        <div id="roleAccessScopeArea">
                            <p class="fw-semibold mb-1">Access Scope:</p>
                            <div id="roleAccessScopeList" class="role-info-list"><span class="text-muted">N/A</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-2"><span class="file-name-display"></span></div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.add-user')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
