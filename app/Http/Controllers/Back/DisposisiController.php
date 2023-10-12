<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationDisposisi;
use App\Models\Disposisi;
use App\Models\PerangkatDaerah;
use App\Models\SuratMasuk;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class DisposisiController extends Controller
{

    public function getpenerima(Request $request, $id)
    {
        //if ($request->ajax()) {
        $id_sm_fk = Hashids::decode($id)[0];

        $cekpenerima = Disposisi::where('id_sm_fk', $id_sm_fk)->orderBy('tgl_masuk', 'DESC')->first()->kepada;
        if ($cekpenerima != '') {
            return $cekpenerima;
        } else {
            return '';
        }
        // }
    }

    public function destroy($id)
    {
        //$this->destroyFunction($id, Disposisi::class, '', '', 'disposisi', '', '');
        $delete = Disposisi::destroy($id);
        if ($delete) :
            return Respon('', true, 'Berhasil membatalkan disposisi', 200);
        else :
            return Respon('', false, 'Gagal membatalkan disposisi', 500);
        endif;
//        if (true):
//            return Respon('', true, 'Berhasil menghapus data', 200);
//        else:
//            return Respon('', false, 'Gagal menghapus data', 500);
//        endif;
    }

    public function bulkDelete(Request $request)
    {
        $list_id = $request->input('id');
        foreach ($list_id as $id) {
            $this->destroyFunction($id, Disposisi::class, '', '', 'disposisi', '', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function create(Request $request)
    {
        $id = $request->input('id');
        $dataSimpan = [];
        $dataMelalui = [];
        $dataLangsung = [];
        if (isset($id)):
            DB::beginTransaction();
            for ($i = count($id); $i >= 1; $i--) {
                $id_sm_fk = Hashids::decode($request->input('id_sm_fk'))[0];
                $tgl_diterima = $request->input('tgl_diterima_' . $i);
                if ($tgl_diterima != '') {
                    $tgl_diterimanya = DateTimeFormatDB($tgl_diterima);
                } else {
                    $tgl_diterimanya = null;
                }
                $penerima = $request->input('penerima_' . $i);
                $nama_penerima = $request->input('nama_penerima_' . $i);
                $status = $request->input('status_' . $i);
                if ($status == 'diteruskan') {
                    $kepada = $request->input('kepada_' . $i);
                    if (isset($kepada)):
                        $kepadaKoma = implode(',', $kepada);
                    else :
                        $kepadaKoma = null;
                    endif;


                    $melalui_id_opd = $request->input('melalui_id_opd_' . $i);
                    $catatan_disposisi = $request->input('catatan_disposisi_' . $i);
                    array_push($dataSimpan, [
                        'id_sm_fk' => $id_sm_fk,
                        'tgl_diterima' => $tgl_diterimanya,
                        'penerima' => $penerima,
                        'nama_penerima' => $nama_penerima,
                        'status' => $status,
                        'kepada' => $kepadaKoma,
                        'melalui_id_opd' => $melalui_id_opd,
                        'catatan_disposisi' => $catatan_disposisi,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    if ($i == 1) :
                        if ($melalui_id_opd != '') :
                            $listKepada_taksearah = '';
                            if (isset($kepada)):
                                for ($x = 0; $x < count($kepada); $x++) {
                                    if (cekJenisOPD($kepada[$x]) == cekJenisOPD($penerima)):
                                        $dataKepadaLangsung = array(
                                            'penerima' => $kepada[$x],
                                            'id_sm_fk' => $id_sm_fk,
                                            'status' => 'diolah',
                                            'created_by' => Auth::user()->id,
                                        );
                                        array_push($dataLangsung, $dataKepadaLangsung);
                                    else:
                                        $listKepada_taksearah .= $kepada[$x] . ',';
                                    endif;
                                }
                                $listkepada_taksearah_fix = rtrim($listKepada_taksearah, ',');
                                $melalui_id_opd_lagi = cekMelaluiIdOpd($melalui_id_opd);
                                array_push($dataMelalui, [
                                    //$dataMelalui = [
                                    'id_sm_fk' => $id_sm_fk,
                                    'penerima' => $melalui_id_opd,
                                    'kepada' => $listkepada_taksearah_fix,
                                    'melalui_id_opd' => $melalui_id_opd_lagi,
                                    'status' => 'diteruskan',
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            endif;

//                            Disposisi::create($dataMelalui);
                        else:
                            //$dataMelalui = [
                            if (isset($kepada)):
                                for ($x = 0; $x < count($kepada); $x++) {

                                    if (in_array(cekJenisOPD($kepada[$x]), ['pimpinan daerah', 'sekretariat daerah'])) {
                                        //$penerima = PerangkatDaerah::where('jenis', 'tu')->where('nama_opd', 'Bagian Umum')->first()->id_opd;
                                        $melalui_id_opd_continue = cekIdBiroAdpim();
                                        $status_continue = 'diteruskan';
                                    } else {
                                        $melalui_id_opd_continue = cekMelaluiIdOpd($kepada[$x]);
                                        $status_continue = 'diolah';
                                    }
                                    $dataKepada = array(
                                        'penerima' => $kepada[$x],
                                        'id_sm_fk' => $id_sm_fk,
                                        'melalui_id_opd' => $melalui_id_opd_continue,
                                        'status' => $status_continue,
                                        'created_by' => Auth::user()->id,
                                    );
                                    //Disposisi::create($dataKepada);
                                    array_push($dataMelalui, $dataKepada);
                                }
                            endif;
                        endif;

                    endif;
                } else {
                    array_push($dataSimpan, [
                        'id_sm_fk' => $id_sm_fk,
                        'tgl_diterima' => $tgl_diterimanya,
                        'penerima' => $penerima,
                        'nama_penerima' => $nama_penerima,
                        'status' => $status,
                        'catatan_disposisi' => '',
                        'melalui_id_opd' => null,
                        'kepada' => null,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            try {
                Disposisi::where('id_sm_fk', Hashids::decode($request->input('id_sm_fk')))->delete();
                Disposisi::insert($dataSimpan);
                Disposisi::insert($dataMelalui);
                Disposisi::insert($dataLangsung);
            } catch (QueryException $e) {
                DB::rollBack();
                return Respon('', false, 'Gagal simpan perubahan disposisi', 200);
            }
            saveLogs('mengubah data pada fitur disposisi');
            DB::commit();
            return Respon('', true, 'Berhasil simpan perubahan disposisi', 200);

//            });

        else:
            return Respon('', false, 'Gagal simpan perubahan disposisi', 200);
        endif;

    }


    public function edit($id)
    {
        $checkData = Disposisi::find(Hashids::decode($id))[0];
        echo json_encode($checkData);
    }

    public function update(Request $request)
    {
        $iditerasi = $request->input('id');
        //dd(count($iditerasi), $request->all());
        if (isset($iditerasi)):
            $id_sm_fk = Hashids::decode($request->input('id_sm_fk'))[0];
            for ($i = 1; $i <= count($iditerasi); $i++) {
                $id = $request->input('id_' . $i);
                $mode = $request->input('mode_' . $i);
                $tgl_diterima = $request->input('tgl_diterima_' . $i);
                //dd($tgl_diterima, $i);
                if ($tgl_diterima != '') {
                    $tgl_diterimanya = DateTimeFormatDB($tgl_diterima);
                } else {
                    $tgl_diterimanya = null;
                }
                $penerima = $request->input('penerima_' . $i);
                $nama_penerima = $request->input('nama_penerima_' . $i);
                $catatan_disposisi = $request->input('catatan_disposisi_' . $i);
                $dataMaster = Disposisi::find($id);
                if ($mode == 'tambah') {
                    $status = $request->input('status_' . $i);
                    if ($status == 'diteruskan') {
                        $kepada = $request->input('kepada_' . $i);
                        if (isset($kepada)):
                            $kepadaKoma = implode(',', $kepada);
                        else :
                            $kepadaKoma = null;
                        endif;

                        $melalui_id_opd = $request->input('melalui_id_opd_' . $i);

                        $dataUpdate = [
                            'id_sm_fk' => $id_sm_fk,
                            'tgl_diterima' => $tgl_diterimanya,
                            'penerima' => $penerima,
                            'nama_penerima' => $nama_penerima,
                            'status' => $status,
                            'kepada' => $kepadaKoma,
                            'melalui_id_opd' => $melalui_id_opd,
                            'catatan_disposisi' => $catatan_disposisi,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ];
                        $dataMaster->update($dataUpdate);

                        if ($melalui_id_opd != '') :
                            $listKepada_taksearah = '';
                            if (isset($kepada)):
                                for ($x = 0; $x < count($kepada); $x++) {
                                    if (cekJenisOPD($kepada[$x]) == cekJenisOPD($penerima)):
                                        $dataKepadaLangsung = array(
                                            'penerima' => $kepada[$x],
                                            'id_sm_fk' => $id_sm_fk,
                                            'status' => 'diolah',
                                            'created_by' => Auth::user()->id,
                                        );
                                        Disposisi::create($dataKepadaLangsung);
                                    else:
                                        $listKepada_taksearah .= $kepada[$x] . ',';
                                    endif;
                                }
                                $listkepada_taksearah_fix = rtrim($listKepada_taksearah, ',');
                                $melalui_id_opd_lagi = cekMelaluiIdOpd($melalui_id_opd);
                                $dataMelalui = [
                                    //$dataMelalui = [
                                    'id_sm_fk' => $id_sm_fk,
                                    'penerima' => $melalui_id_opd,
                                    'kepada' => $listkepada_taksearah_fix,
                                    'melalui_id_opd' => $melalui_id_opd_lagi,
                                    'status' => 'diteruskan',
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ];
                                Disposisi::create($dataMelalui);
                            endif;
                        else:

                            if (isset($kepada)):
                                for ($x = 0; $x < count($kepada); $x++) {
                                    if (in_array(cekJenisOPD($kepada[$x]), ['pimpinan daerah', 'sekretariat daerah'])) {
                                        $melalui_id_opd_continue = cekIdBiroAdpim();
                                        $status_continue = 'diteruskan';
                                    } else {
                                        $melalui_id_opd_continue = cekMelaluiIdOpd($kepada[$x]);
                                        $status_continue = 'diolah';
                                    }
                                    $dataKepada = array(
                                        'penerima' => $kepada[$x],
                                        'id_sm_fk' => $id_sm_fk,
                                        'melalui_id_opd' => $melalui_id_opd_continue,
                                        'status' => $status_continue,
                                        'created_by' => Auth::user()->id,
                                    );
                                    Disposisi::create($dataKepada);
                                }
                            endif;
                        endif;
                        $suratMasuk = SuratMasuk::find($dataMaster->id_sm_fk);
                        if ($melalui_id_opd == null)
                            SendNotificationDisposisi::dispatchAfterResponse($suratMasuk, $kepada);
                        else
                            SendNotificationDisposisi::dispatchAfterResponse($suratMasuk, $melalui_id_opd);

                    } else {
                        $dataUpdate = [
                            'id_sm_fk' => $id_sm_fk,
                            'tgl_diterima' => $tgl_diterimanya,
                            'penerima' => $penerima,
                            'nama_penerima' => $nama_penerima,
                            'status' => $status,
                            'catatan_disposisi' => '',
                            'melalui_id_opd' => null,
                            'kepada' => null,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ];
                        $dataMaster->update($dataUpdate);
                    }
                } else {
                    $dataUpdate = [
                        'id_sm_fk' => $id_sm_fk,
                        'tgl_diterima' => $tgl_diterimanya,
                        'nama_penerima' => $nama_penerima,
                        'catatan_disposisi' => $catatan_disposisi,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ];
                    $dataMaster->update($dataUpdate);
                }
            }
            saveLogs('mengubah data pada fitur disposisi');
            return Respon('', true, 'Berhasil simpan perubahan disposisi', 200);

        else:
            return Respon('', false, 'Gagal simpan perubahan disposisi', 200);
        endif;

    }

    public function data(Request $request, $id)
    {
        if ($request->ajax()) {
            // $id_sm_fk = $request->get('id_sm_fk');
            $data = [
                "listDisposisi" => Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->orderBy('id', 'DESC')->get(),
                'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                'listTu' => PerangkatDaerah::where('jenis', 'tu')->pluck('id_opd', 'nama_opd')->all(),
                'id_sm_fk' => $id,
            ];
            if (Auth::user()->level == 'superadmin') {
                //$view = 'dashboard_page.suratmasuk.datadisposisi';
//                $view = 'dashboard_page.suratmasuk.datadisposisi';
//                $data['bolehSimpan'] = true;
                $view = 'dashboard_page.suratmasuk.datadisposisiuser';
                $data['bolehSimpan'] = false;
            } else {
                $cekPenerima = Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->where('penerima', Auth::user()->id_opd_fk)->get();
                if (count($cekPenerima) > 0) {
                    $data['bolehSimpan'] = true;
                } else {
                    $data['bolehSimpan'] = false;
                }
                $view = 'dashboard_page.suratmasuk.datadisposisiuser';
            }
            return view($view, $data)->render();
        } else {
            return false;
        }
    }


}
