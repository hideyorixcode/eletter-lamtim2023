<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Cache;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        /*View::composer(
            'profile', 'App\Http\View\Composers\ProfileComposer'
        );*/

        // Using Closure based composers...

        View::composer('*', function ($view) {
            $data = null;
            if (!Cache::has('settings')) {
                $setting = Settings::all();
                foreach ($setting as $s) {
                    $data[$s->setting_Key] = $s->setting_Value;
                }
                Cache::put('settings', $data, 3600);
                $view->with('settings', $data);
                //
            } else {
                $data = Cache::get('settings');
                $view->with('settings', $data);
            }
        });
    }
}
