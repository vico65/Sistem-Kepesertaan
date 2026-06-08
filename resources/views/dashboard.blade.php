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
                    Kepesertaan <span class="text-primary-hijau">KC Palembang</span>
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
            class="bg-slate-50 z-20 top-14 sm:top-0  w-2/5 sm:w-52 shadow-md h-screen fixed sm:ml-0 transition-all duration-200 -ml-96">
            <section class="bg-primary-biru  mb-1 p-3 shadow-md">
                <div
                    class="bg-cover overflow-hidden xl:w-20 xl:h-20 w-16 h-16 rounded-full shadow-md self-center mx-auto mb-2">
                    <img src="{{ asset('images/profil_dummy.png') }}" alt="profil_image" class="scale-100" />
                </div>

                <div class="flex flex-col flex-wrap">
                    <h2 class="text-center font-medium font-poppins text-white text-base sm:text-[18px] ">
                        Alvico
                    </h2>
                    <p class="text-center text-xs tracking-wide text-slate-300 font-light font-inter">
                        Admin Kepesertaan
                    </p>
                </div>

            </section>

            <section class="flex gap-2 justify-center mt-3 mb-2">
                <i class="icon-terang bi bi-brightness-low text-yellow-500 text-xl"></i>
                <div
                    class="toggle-dark-mode w-10 h-5 self-center shadow-md rounded-full  bg-slate-200 dark:bg-sky-900 cursor-pointer p-1">
                    <div class="h-full w-4 bg-slate-50 dark:bg-slate-300 rounded-full">

                    </div>
                </div>
                <i class="icon-gelap bi bi-moon-stars text-slate-400  self-center"></i>
            </section>


            <div class="px-2 py-2">
                <ul class="text-left text-slate-800 dark:text-slate-400 font-medium leading-6 text-sm">
                    <li id="pointer_home"
                        class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                        <i class="bi bi-house-door-fill"></i> Halaman Depan
                    </li>
                    <li id="pointer_bio"
                        class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                        <i class="bi bi-person-fill"></i> Data Pribadi
                    </li>
                    <li id="pointer_akademik"
                        class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                        <i class="bi bi-book"></i> Data Akademik
                    </li>

                    <ul
                        class="list_akademik_pages hidden text-left text-slate-600 dark:text-slate-500 ml-4 font-normal leading-6 text-xs">
                        <li id="pointer_krs"
                            class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                            Kartu Studi Mahasiswa
                        </li>
                        <li id="pointer_khs"
                            class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                            Kartu Hasil Studi
                        </li>
                        <li id="pointer_tn"
                            class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                            Transkrip Nilai
                        </li>
                    </ul>

                    <li id="pointer_suliet"
                        class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                        <i class="bi bi-newspaper"></i> SULIET / USEPT
                    </li>
                    <li id="pointer_kkn"
                        class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                        <i class="bi bi-briefcase-fill"></i> Pendaftaran KKN
                    </li>
                    <li id="pointer_password"
                        class="cursor-pointer hover:text-white hover:bg-yellow-500 dark:hover:bg-sky-900 dark:hover:text-slate-300  hover:p-1">
                        <i class="bi bi-key-fill"></i> Setting Password
                    </li>
                </ul>
            </div>

            <div class="text-center text-[9px] dark:text-gray-500 text-gray-400 absolute bottom-3 px-3">
                <p>UNIVERSITAS SRIWIJAYA ACADEMIC INFORMATION SYSTEM </p>
            </div>
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
