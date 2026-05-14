/**
 * app.js — Sistem Absensi
 * Handles: real-time clock, sidebar toggle (mobile), misc UI helpers
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Real-time Clock ──────────────────────────────────── */
    const DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const MONTHS_FULL = ['Januari','Februari','Maret','April','Mei','Juni',
                         'Juli','Agustus','September','Oktober','November','Desember'];

    function pad(n) { return String(n).padStart(2, '0'); }

    function updateClock() {
        const now  = new Date();
        const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        const dateShort = `${DAYS[now.getDay()]}, ${now.getDate()} ${MONTHS_FULL[now.getMonth()]} ${now.getFullYear()}`;

        // Navbar clock
        const navClock = document.getElementById('navClock');
        if (navClock) navClock.textContent = time;

        // Sidebar clock
        const sbTime = document.getElementById('sidebarTime');
        const sbDate = document.getElementById('sidebarDate');
        if (sbTime) sbTime.textContent = time;
        if (sbDate) sbDate.textContent = dateShort;

        // Legacy: dashboard big clock (if view uses id="clock" / id="date")
        const legacyClock = document.getElementById('clock');
        const legacyDate  = document.getElementById('date');
        if (legacyClock) legacyClock.textContent = time;
        if (legacyDate)  legacyDate.textContent  = dateShort;
    }

    updateClock();
    setInterval(updateClock, 1000);


    /* ── Sidebar Toggle (mobile) ──────────────────────────── */
    const sidebar  = document.getElementById('appSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const openBtn  = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    function openSidebar() {
        if (!sidebar || !overlay) return;
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent scroll behind
    }

    function closeSidebar() {
        if (!sidebar || !overlay) return;
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (openBtn)  openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay)  overlay.addEventListener('click', closeSidebar);

    // Close sidebar when a link is tapped on mobile
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) closeSidebar();
            });
        });
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) closeSidebar();
    });


    /* ── Auto-dismiss flash alerts ────────────────────────── */
    document.querySelectorAll('.app-alert.alert-success').forEach(function (el) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            if (bsAlert) bsAlert.close();
        }, 4000);
    });


    /* ── Navbar scroll shadow ─────────────────────────────── */
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 8) {
                navbar.style.boxShadow = '0 4px 20px rgba(22,101,52,.35)';
            } else {
                navbar.style.boxShadow = '0 2px 12px rgba(22,101,52,.30)';
            }
        }, { passive: true });
    }

    const DAYS_ID = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni',
'Juli','Agustus','September','Oktober','November','Desember'];

function pad(n){ return String(n).padStart(2,'0'); }

function updateDashClock(){
    const now = new Date();

    document.getElementById('dashHour').textContent = pad(now.getHours());
    document.getElementById('dashMin').textContent = pad(now.getMinutes());
    document.getElementById('dashSec').textContent = pad(now.getSeconds());
    document.getElementById('dashDay').textContent = DAYS_ID[now.getDay()];
    document.getElementById('dashDate').textContent =
        `${now.getDate()} ${MONTHS_ID[now.getMonth()]} ${now.getFullYear()}`;
}

setInterval(updateDashClock, 1000);
updateDashClock();


function startAbsensi(tipe){
    alert("Mulai absensi: " + tipe);
}

});