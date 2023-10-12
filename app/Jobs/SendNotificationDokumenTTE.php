<?php

namespace App\Jobs;

use App\Models\DokumenTTE;
use App\Models\User;
use App\Services\DokumenTTEServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;

class SendNotificationDokumenTTE implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    Instance surat Keluar \App\Models\DokumenTTE
    protected $dokumenTTE;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DokumenTTE $dokumenTTE)
    {
        //
        $this->dokumenTTE = $dokumenTTE;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DokumenTTEServices $dokumenTTEServices)
    {
        //
        $user = User::where('id_jenis_ttd_fk', $this->dokumenTTE->id_jenis_ttd_fk)->where('level', 'penandatangan')->first();
        $dokumenTTEServices->send($this->dokumenTTE, $user);
    }

    public function middleware() {
        return [new ThrottlesExceptions(5, 5)];
    }

    public function retryUntil() {
        return now()->addMinutes(5);
    }
}
