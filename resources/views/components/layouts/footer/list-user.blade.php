<a type="button" id="backToDashboardBtn" class="footer-btn" href="{{ route('dashboard') }}">
    <div class="nav-icon"><i class="fa fa-long-arrow-alt-left"></i></div>
    <div class="nav-text">Back to Dashboard</div>
</a>

<a type="button" id="addUserBtn" class="footer-btn" href="{{ route('profiles.users.create') }}">
    <div class="nav-icon"><i class="fas fa-plus-circle"></i></div>
    <div class="nav-text">Add User</div>
</a>

<a type="button" id="editRoleBtn" class="footer-btn">
    <div class="nav-icon"><i class="fas fa-user-edit"></i></div>
    <div class="nav-text">Edit Roles</div>
</a>

<a type="submit" id="saveChangesBtn" class="footer-btn d-none">
    <div class="nav-icon"><i class="fas fa-save"></i></div>
    <div class="nav-text">Save Changes</div>
</a>
