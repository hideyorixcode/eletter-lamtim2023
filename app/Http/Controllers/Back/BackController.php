<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Opd;
use App\Models\Post;
use App\Models\Topik;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BackController extends Controller
{
    //
    public function index()
    {
        $data = [

        ];
        return view('dashboard_page.dashboard', $data);
    }
}
