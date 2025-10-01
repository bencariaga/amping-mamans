@extends('layouts.personal-pages')

@section('title', 'User Profile')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/profile/users.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/profile/users.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.users.list') }}" class="text-decoration-none text-reset">Users</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('user.profile.show') }}" class="text-decoration-none text-reset">My Profile</a>
@endsection

@section('content')
    @php
        $isSelf = Auth::id() === $user->member_id;
        $updateRoute = $isSelf ? route('user.profile.update') : route('profiles.users.update', ['user' => $user->member_id]);
        $destroyRoute = $isSelf ? route('user.profile.destroy') : route('profiles.users.destroy', ['user' => $user->member_id]);
        $imageRecord = $user->files()->firstWhere('file_type', 'Image');
        $profileImage = optional($imageRecord)->filename;
        $roleLabel = $user->member_type === 'Staff' && $user->staff ? optional($user->staff->role)->role : 'N/A';
        $fullName = trim($user->first_name . ' ' . $user->last_name);
    @endphp

    <div class="container-fluid mt-4">
        <div class="profile-container d-flex">
            <div class="profile-left-section text-center me-4">
                <div class="profile-pic-container">
                    <label class="profile-pic-wrapper">
                        @if($profileImage)
                            <img src="{{ asset('storage/' . $profileImage) }}" class="profile-pic rounded-circle" onerror="this.style.border='2px solid red'">
                        @else
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="edit-icon"><i class="fas fa-pencil-alt"></i></div>
                    </label>
                </div>
                <div class="user-role">{{ $roleLabel }}</div>
            </div>

            <form method="POST" action="{{ $updateRoute }}" enctype="multipart/form-data" class="profile-right-section">
                @csrf
                @method('PUT')

                <input type="file" name="profile_picture" id="profile_picture_upload" class="d-none" accept=".jpg,.jpeg,.jfif,.png,image/jpeg,image/png">
                <input type="hidden" name="remove_profile_picture_flag" id="remove_profile_picture_flag" value="0">

                <div class="profile-grid-container">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required-asterisk">*</span></label>
                        <input id="form-input" type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required pattern="[A-Za-z ]+" title="Letters and spaces only">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required-asterisk">*</span></label>
                        <input id="form-input" type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required pattern="[A-Za-z ]+" title="Letters and spaces only">
                    </div>
                    <div class="form-group button-container">
                        <button type="button" class="btn btn-primary" id="commonBtn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            CHANGE PASSWORD
                        </button>
                    </div>
                    <div class="form-group button-container">
                        <button type="button" class="btn btn-danger" id="commonBtn" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            DELETE ACCOUNT
                        </button>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input id="form-input" type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $user->middle_name) }}" pattern="[A-Za-z ]*" title="Letters and spaces only">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Suffix</label>
                        <div class="dropdown">
                            <button id="suffixDropdownBtn" class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ old('suffix', $user->suffix) ?: '' }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="#" data-value=""></a></li>
                                @foreach(['Sr.','Jr.','II','III','IV','V'] as $suf)
                                    <li><a class="dropdown-item" href="#" data-value="{{ $suf }}">{{ $suf }}</a></li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="suffix" id="suffixInput" value="{{ old('suffix', $user->suffix) }}">
                        </div>
                    </div>
                    <div class="form-group button-container">
                        <button type="button" class="btn btn-danger" id="removeProfilePictureBtn">
                            REMOVE PICTURE
                        </button>
                    </div>
                    <div class="form-group button-container">
                        <button type="submit" class="btn btn-primary" id="updateProfileBtn">
                            UPDATE PROFILE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ $updateRoute }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="action" value="change_password">
                    <div class="modal-header" id="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <div class="mb-3">
                            <label class="modal-label">To confirm password change, type the following.</label>
                            <input type="text" name="username_confirmation_change" class="form-control" id="formControl" required placeholder="{{ $fullName }}">
                        </div>
                        <div class="mb-3">
                            <label class="modal-label">Provide the new password.</label>
                            <input type="password" name="new_password" class="form-control" id="formControl" required>
                        </div>
                        <div class="mb-3">
                            <label class="modal-label">Confirm the new password.</label>
                            <input type="password" name="new_password_confirmation" class="form-control" id="formControl" required>
                        </div>
                    </div>
                    <div class="modal-footer" id="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn btn-primary">CHANGE PASSWORD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ $destroyRoute }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header" id="modal-header">
                        <h5 class="modal-title">Delete Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <label class="modal-label">This action cannot be undone.<br>To confirm account deletion, type the following.</label>
                        <div class="mb-3">
                            <input type="text" name="username_confirmation_delete" class="form-control" id="formControl" required placeholder="{{ $fullName }}">
                        </div>
                    </div>
                    <div class="modal-footer" id="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-danger">DELETE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
