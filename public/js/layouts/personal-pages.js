document.addEventListener('DOMContentLoaded', function () {
    const sidebarNav = document.querySelector('.sidebar-nav');
    const dropdownToggles = sidebarNav.querySelectorAll('.dropdown-toggle');
    const collapseElements = sidebarNav.querySelectorAll('.collapse');
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

        collapseElements.forEach(collapseEl => {
            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);

            if (bsCollapse && collapseEl.classList.contains('show')) {
                bsCollapse.hide();
            }

            const toggle = sidebarNav.querySelector(`[href="#${collapseEl.id}"]`);
            toggle.classList.remove('active', 'current-page-active');
        });
    }

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            dashboardLink.classList.remove('active');

            const targetId = this.getAttribute('href').substring(1);

            collapseElements.forEach(collapseEl => {
                const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);

                if (collapseEl.classList.contains('show') && collapseEl.id !== targetId && bsCollapse) {
                    bsCollapse.hide();
                }
            });
        });

        toggle.addEventListener('show.bs.collapse', function () {
            this.classList.add('rotated');
        });

        toggle.addEventListener('hide.bs.collapse', function () {
            this.classList.remove('rotated');
        });
    });

    collapseElements.forEach(collapseEl => {
        const toggle = sidebarNav.querySelector(`[href="#${collapseEl.id}"]`);

        collapseEl.addEventListener('shown.bs.collapse', function () {
            toggle.classList.add('active');
        });

        collapseEl.addEventListener('hidden.bs.collapse', function () {
            if (!toggle.classList.contains('current-page-active')) {
                toggle.classList.remove('active');
            }

            const anyActive = sidebarNav.querySelector('.nav-link.active:not([href*="/dashboard"])');

            if (!anyActive) {
                if (currentLink && !currentLink.classList.contains('dropdown-toggle')) {
                    currentLink.classList.add('active');
                } else {
                    dashboardLink.classList.add('active');
                }
            }
        });
    });

    collapseElements.forEach(collapseEl => {
        const toggle = sidebarNav.querySelector(`[href="#${collapseEl.id}"]`);

        collapseEl.addEventListener('shown.bs.collapse', function () {
            toggle.classList.add('rotated');
        });

        collapseEl.addEventListener('hidden.bs.collapse', function () {
            toggle.classList.remove('rotated');
            const anyActive = sidebarNav.querySelector('.nav-link.active:not([href*="/dashboard"])');

            if (!anyActive) {
                if (currentLink && !currentLink.classList.contains('dropdown-toggle')) {
                    currentLink.classList.add('active');
                } else {
                    dashboardLink.classList.add('active');
                }
            }
        });
    });
});
