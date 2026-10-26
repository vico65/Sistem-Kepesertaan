<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Dashboard - Sistem Kepesertaan</title>

    {{-- 
        Injeksi Asset via Vite:
        Semua CSS dan JS dikompilasi oleh Vite. Tidak perlu lagi menambahkan tag <script> manual di akhir file.
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body data-theme="light" class="bg-slate-100 text-slate-800 antialiased font-sans">
    {{-- 
        Diperbaiki: Mengganti kelas 'container' dengan pembungkus 'min-h-screen w-full relative'
        agar tidak membatasi lebar header fixed dan menghindari scrollbar horizontal tak diinginkan.
    --}}
    <div class="min-h-screen w-full flex flex-col">

        {{-- 
            HEADER ATAS (Fixed):
            Diperbaiki: Menggunakan 'w-full' bukan 'w-screen' untuk mencegah overflow horizontal pada OS dengan scrollbar.
        --}}
        <header class="w-full h-14 fixed top-0 left-0 z-20 bg-slate-50 border-b border-slate-200/80 shadow-sm flex items-center gap-2 px-3 sm:px-4">
            {{-- Tombol Toggle Sidebar (Hanya tampil di layar Mobile / < sm) --}}
            <button type="button" id="button_list_sidebar" aria-label="Buka Menu" class="sm:hidden p-1 rounded-md text-slate-700 hover:bg-slate-200/60 cursor-pointer transition-colors">
                <x-icons.menu id="icon_list_sidebar" class="transition-transform duration-200"></x-icons.menu>
            </button>

            {{-- Judul Aplikasi --}}
            <div class="flex items-center gap-2 sm:ml-52">
                <h1 class="font-semibold font-poppins text-sm sm:text-base text-slate-800">
                    Kepesertaan <span class="text-primary-biru font-bold">KC Palembang</span>
                </h1>
            </div>

            {{-- Aksi Sisi Kanan (Logout & Profil Ringkas) --}}
            <div class="flex items-center gap-3 ml-auto mr-2 sm:mr-4">
                {{-- 
                    Diperbaiki:
                    1. Mengganti href="login.html" statis ke "#" (atau route auth ketika sudah siap).
                    2. Tooltip diposisikan secara 'absolute' relatif terhadap pembungkusnya, bukan 'fixed' tanpa koordinat.
                --}}
                <div class="relative group flex items-center">
                    <a href="#" aria-label="Logout" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors">
                        <x-icons.logout></x-icons.logout>
                    </a>
                    <span class="absolute hidden group-hover:block right-0 top-full mt-2 text-xs font-medium text-white bg-slate-900 px-2 py-1 rounded shadow-lg pointer-events-none whitespace-nowrap z-30 transition-opacity">
                        Logout
                    </span>
                </div>
            </div>
        </header>

        {{-- 
            Backdrop Gelap (Overlay Mobile):
            Muncul saat sidebar terbuka di layar smartphone agar pengguna bisa mengetuk area luar untuk menutup sidebar.
        --}}
        <div id="sidebar_backdrop" class="fixed inset-0 bg-black/40 z-10 hidden sm:hidden transition-opacity"></div>

        {{-- 
            SIDEBAR NAVIGASI:
            Diperbaiki:
            1. Menggunakan CSS Hardware-Accelerated transform (-translate-x-full sm:translate-x-0)
               menggantikan margin (-ml-96) untuk performa animasi yang jauh lebih mulus.
            2. Lebar mobile diubah menjadi 'w-64 sm:w-52' agar teks menu tidak terpotong di layar HP.
        --}}
        <nav id="sidebar"
            class="bg-slate-50 z-20 top-14 sm:top-0 w-64 sm:w-52 border-r border-slate-200/80 shadow-md h-screen fixed -translate-x-full sm:translate-x-0 transition-transform duration-200 ease-in-out pt-5 flex flex-col justify-between">

            <div>
                {{-- Section Atas: Informasi Profil Pengguna --}}
                <section class="flex items-center justify-start gap-3 mx-auto w-[92%] card">
                    <div class="overflow-hidden w-12 h-12 rounded-full shadow-sm bg-slate-900 shrink-0">
                        {{-- 
                            Catatan Performa:
                            profil_dummy.png berukuran 2.46 MB. Anda dapat mempertimbangkan mengubahnya ke WebP
                            atau menggunakan file vektor profil_image.svg untuk loading yang instan.
                        --}}
                        <img src="{{ asset('images/Profil-Dummy1.jpg') }}" alt="profil_image" class="w-full h-full object-cover" />
                    </div>

                    <div class="flex flex-col text-left justify-center overflow-hidden">
                        <h2 class="font-bold font-poppins text-xs tracking-wide truncate">
                            Alvico
                        </h2>
                        <p class="text-[10px] tracking-wider font-light font-inter opacity-90 truncate">
                            Admin Kepesertaan
                        </p>
                    </div>
                </section>

                {{-- Section Menu Navigasi --}}
                <div class="px-2 py-3 mt-3">
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="menu-list menu-selected">
                                <x-icons.dashboard></x-icons.dashboard>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="menu-list">
                                <x-icons.people></x-icons.people>
                                <p>List RO</p>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="menu-list">
                                <x-icons.info></x-icons.info>
                                <p>Info PKS</p>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="menu-list">
                                <x-icons.chart></x-icons.chart>
                                <p>Pembagian BU per RO</p>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="menu-list">
                                <x-icons.chart-line></x-icons.chart-line>
                                <p>Pencapaian Wasrik</p>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="menu-list">
                                <x-icons.report></x-icons.report>
                                <p>Rekonsiliasi PBPU Mandiri</p>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="menu-list">
                                <x-icons.profile></x-icons.profile>
                                <p>Administrasi User</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Section Bawah: Footer Sidebar --}}
            <div class="text-center py-3 px-2 border-t border-slate-200/60 text-[10px] text-slate-400 font-inter">
                Kepser KC Palembang &copy; {{ date('Y') }}
            </div>
        </nav>

        {{-- 
            KONTEN UTAMA (Main):
            Diperbaiki: Menggunakan 'w-full' bukan 'w-screen' dan menambahkan padding-top agar tidak tertutup header fixed.
        --}}
        <main class="w-full pt-14 sm:pl-52 min-h-screen">
            {{-- Indikator Loading Halaman --}}
            {{-- <section id="loader" class="p-6">   
                <div class="w-10 h-10 rounded-full border-yellow-500 border-4 border-t-slate-200 animate-spin mx-auto mt-8"></div>
            </section> --}}

            {{-- Kontainer Konten Dinamis --}}
            <section id="container_halaman" class="p-4 sm:p-6"></section>
        </main>
    </div>

    {{-- 
        Diperbaiki:
        Tag <script src="{{ resource_path('js/app.js') }}"></script> telah dihapus.
        Skrip sudah dimuat dengan benar oleh @vite di tag <head>.
    --}}
</body>

</html>

