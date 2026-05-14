const toggle = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');

const overlay = document.createElement('div');
overlay.classList.add('sidebar-overlay');
document.body.appendChild(overlay);

toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
});

overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});