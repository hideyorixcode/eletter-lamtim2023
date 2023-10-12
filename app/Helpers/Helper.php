<?php

use App\Models\Logs;
use App\Models\PerangkatDaerah;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('assetku')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function assetku($path, $secure = null)
    {
        return app('url')->asset('public/' . $path, $secure);
    }
}
if (!function_exists('activeMenu')) {
    function activeMenu($uri = '')
    {
        $active = '';

        if (Request::is($uri . '/*') || Request::is($uri)) {
            $active = 'active';
        }
        return $active;
    }
}

if (!function_exists('activeSegment')) {
    function activeSegment($uri = '')
    {
        $active = '';

        if (Request::is($uri)) {
            $active = 'active';
        }
        return $active;
    }
}

if (!function_exists('getAvatarThumb')) {
    function getAvatarThumb($avatar)
    {
        if ($avatar) {
            return url('uploads/thumbnail/' . $avatar);
        } else {
            return url('uploads/blank.png');
        }
    }
}

if (!function_exists('getAvatar')) {
    function getAvatar($avatar)
    {
        if ($avatar) {
            return url('uploads/' . $avatar);
        } else {
            return url('uploads/blank.png');
        }
    }
}

if (!function_exists('getActive')) {
    function getActive($active)
    {
        if ($active == 1) {
            return 'text-success';
        } else {
            return 'text-danger';
        }
    }
}

if (!function_exists('getActiveTeks')) {
    function getActiveTeks($active)
    {
        if ($active == 1) {
            return 'AKTIF';
        } else {
            return 'TIDAK AKTIF';
        }
    }
}

if (!function_exists('getImageIconThumb')) {
    function getImageIconThumb($icon)
    {
        if ($icon) {
            return url('uploads/thumbnail/' . $icon);
        } else {
            return url('uploads/noicon.png');
        }
    }
}

if (!function_exists('getImageIcon')) {
    function getImageIcon($icon)
    {
        if ($icon) {
            return url('uploads/' . $icon);
        } else {
            return url('uploads/noicon.png');
        }
    }
}


if (!function_exists('activeSide')) {
    function activeSide($uri = '')
    {
        $active = '';

        if (Request::is($uri . '/*') || Request::is($uri)) {
            $active = 'active';
        }
        return $active;
    }
}

function ganti_titik_ke_koma($angka)
{
    return str_replace(".", ",", $angka);
}

function format_angka_indo($angka)
{
    $rupiah = number_format($angka, 0, ',', '.');
    return $rupiah;
}

function rubah_tanggal_indo($format_tgl)
{
    $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
    $tahun = substr($format_tgl, 0, 4);
    $bulan = substr($format_tgl, 5, 2);
    $tgl = substr($format_tgl, 8, 2);
    $tgl_indonesia = $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;

    return $tgl_indonesia;
}

