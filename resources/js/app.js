/**
 * Sistem Kepesertaan - Core Application Script
 * Mengatur interaktivitas UI dashboard, navigasi responsive, dan sidebar.
 */

// Menangkap elemen-elemen DOM yang digunakan
const buttonListSidebar = document.getElementById('button_list_sidebar');
const sidebar = document.getElementById('sidebar');
const iconListSidebar = document.getElementById('icon_list_sidebar');
const sidebarBackdrop = document.getElementById('sidebar_backdrop');

/**
 * Fungsi untuk membuka/menutup (toggle) sidebar pada layar mobile.
 * Menggunakan class transform CSS (translate-x) agar performa animasi lebih halus (GPU accelerated)
 * dibanding mengubah margin (-ml-96) yang memicu layout reflow.
 */
const toggleSidebar = () => {
    if (!sidebar) return;

    // Toggle kelas geser sidebar (tampil vs sembunyi)
    sidebar.classList.toggle('-translate-x-full');

    // Toggle animasi ikon burger/menu jika elemennya ada
    if (iconListSidebar) {
        iconListSidebar.classList.toggle('text-primary');
        iconListSidebar.classList.toggle('scale-110');
    }

    // Toggle backdrop gelap untuk kemudahan menutup sidebar di layar sentuh/mobile
    if (sidebarBackdrop) {
        sidebarBackdrop.classList.toggle('hidden');
    }
};

/**
 * Event Listener dengan Null-safety:
 * Menghindari error runtime "Cannot read properties of null" jika script dimuat di halaman tanpa sidebar.
 */
buttonListSidebar?.addEventListener('click', toggleSidebar);
sidebarBackdrop?.addEventListener('click', toggleSidebar);

