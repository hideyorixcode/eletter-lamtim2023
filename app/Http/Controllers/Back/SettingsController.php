<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{


    public function index()
    {
        $data = [
            'settingsText' => Settings::whereIn('setting_Type', ['textfield', 'email', 'number'])->get(),
            'settingsTextarea' => Settings::where('setting_Type', 'textarea')->get(),
            'settingsGambar' => Settings::whereIn('setting_Type', ['gambar', 'favicon'])->get(),
        ];

        return view('dashboard_page.settings.index', $data);
    }

    public function updateAll(Request $request)
    {
        $setting_Id = $request->input('setting_Id');
        $idberkas = $request->input('idberkas');
        $setting_Typeberkas = $request->input('setting_Typeberkas');
        if ($idberkas) :
            for ($i = 0; $i < sizeof($idberkas); $i++) {
                $setting_Valueberkas = $request->input('setting_Valueberkas');
                $arrayfilenya = 'berkas_' . $i;

                if ($request->hasFile($arrayfilenya)) {
                    if ($setting_Typeberkas[$i] == 'gambar') {
                        $this->validate($request, [
                            $arrayfilenya => 'max:1024|mimes:png,jpg,gif,JPG,jpeg,JPEG',
                        ]);
                    } else if ($setting_Typeberkas[$i] == 'favicon') {
                        $this->validate($request, [
                            $arrayfilenya => 'max:200|mimes:ico',
                        ]);
                    }
                    $image = $request->file($arrayfilenya);
                    $namePicture = round(microtime(true) * 1000) . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/', $namePicture);
                    $setting_ValueAwal = $namePicture;
                } else {
                    $setting_ValueAwal = $setting_Valueberkas[$i];
                }

                $updateBerkas = [
                    'setting_Id' => ($idberkas[$i]),
                    'setting_Value' => $setting_ValueAwal,
                ];
                $dataMasterBerkas = Settings::find($idberkas[$i]);
                $updateFile = $dataMasterBerkas->update($updateBerkas);
            }
        endif;


        for ($x = 0; $x < sizeof($setting_Id); $x++) {
            $setting_Value = $request->input('setting_Value');
            $setting_Valuenya = $setting_Value[$x];

            $updateArray = [
                'setting_Id' => ($setting_Id[$x]),
                'setting_Value' => $setting_Valuenya,
            ];
            $dataMaster = Settings::find($setting_Id[$x]);
            $updateText = $dataMaster->update($updateArray);
        }

        if ($updateText || $updateFile) :
            saveLogs('berhasil ubah konfigurasi pada fitur setting aplikasi');
            return redirect(route('settings'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Settings Aplikasi berhasil diupdate',
                    'judul' => 'Settings'
                ]);
        else:
            return redirect(route('settings'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Settings Aplikasi gagal diupdate',
                    'judul' => 'Settings'
                ]);
        endif;


    }
}
