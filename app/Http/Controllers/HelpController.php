<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Show the help page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('help.index');
    }

    /**
     * Show the FAQ page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function faq()
    {
        $faqs = [
            [
                'question' => 'Apa itu Kenaikan Gaji Berkala (KGB)?',
                'answer' => 'Kenaikan Gaji Berkala adalah kenaikan gaji yang diberikan kepada Pegawai Negeri Sipil (PNS) secara berkala berdasarkan masa kerja dan pangkatnya. KGB biasanya diberikan setiap 2 tahun sekali sesuai dengan peraturan yang berlaku.'
            ],
            [
                'question' => 'Bagaimana cara mengajukan KGB?',
                'answer' => 'Untuk mengajukan KGB, Anda perlu masuk ke menu Pengajuan KGB, lengkapi formulir pengajuan, unggah dokumen yang diperlukan, dan kirimkan pengajuan Anda. Pengajuan akan diproses sesuai dengan alur verifikasi yang berlaku.'
            ],
            [
                'question' => 'Berapa lama proses KGB biasanya selesai?',
                'answer' => 'Waktu proses KGB biasanya memakan waktu sekitar 7-14 hari kerja tergantung pada kelengkapan dokumen dan proses verifikasi di dinas terkait.'
            ],
            [
                'question' => 'Apa saja dokumen yang diperlukan untuk KGB?',
                'answer' => 'Dokumen yang biasanya diperlukan antara lain SK CPNS, SK PNS, SK KGB terakhir, SK Pangkat Terakhir, dan daftar riwayat hidup.'
            ],
            [
                'question' => 'Apa yang harus dilakukan jika pengajuan KGB ditolak?',
                'answer' => 'Jika pengajuan ditolak, silakan periksa keterangan penolakan, perbaiki dokumen yang diminta, dan ajukan kembali pengajuan Anda.'
            ],
            [
                'question' => 'Bagaimana cara mengunduh SK KGB setelah disetujui?',
                'answer' => 'Setelah pengajuan disetujui dan dinyatakan selesai, SK KGB akan tersedia untuk diunduh di menu SK KGB pada panel Anda.'
            ]
        ];

        return view('help.faq', compact('faqs'));
    }

    /**
     * Show the user guide page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function guide()
    {
        return view('help.guide');
    }

    /**
     * Show the video tutorials page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function videoTutorials()
    {
        $videos = \App\Models\VideoTutorial::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('help.videos', compact('videos'));
    }
}
