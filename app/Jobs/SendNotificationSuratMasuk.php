<?php

namespace App\Jobs;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Services\SuratKeluarServices;
use App\Services\SuratMasukServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;

class SendNotificationSuratMasuk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    Instance surat Keluar \App\Models\SuratKeluar
    protected $suratMasuk;
    protected $kepada;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SuratMasuk $suratMasuk, $kepada = null)
    {
        //
        $this->suratMasuk = $suratMasuk;
        $this->kepada = $kepada;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SuratMasukServices $suratMasukServices)
    {
        //
        if ($this->suratMasuk->sifat_surat == 'langsung') {
            $user = User::active()->where('level', 'umum')->first();
        } else if ($this->suratMasuk->sifat_surat == 'rahasia') {
            $user = User::where('id_opd_fk', $this->kepada)->first();
        } else  {
            $user = User::active()->where('level', 'adpim')->first();
        }
        $suratMasukServices->send($this->suratMasuk, $user);
    }

    public function middleware() {
        return [new ThrottlesExceptions(5, 5)];
    }

    public function retryUntil() {
        return now()->addMinutes(5);
    }
}
