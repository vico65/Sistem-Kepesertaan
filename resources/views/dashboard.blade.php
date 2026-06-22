<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body data-theme="light">
    <div class="container">
        <header class="w-screen h-14 fixed z-20 bg-slate-50 shadow-sm flex gap-2 px-3">
            <button role="button" id="button_list_sidebar" class="sm:hidden p-1 cursor-pointer group">
                <x-icons.menu id="icon_list_sidebar"
                    class="hover:text-primary hover:scale-115
                "></x-icons.menu>
            </button>

            <div class="flex gap-2 sm:ml-52">
                {{-- <img src="../../res/logo_unsri.png" alt="logo_unsri" class="w-8 shadow-md h-8  self-center dark:hidden" /> --}}
                <h1 class="h-fit font-semibold font-poppins poppins-medium self-center">
                    Kepesertaan <span class="text-primary-biru">KC Palembang</span>
                </h1>
            </div>

            <div class="flex gap-3 sm:gap-4 self-center ml-auto mr-4 md:mr-10 ">
                <div>
                    <a href="login.html"
                        class="peer text-red-500 hover:text-red-800"><x-icons.logout></x-icons.logout></a>
                    <p
                        class="fixed hidden peer-hover:block text-sm text-white bg-black p-2 dark:bg-slate-100 dark:text-black">
                        logout
                    </p>
                </div>
            </div>
        </header>

        <nav id="sidebar"
            class="bg-slate-50 z-20 top-14 sm:top-0  w-2/5 sm:w-52 shadow-md h-screen fixed sm:ml-0 transition-all duration-200 -ml-96 pt-5">

            {{-- section atas --}}
            <section class="flex justify-start gap-2 mx-auto w-[95%] card">
                <div class="bg-cover overflow-hidden w-11 h-11 rounded-full shadow-md bg-dark">
                    <img src="{{ asset('images/profil_dummy.png') }}" alt="profil_image" class="scale-100" />
                </div>

                <div class="flex flex-col text-left justify-center">
                    <h2 class="font-bold font-poppins text-xs tracking-wide">
                        Alvico
                    </h2>
                    <p class="text-[10px] tracking-wider font-light font-inter">
                        Admin Kepesertaan
                    </p>
                </div>

            </section>

            {{-- <section class="flex gap-2 justify-center mt-3 mb-2">
                <x-icons.sun class="icon-terang"></x-icons.sun>
                <div
                    class="toggle-dark-mode w-10 h-5 self-center shadow-md rounded-full  bg-slate-200 dark:bg-sky-900 cursor-pointer p-1">
                    <div class="h-full w-4 bg-slate-50 dark:bg-slate-300 rounded-full">

                    </div>
                </div>
                <x-icons.moon class="icon-gelap"></x-icons.mo>
            </section> --}}


            {{-- section menu --}}
            <div class="px-1 py-2 mt-5">
                <ul class="text-left text-slate-800 dark:text-slate-400 font-medium leading-6 text-xs">
                    <li class="menu-list menu-selected">
                        <x-icons.dashboard></x-icons.dashboard>
                        <p>Dashboard</p>
                    </li>
                    <li class="menu-list">
                        <x-icons.info></x-icons.info>
                            <p>Info PKS</p>
                    </li>
                    <li class="menu-list">
                        <x-icons.chart></x-icons.chart>
                            <p>Pembagian BU per RO</p>
                    </li>
                    <li class="menu-list">
                        <x-icons.chart-line></x-icons.chart-line>
                            <p>Pencapaian Wasrik</p>
                    </li>
                    <li class="menu-list">
                        <x-icons.report></x-icons.report>
                            <p>Rekonsiliasi PBPU Mandiri</p>
                    </li>
                    <li class="menu-list">
                        <x-icons.profile></x-icons.profile>
                            <p>Administrasi User</p>
                    </li>
                </ul>
            </div>

            {{-- section bawah --}}
            {{-- <div class="text-center px-3 bg-primary-biru">
                <p>Bagian Kepesertaan BPJS KC Palembang</p>
            </div> --}}
        </nav>

        <main class="w-screen">
            <section id="loader" class="sm:ml-52 p-3">
                <div
                    class="w-12 h-12 rounded-full border-yellow-500 dark:border-sky-700 border-8 border-t-8 border-t-slate-200 animate-spin relative top-14 mx-auto">
                </div>
            </section>

            <section id="container_halaman" class="sm:ml-52"></section>
        </main>
    </div>
</body>
<script src="{{ resource_path('js/app.js') }}"></script>

</html>
