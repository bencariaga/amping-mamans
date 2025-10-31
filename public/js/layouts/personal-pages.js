document.addEventListener('DOMContentLoaded', function () {
    const sidebarNav = document.querySelector('.sidebar-nav');
    const currentPath = window.location.pathname;
    const allNavLinks = sidebarNav.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    let matchedLink = null;
    let longestMatch = 0;

    allNavLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath && linkPath !== '#' && currentPath.startsWith(linkPath)) {
            if (linkPath.length > longestMatch) {
                longestMatch = linkPath.length;
                matchedLink = link;
            }
        }
    });

    if (matchedLink) {
        matchedLink.classList.add('active');

        const parentCollapse = matchedLink.closest('.collapse');
        if (parentCollapse) {
            const bsCollapse = new bootstrap.Collapse(parentCollapse, { toggle: false });
            bsCollapse.show();

            const parentToggle = sidebarNav.querySelector(`[href="#${parentCollapse.id}"]`);
            if (parentToggle) {
                parentToggle.classList.add('active');
            }
        }
    }

    const dropdownToggles = sidebarNav.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            dropdownToggles.forEach(t => {
                if (t !== toggle && !t.classList.contains('active')) {
                    const targetCollapse = document.querySelector(t.getAttribute('href'));
                    if (targetCollapse && targetCollapse.classList.contains('show')) {
                        const bsCollapse = bootstrap.Collapse.getInstance(targetCollapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                }
            });
        });
    });

    const collapseElements = document.querySelectorAll('.collapse');
    collapseElements.forEach(collapseEl => {
        collapseEl.addEventListener('shown.bs.collapse', function() {
            const toggle = sidebarNav.querySelector(`[href="#${collapseEl.id}"]`);
            if (toggle) {
                toggle.classList.add('active');
            }
        });

        collapseEl.addEventListener('hidden.bs.collapse', function() {
            const toggle = sidebarNav.querySelector(`[href="#${collapseEl.id}"]`);
            if (toggle) {
                const hasActiveChild = collapseEl.querySelector('.nav-link.active');
                if (!hasActiveChild) {
                    toggle.classList.remove('active');
                }
            }
        });
    });
});
