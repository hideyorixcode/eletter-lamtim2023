<?php

namespace App\Jobs;

use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Services\SuratDisposisiServices;
use App\Services\SuratKeluarServices;
use App\Services\SuratMasukServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;

class SendNotificationDisposisi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    Instance surat Keluar \App\Models\SuratKeluar
    protected $suratMasuk;
    protected $userArray;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SuratMasuk $suratMasuk, $userArray)
    {
        //
        $this->suratMasuk = $suratMasuk;
        $this->userArray = $userArray;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SuratDisposisiServices $suratDisposisiServices)
    {
        //
        $users = $this->userArray;
        if (is_array($users)) {
            for ($i = 0; $i < count($users); $i++) {
                $user = User::where('id_opd_fk', $users[$i])->first();
                $suratDisposisiServices->send($this->suratMasuk, $user);
            }
        } else {
            $user = User::where('id_opd_fk', $this->userArray)->first();
            $suratDisposisiServices->send($this->suratMasuk, $user);
        }
    }

    public function middleware() {
        return [new ThrottlesExceptions(5, 5)];
    }

    public function retryUntil() {
        return now()->addMinutes(5);
    }
}
