<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panduan Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Panduan Pengguna Aplikasi KGB</h1>
                    
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold mb-4">1. Login ke Aplikasi</h2>
                        <ol class="list-decimal pl-6 space-y-2">
                            <li>Buka browser dan akses alamat aplikasi</li>
                            <li>Masukkan email dan password Anda</li>
                            <li>Klik tombol "Login"</li>
                        </ol>
                    </div>
                    
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold mb-4">2. Mengajukan KGB</h2>
                        <ol class="list-decimal pl-6 space-y-2">
                            <li>Pilih menu "Pengajuan KGB" di sidebar</li>
                            <li>Klik tombol "Ajukan KGB Baru"</li>
                            <li>Isi formulir pengajuan dengan data yang benar</li>
                            <li>Unggah dokumen-dokumen yang diperlukan</li>
                            <li>Klik tombol "Kirim Pengajuan"</li>
                        </ol>
                    </div>
                    
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold mb-4">3. Melacak Status Pengajuan</h2>
                        <ol class="list-decimal pl-6 space-y-2">
                            <li>Buka menu "Pengajuan Saya" atau "Semua Pengajuan"</li>
                            <li>Temukan pengajuan yang ingin Anda lacak</li>
                            <li>Lihat status terkini dan catatan verifikasi</li>
                        </ol>
                    </div>
                    
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold mb-4">4. Mengunduh SK KGB</h2>
                        <ol class="list-decimal pl-6 space-y-2">
                            <li>Buka menu "SK KGB" setelah pengajuan disetujui</li>
                            <li>Cari SK KGB yang tersedia untuk Anda</li>
                            <li>Klik tombol "Unduh" atau "Pratinjau" untuk melihat dokumen</li>
                        </ol>
                    </div>
                    
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold mb-4">5. Mengelola Profil</h2>
                        <ol class="list-decimal pl-6 space-y-2">
                            <li>Klik avatar Anda di pojok kanan atas</li>
                            <li>Pilih "Profil" dari menu dropdown</li>
                            <li>Perbarui informasi profil Anda sesuai kebutuhan</li>
                            <li>Jangan lupa klik "Simpan" setelah selesai</li>
                        </ol>
                    </div>
                    
                    <div class="mt-8 p-4 bg-yellow-50 rounded-lg">
                        <h4 class="text-lg font-semibold mb-2">Tips Menggunakan Aplikasi</h4>
                        <ul class="list-disc pl-6 space-y-1">
                            <li>Periksa email Anda secara berkala untuk notifikasi penting</li>
                            <li>Selalu lengkapi data dengan benar untuk mempercepat proses</li>
                            <li>Simpan dokumen dalam format PDF atau JPEG dengan ukuran maksimal 10MB</li>
                            <li>Jika menemukan masalah teknis, laporkan segera ke tim IT</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>