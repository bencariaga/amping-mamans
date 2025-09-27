document.addEventListener('DOMContentLoaded', function () {
    const sidebarNav = document.querySelector('.sidebar-nav');
    const dashboardLink = sidebarNav.querySelector('a[href*="/dashboard"]');
    const currentPath = window.location.href.split('#')[0];
    const currentLink = sidebarNav.querySelector(`.nav-link[href*="${currentPath}"]`);

    if (currentLink && !currentLink.classList.contains('dropdown-toggle')) {
        currentLink.classList.add('active');

        if (currentLink !== dashboardLink) {
            dashboardLink.classList.remove('active');
        }

        const parentCollapse = currentLink.closest('.collapse');

        if (parentCollapse) {
            const parentToggle = sidebarNav.querySelector(`[href="#${parentCollapse.id}"]`);
            parentToggle.classList.add('active', 'current-page-active');
        }
    } else {
        dashboardLink.classList.add('active');

        const collapseElements = document.querySelectorAll('.collapse');

        collapseElements.forEach(collapseEl => {
            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);

            if (bsCollapse && collapseEl.classList.contains('show')) {
                bsCollapse.hide();
            }

            const toggle = sidebarNav.querySelector(`[href="#${collapseEl.id}"]`);

            if (toggle) {
                toggle.classList.remove('active', 'current-page-active');
            }
        });
    }
});
