function showHouseholdModal() {
    document.getElementById('householdModal').style.display = 'flex';
}

function closeHouseholdModal() {
    document.getElementById('householdModal').style.display = 'none';
}

function createHousehold() {
    document.getElementById('householdForm').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const addHouseholdBtn = document.getElementById('addHouseholdBtn');

    if (addHouseholdBtn) {
        addHouseholdBtn.addEventListener('click', showHouseholdModal);
    }
});
