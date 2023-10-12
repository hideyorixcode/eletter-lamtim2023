<?php

namespace App\Jobs;

use App\Models\SuratKeluar;
use App\Models\User;
use App\Services\SuratKeluarServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;

class SendNotificationSuratKeluar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    Instance surat Keluar \App\Models\SuratKeluar
    protected $suratKeluar;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SuratKeluar $suratKeluar)
    {
        //
        $this->suratKeluar = $suratKeluar;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SuratKeluarServices $suratKeluarServices)
    {
        //
        $user = User::where('id_jenis_ttd_fk', $this->suratKeluar->id_jenis_ttd_fk)->where('level', 'penandatangan')->first();
        $suratKeluarServices->send($this->suratKeluar, $user);
    }

    public function middleware() {
        return [new ThrottlesExceptions(5, 5)];
    }

    public function retryUntil() {
        return now()->addMinutes(5);
    }
}
