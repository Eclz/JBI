document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('mobile-menu-toggle');
    const sidebar = document.getElementById('student-navigation');
    const overlay = document.getElementById('sidebar-overlay');
    if (!button || !sidebar || !overlay) return;
    const mobile = window.matchMedia('(max-width: 991.98px)');
    const setOpen = open => {
        sidebar.classList.toggle('show', open);
        overlay.classList.toggle('show', open);
        document.body.classList.toggle('navigation-open', open);
        button.setAttribute('aria-expanded', String(open));
        button.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
        sidebar.inert = mobile.matches && !open;
    };
    button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));
    overlay.addEventListener('click', () => { setOpen(false); button.focus(); });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') { setOpen(false); button.focus(); }
    });
    sidebar.addEventListener('click', event => { if (mobile.matches && event.target.closest('a')) setOpen(false); });
    mobile.addEventListener('change', () => setOpen(false));
    setOpen(false);
});
