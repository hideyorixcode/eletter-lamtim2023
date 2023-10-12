<?php

namespace App\Services;

use App\Mail\SendMail;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SuratMasukServices {
    public function send($suratMasuk, $user)
    {
        $email = $user->email;
        $template = 'mail.surat_masuk';
        $dataSurat = [
            'subject'   => 'Surat Masuk telah diterima!',
            'from_nama' => env('APP_NAME'),
            'surat'     => $suratMasuk
        ];

        Mail::to($email)->queue(new SendMail($template, $dataSurat));
    }
}
