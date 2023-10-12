<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Logs;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class LogsController extends Controller
{
    //
    public function index()
    {
        $data = [
            'listUser' => User::pluck('id', 'username')->all()
        ];
        return view('dashboard_page.logs.index', $data);
    }

    public function data(Request $request)
    {
        $data = Logs::select('*');
        $log_IdUser = $request->get('log_IdUser');
        if ($log_IdUser != '') :
            $data = $data->where('log_IdUser', $log_IdUser);
        endif;
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->log_Id) . '" class="data-check">';
                return $checkbox;
            })
            ->editColumn('log_Id', function ($row) {
                return Hashids::encode($row->log_Id);
            })
            ->editColumn('log_Time', function ($row) {
                return TanggalIndowaktu($row->log_Time);
            })
            ->addColumn('action', function ($row) {
                //btn btn-text-dark-50 btn-icon-primary btn-hover-icon-danger font-weight-bold btn-hover-bg-light mr-3

                $btn = '<div class="btn-group" role="group" aria-label="First group">';
                $btn .= '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->log_Id) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            //->rawColumns(['action'])
            //->make(true);
            ->escapeColumns([])
            ->toJson();
    }

    public function destroy($id)
    {
        $idDecode = Hashids::decode($id);
        $dataMaster = Logs::find($idDecode)[0];
        if ($dataMaster) :
            $delete = Logs::destroy($idDecode);
            if ($delete) :
                return Respon('', true, 'Berhasil menghapus data', 200);
            else :
                return Respon('', false, 'Gagal menghapus data', 200);
            endif;
        else :
            return Respon('', false, 'Gagal menghapus data', 200);
        endif;
    }

    public function bulkDelete(Request $request)
    {
        $list_id = $request->input('id');
        foreach ($list_id as $getId) {
            $idDecode = Hashids::decode($getId);
            $dataMaster = Logs::find($idDecode)[0];
            if ($dataMaster) :
                Logs::destroy($idDecode);
            endif;
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function logsPengguna()
    {
        return view('dashboard_page.pengguna.logs');
    }

    public function logsData(Request $request)
    {
        if ($request->ajax()) {
            $dataLogs = Logs::where('log_IdUser', Auth::user()->id)->orderBy('log_Time', 'DESC')->paginate(10);
            $data = [
                "listLog" => $dataLogs
            ];
            return view('dashboard_page.pengguna.dataLogs', $data)->render();
        } else {
            return false;
        }
    }
}
