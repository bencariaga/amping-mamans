<a type="button" id="householdBtn" class="footer-btn" href="{{ route('profiles.households.list') }}">
    <div class="nav-icon"><i class="fa fa-long-arrow-alt-left"></i></div>
    <div class="nav-text">Back to List</div>
</a>

<a type="submit" id="updateHouseholdBtn" class="footer-btn" onclick="document.getElementById('householdProfileForm').submit();">
    <div class="nav-icon"><i class="fas fa-save"></i></div>
    <div class="nav-text">Save Changes</div>
</a>

<a type="button" id="exportBtn" class="footer-btn">
    <div class="nav-icon"><i class="fas fa-file-excel"></i></div>
    <div class="nav-text">Export as<br>Excel File</div>
</a>
