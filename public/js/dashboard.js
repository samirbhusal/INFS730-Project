/**
 * Employee Dashboard — Client-side Logic
 * ────────────────────────────────────────
 * Live clock, late-reason toggle, form validation,
 * hamburger menu, sidebar toggle, profile dropdown.
 */

(function () {
    'use strict';

    /* ── Live Clock ─────────────────────────────── */

    const clockEl = document.getElementById('liveClock');

    function updateClock() {
        const now = new Date();
        let h = now.getHours();
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        clockEl.textContent = `${h}:${m}:${s} ${ampm}`;
    }

    if (clockEl) {
        updateClock();
        setInterval(updateClock, 1000);
    }

    /* ── Hamburger + Sidebar Toggle ────────────── */

    const hamburgerBtn    = document.getElementById('hamburgerBtn');
    const sidebar         = document.getElementById('sidebar');
    const sidebarOverlay  = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        const isOpen = sidebar.classList.toggle('open');
        hamburgerBtn.classList.toggle('active', isOpen);
        sidebarOverlay.classList.toggle('show', isOpen);
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        hamburgerBtn.classList.remove('active');
        sidebarOverlay.classList.remove('show');
    }

    if (hamburgerBtn && sidebar) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    /* ── Profile Dropdown Toggle ────────────────── */

    const profileBtn      = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('open');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.remove('open');
            }
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                profileDropdown.classList.remove('open');
            }
        });
    }

    /* ── Late-reason field toggle (real-time) ──── */

    const reasonField = document.getElementById('reasonField');
    const checkInForm = document.getElementById('checkInForm');

    if (reasonField && checkInForm) {
        const SHIFT_HOUR = 9;
        const SHIFT_MIN  = 0;
        const GRACE      = 15;

        function checkLateStatus() {
            const now = new Date();
            const deadline = new Date();
            deadline.setHours(SHIFT_HOUR, SHIFT_MIN + GRACE, 0, 0);

            if (now > deadline) {
                reasonField.classList.add('show');
                const textarea = document.getElementById('lateReason');
                if (textarea) textarea.required = true;
            }
        }

        checkLateStatus();
        setInterval(checkLateStatus, 10000);

        /* ── Form Validation ───────────────────── */

        checkInForm.addEventListener('submit', function (e) {
            const textarea  = document.getElementById('lateReason');
            const errorEl   = document.getElementById('reasonError');

            if (textarea && textarea.required && textarea.value.trim() === '') {
                e.preventDefault();
                errorEl.textContent = 'Please provide a reason for your late check-in.';
                textarea.focus();
                return;
            }

            if (errorEl) errorEl.textContent = '';
        });
    }

})();
