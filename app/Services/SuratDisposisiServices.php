<?php

namespace App\Services;

use App\Mail\SendMail;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use App\PDF\PDFFooter;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SuratDisposisiServices {
    public function send($disposisi, $user)
    {
        $email = $user->email;
        $template = 'mail.surat_disposisi';
        $dataSurat = [
            'subject'   => 'Surat Disposisi telah diterima!',
            'from_nama' => env('APP_NAME'),
            'surat'     => $disposisi
        ];

        Mail::to($email)->queue(new SendMail($template, $dataSurat));
    }

}
