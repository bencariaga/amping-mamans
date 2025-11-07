document.addEventListener('DOMContentLoaded', function () {
    const sidebarNav = document.querySelector('.sidebar-nav');
    if (!sidebarNav) return;
    
    const currentPath = window.location.pathname;
    const currentFullPath = window.location.pathname + window.location.search;
    
    // Remove active class from all nav links
    const allNavLinks = sidebarNav.querySelectorAll('.nav-link');
    allNavLinks.forEach(link => link.classList.remove('active'));
    
    // Find the best matching link
    let bestMatch = null;
    let bestMatchScore = 0;
    
    const navLinks = sidebarNav.querySelectorAll('.nav-link[href]:not(.submenu-toggle)');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        // Skip anchors and non-URL links
        if (!href || href === '#' || href.startsWith('javascript:')) return;
        
        try {
            const linkUrl = new URL(href, window.location.origin);
            const linkPath = linkUrl.pathname;
            const linkFullPath = linkUrl.pathname + linkUrl.search;
            
            let score = 0;
            
            // Exact match with query string (highest priority)
            if (currentFullPath === linkFullPath && linkFullPath !== '/') {
                score = linkFullPath.length + 1000;
            }
            // Exact path match
            else if (currentPath === linkPath && linkPath !== '/') {
                score = linkPath.length + 100;
            }
            // Path starts with link path (for nested routes)
            else if (linkPath !== '/' && currentPath.startsWith(linkPath + '/')) {
                score = linkPath.length;
            }
            // Special case for dashboard
            else if (linkPath === '/dashboard' && currentPath === '/dashboard') {
                score = 50;
            }
            
            if (score > bestMatchScore) {
                bestMatchScore = score;
                bestMatch = link;
            }
        } catch (e) {
            console.error('Error parsing href:', href, e);
        }
    });
    
    // Apply active class to best match
    if (bestMatch) {
        bestMatch.classList.add('active');
        
        // If active link is in a submenu, expand it and highlight parent
        const parentCollapse = bestMatch.closest('.collapse');
        if (parentCollapse) {
            parentCollapse.classList.add('show');
            const parentToggle = sidebarNav.querySelector(`[href="#${parentCollapse.id}"]`);
            if (parentToggle) {
                parentToggle.classList.add('active');
                parentToggle.setAttribute('aria-expanded', 'true');
            }
        }
    } else {
        // Default to dashboard if no match
        const dashboardLink = sidebarNav.querySelector('a[href*="/dashboard"]');
        if (dashboardLink) {
            dashboardLink.classList.add('active');
        }
    }
    
    // Handle submenu toggle chevron rotation
    const submenuToggles = document.querySelectorAll('.submenu-toggle[data-bs-toggle="collapse"]');
    submenuToggles.forEach(toggle => {
        const targetId = toggle.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            targetElement.addEventListener('show.bs.collapse', function () {
                toggle.setAttribute('aria-expanded', 'true');
            });
            
            targetElement.addEventListener('hide.bs.collapse', function () {
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });
});
