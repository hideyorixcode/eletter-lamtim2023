<?php

namespace App\Services;

use App\Mail\SendMail;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\PDF\PDFFooter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class SuratKeluarServices
{
    public function send($suratKeluar, $user)
    {
        $email = $user->email;
        $template = 'mail.surat_keluar';
        $dataSurat = [
            'subject' => 'Surat Keluar perlu ditandatangani secara elektronik!',
            'from_nama' => env('APP_NAME'),
            'surat' => $suratKeluar
        ];

        Mail::to($email)->queue(new SendMail($template, $dataSurat));
    }


    public function placeQRtoPDF($berkasPath, $qrCodeUrl, $surat)
    {

        $data['qr'] = $qrCodeUrl;
        $data['kategori_ttd'] = $surat->kategori_ttd;
        if ($surat->kategori_ttd == 'basah') {
            $data['link'] = url('surat-keluar/' . Hashids::encode($surat->id));
        } else {
            $data['link'] = url('surat-keluar-tte/' . Hashids::encode($surat->id));
        }
//        $pdf = new PDFFooter('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false, $data);

        if ($surat->kategori_ttd == 'elektronik') {
            $pdf = new PDFFooter($data, $surat->halaman);
            $pdf->setPrintHeader(false);
            $pages = $pdf->setSourceFile(Storage::disk('berkas')->path('temp/' . $berkasPath));
            for ($i = 1; $i <= $pages; $i++) {
                $pdf->AddPage();
                $page = $pdf->importPage($i);
                $pdf->useTemplate($page, 0, 0, null, null, true);
            }
            $pdf->Output(Storage::disk('berkas')->path('temp/' . $berkasPath), "F");
        } else {
            $pdf = new PDFFooter($data, 1);
            $pdf->setPrintHeader(false);
            $pages = $pdf->setSourceFile(Storage::disk('berkas')->path($berkasPath));
            for ($i = 1; $i <= $pages; $i++) {
                $pdf->AddPage();
                $page = $pdf->importPage($i);
                $pdf->useTemplate($page, 0, 0, null, null, true);
            }
            $pdf->Output(Storage::disk('berkas')->path($berkasPath), "F");
        }

    }
}
