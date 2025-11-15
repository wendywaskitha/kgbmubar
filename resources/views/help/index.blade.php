<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bantuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Pusat Bantuan Aplikasi KGB</h1>
                    
                    <p class="mb-6">Selamat datang di pusat bantuan aplikasi Kenaikan Gaji Berkala (KGB). Di sini Anda dapat menemukan informasi yang berguna tentang penggunaan aplikasi ini.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-3">FAQ</h3>
                            <p class="mb-4">Pertanyaan yang sering diajukan tentang proses KGB dan penggunaan aplikasi</p>
                            <a href="{{ route('help.faq') }}" class="text-blue-600 hover:underline">Lihat FAQ</a>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-3">Panduan Pengguna</h3>
                            <p class="mb-4">Langkah-langkah mengenai cara menggunakan aplikasi untuk mengajukan KGB</p>
                            <a href="{{ route('help.guide') }}" class="text-blue-600 hover:underline">Lihat Panduan</a>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-3">Video Tutorial</h3>
                            <p class="mb-4">Tonton video-video tutorial untuk mempelajari fitur-fitur aplikasi</p>
                            <a href="{{ route('help.videos') }}" class="text-blue-600 hover:underline">Lihat Video</a>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-3">Kontak Bantuan</h3>
                            <p class="mb-4">Hubungi tim dukungan kami jika Anda membutuhkan bantuan lebih lanjut</p>
                            <a href="mailto:help@kgbapp.test" class="text-blue-600 hover:underline">Kirim Email</a>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold mb-4">Fitur Utama Aplikasi</h2>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Pengajuan KGB secara online</li>
                            <li>Verifikasi otomatis dan manual</li>
                            <li>Pelacakan status pengajuan</li>
                            <li>Notifikasi real-time</li>
                            <li>Manajemen dokumen digital</li>
                            <li>Unduh SK KGB setelah disetujui</li>
                            <li>Laporan dan analitik</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>