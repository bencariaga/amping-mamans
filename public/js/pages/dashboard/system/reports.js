document.addEventListener('DOMContentLoaded', function() {
    const filterMode = document.getElementById('filterMode');
    const groupRange = document.getElementById('group-range');
    const groupMonth = document.getElementById('group-month');
    const groupYear = document.getElementById('group-year');

    function setMode(value) {
        if (groupRange) groupRange.style.display = (value === 'range') ? '' : 'none';
        if (groupMonth) groupMonth.style.display = (value === 'month') ? '' : 'none';
        if (groupYear) groupYear.style.display = (value === 'year') ? '' : 'none';
    }

    if (filterMode) {
        filterMode.addEventListener('change', function() {
            setMode(this.value);
        });

        setMode(filterMode.value);
    }
});
