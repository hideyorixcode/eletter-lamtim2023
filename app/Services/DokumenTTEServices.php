<?php

namespace App\Services;

use App\Mail\SendMail;
use App\Models\User;
use App\PDF\PDFFooter;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class DokumenTTEServices {
    public function send($dokumenTTE, $user)
    {
        $email = $user->email;
        $template = 'mail.dokumen_tte';
        $dataSurat = [
            'subject'   => 'Dokumen perlu ditandatangani!',
            'from_nama' => env('APP_NAME'),
            'dokumen'     => $dokumenTTE
        ];

        Mail::to($email)->queue(new SendMail($template, $dataSurat));
    }


    public function placeQRtoPDF($berkasPath, $qrCodeUrl, $dokumen) {

        $data['qr'] = $qrCodeUrl;
        $data['link'] = url('dokumen-tte/'. Hashids::encode($dokumen->id));
        $data['kategori_ttd'] = 'elektronik';
//        $pdf = new PDFFooter('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false, $data);
        $pdf = new PDFFooter($data);
        $pdf->setPrintHeader(false);
        $pages = $pdf->setSourceFile(Storage::disk('berkas')->path('temp/' . $berkasPath));

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0, null, null, true);
        }

        $pdf->Output(Storage::disk('berkas')->path('temp/'.$berkasPath), "F");
    }
}