//DATE INDONESIA BY FERI
if (!function_exists('date_indonesian')) {
    function date_indonesian($date, $format = 'd F Y')
    {
        $date = date_create($date);
        $array1 = array('January', 'February', 'March', 'May', 'June', 'July', 'August', 'October', 'December', 'Aug', 'Oct', 'Dec', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $array2 = array('Januari', 'Februari', 'Maret', 'Mei', 'Juni', 'Juli', 'Agustus', 'Oktober', 'Desember', 'Agu', 'Okt', 'Dec', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $new_date = date_format($date, $format);
        $hasil = str_replace($array1, $array2, $new_date);
        return $hasil;
    }
}

function TanggalIndowaktu($date)
{
    if ($date != '') {
        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl = substr($date, 8, 2);
        $waktu = substr($date, 11, 8);
        $result = $tgl . "/" . $bulan . "/" . $tahun . ' ' . $waktu;
        return ($result);
    } else {
        return '';
    }

}


function waktuaja($date)
{
    if ($date != '') {

        $waktu = substr($date, 11, 8);
        $result = $waktu;
        return ($result);
    } else {
        return '';
    }

}


function DateTimeFormatDB($date)
{
    if ($date != '') {
        $tahun = substr($date, 6, 4);
        $bulan = substr($date, 3, 2);
        $tgl = substr($date, 0, 2);
        $waktu = substr($date, 11, 8);
        $result = $tahun . "-" . $bulan . "-" . $tgl . ' ' . $waktu;
        return ($result);
    } else {
        return '0000-00-00';
    }

}

function TanggalIndoSimple($date)
{
    if ($date != '') {
        $BulanIndo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agu", "Sep", "Okt", "Nov", "Des");

        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl = substr($date, 8, 2);

        $result = $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;
        return ($result);
    } else {
        return '';
    }

}

function TanggalIndo($date)
{
    if ($date != '') {
        $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl = substr($date, 8, 2);

        $result = $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;
        return ($result);
    } else {
        return '';
    }

}

function TanggalIndo2($date)
{
    if ($date != '') {
        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl = substr($date, 8, 2);

        $result = $tgl . "/" . $bulan . "/" . $tahun;
        return ($result);
    } else {
        return '';
    }

}

function TanggalIndo3($date)
{
    if ($date != '') {
        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl = substr($date, 8, 2);

        $result = $tgl . "-" . $bulan . "-" . $tahun;
        return ($result);
    } else {
        return '';
    }

}

function TanggalIndoFormat($date, $format)
{
    if ($date != '') {
        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl = substr($date, 8, 2);

        $result = $tgl . $format . $bulan . $format . $tahun;
        return ($result);
    } else {
        return '';
    }

}

function BulanTahunAja($date)
{

    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);

    $result = $bulan . "/" . $tahun;
    return ($result);
}

function TanggalAja($date)
{

    $tgl = substr($date, 8, 2);

    $result = $tgl;
    return ($result);
}

function ubahformatTgl($tanggal)
{
    $pisah = explode('/', $tanggal);
    $urutan = array($pisah[2], $pisah[1], $pisah[0]);
    $satukan = implode('-', $urutan);
    return $satukan;
}

function replace_level_word($stringnya)
{
    $replace_text = str_replace("LEVEL ", "", $stringnya);
    return $replace_text;
}

if (!function_exists('assetRoot')) {

    function assetRoot($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}
if (!function_exists('tanggalWaktu')) {
    function tanggalWaktu($tanggal)
    {
        return Carbon::parse($tanggal)->isoFormat('Do MMMM YYYY, h:mm:ss a');
    }
}
if (!function_exists('Respon')) {
    function Respon($data, $status, $pesan, $statusCode)
    {

        $res['status'] = $status;
        $res['pesan'] = $pesan;
        $res['data'] = $data;

        return response()->json($res, $statusCode);
    }
}
if (!function_exists('setImage')) {
    function setImage($file, $dir)
    {
        $file_name = round(microtime(true) * 1000) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $file_name);
        return $file_name;
    }
}
if (!function_exists('tanggalIndo')) {
    function tanggalIndo($tanggal)
    {
        return Carbon::parse($tanggal)->format('d M Y');
    }
}

if (!function_exists('angkaTitikTiga')) {
    function angkaTitikTiga($var)
    {
        return number_format($var, 0, ',', '.');
    }
}
if (!function_exists('arrBulan')) {
    function arrBulan()
    {
        return array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    }
}

if (!function_exists('removeBaseURL')) {
    function removeBaseURL($string)
    {
        return str_replace(url('/'), '', $string);
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key)
    {
        $result = Settings::where('setting_Key', $key)->first();
        if ($result) {
            return $result->setting_Value;
        } else {
            return '';
        }
    }
}

if (!function_exists('saveLogs')) {
    function saveLogs($description)
    {
        $dataLog = [
            'log_Time' => date("Y-m-d H:i:s"),
            'log_IdUser' => Auth::user()->id,
            'log_Description' => Auth::user()->username . ' ' . $description,
        ];
        Logs::create($dataLog);
    }
}

if (!function_exists('detectDelimiter')) {
    function detectDelimiter($csvFile)
    {
        $delimiters = [";" => 0, "," => 0, "\t" => 0, "|" => 0];

        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
}

if (!function_exists('cekJenisOPD')) {
    function cekJenisOPD($id)
    {

        $cekOPD = \App\Models\PerangkatDaerah::where('id_opd', $id)->first();
        if ($cekOPD):
            return $cekOPD->jenis;
        else:
            return '';
        endif;

    }
}

if (!function_exists('cekIdBiroUmum')) {
    function cekIdBiroUmum()
    {

        $cekOPD = PerangkatDaerah::where('jenis', 'tu')->where('nama_opd', 'BAGIAN UMUM')->first();
        if ($cekOPD):
            return $cekOPD->id_opd;
        else:
            return '';
        endif;

    }
}

if (!function_exists('cekIdBiroAdpim')) {
    function cekIdBiroAdpim()
    {

        $cekOPD = PerangkatDaerah::where('jenis', 'tu')->where('nama_opd', 'BAGIAN PROTOKOL PIMPINAN')->first();
        if ($cekOPD):
            return $cekOPD->id_opd;
        else:
            return '';
        endif;

    }
}

if (!function_exists('cekIdPemprov')) {
    function cekIdPemprov()
    {

        $cekOPD = PerangkatDaerah::where('jenis', 'opd')->where('nama_opd', 'PEMERINTAH PROVINSI LAMPUNG')->first();
        if ($cekOPD):
            return $cekOPD->id_opd;
        else:
            return '';
        endif;

    }
}

if (!function_exists('cekMelaluiIdOpd')) {
    function cekMelaluiIdOpd($id)
    {
        $cekOPD = PerangkatDaerah::where('jenis', 'tu')->where('id_opd', $id)->first();
        if ($cekOPD):
            if ($cekOPD->nama_opd == 'BAGIAN PROTOKOL PIMPINAN') {
                return cekIdBiroUmum();
            } else if ($cekOPD->nama_opd == 'BAGIAN UMUM') {
                //return cekIdBiroAdpim();
                return NULL;
            } else {
                return NULL;
            }
        else:
            return NULL;
        endif;

    }
}

if (!function_exists('cek_opd')) {
    function cek_opd($id_opd)
    {

        $cekOPD = PerangkatDaerah::where('id_opd', $id_opd)->first();
        if ($cekOPD):
            return $cekOPD;
        else:
            return '';
        endif;

    }
}

function getJumlahJenisTTD($id_jenis_ttd_fk, $kategori_ttd, $tahun, $bulan)
{

    if ($tahun != null) {
        if ($bulan != null) {
            $periode = $bulan . '-' . $tahun;
            $datanya = \App\Models\ViewModel\v_bar_penandatangan::where('periode', $periode)->groupBy('id_jenis_ttd_fk')->groupBy('kategori_ttd');
        } else {
            $datanya = \App\Models\ViewModel\v_bar_ttd_tahunan::where('periode', $tahun)->groupBy('id_jenis_ttd_fk')->groupBy('kategori_ttd');
        }
    } else {
        $datanya = \App\Models\ViewModel\v_bar_ttd_tahunan::selectRaw('SUM(jumlah) AS jumlah, id_jenis_ttd_fk, jenis_ttd, kategori_ttd')->groupBy('id_jenis_ttd_fk')->groupBy('kategori_ttd');
    }


    $hasil = $datanya->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->where('kategori_ttd', $kategori_ttd);
    $cekJumlah = clone $hasil;
    $hasilnya = clone $hasil;
    if ($cekJumlah->count() == 0) {
        return 0;
    }
    return ($hasilnya->first()->jumlah);
}

if (!function_exists('cekIdBKD')) {
    function cekIdBKD()
    {

        $cekOPD = PerangkatDaerah::where('jenis', 'opd')->where('nama_opd', 'BADAN KEPEGAWAIAN DAERAH')->first();
        if ($cekOPD):
            return $cekOPD->id_opd;
        else:
            return '';
        endif;

    }
}

if (!function_exists('cekUnkerSimpedu')) {
    function cekUnkerSimpedu($idopd)
    {

        $cekOPD = PerangkatDaerah::where('T_KUnker', '!=', '')->where('id_opd', $idopd)->first();
        if ($cekOPD):
            return true;
        else:
            return false;
        endif;

    }
}

if (!function_exists('cek_ttd')) {
    function cek_ttd($id_jenis_ttd)
    {

        $cekOPD = \App\Models\JenisPenandatangan::where('id_jenis_ttd', $id_jenis_ttd)->first();
        if ($cekOPD):
            return $cekOPD;
        else:
            return '';
        endif;

    }
}

function angkatobilang($angka)
{
    $formatangkaterbilangindo = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);
    return $formatangkaterbilangindo->format($angka);

}

function sendfirebasemessage($eloquentfirebase, $title, $body)
{
    //$firebaseToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();

    $SERVER_API_KEY = env('FCM_SERVER_KEY');

    $data = [
        "registration_ids" => $eloquentfirebase,
        "notification" => [
            "title" => $title,
            "body" => $body,
        ]
    ];
    $dataString = json_encode($data);

    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_exec($ch);
}

function getnamaunker($kode)
{
    return DB::connection('mysql_2')->table('unkerja')->where('KUnKer', $kode)->first();
}


if (!function_exists('random_strings')) {
    function random_strings($length_of_string)
    {
        // return substr(sha1(time()), 0, $length_of_string);
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shuffle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result),
            0, $length_of_string);
    }
}
