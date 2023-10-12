<?php
namespace App\PDF;

use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;

class PDFFooter extends Fpdi
{

    private $data = [];
    protected $halaman;

    public function __construct($data = null, $halaman =1)
    {
        $this->data = $data;
        $this->halaman = $halaman;
        parent::__construct();
    }

    public function Footer()
    {
        // Position at 15 mm from bottom
        if ($this->page == $this->halaman) {
            $fontName = \TCPDF_FONTS::addTTFfont(Storage::path('bookman old style.ttf'));
            $this->setFont($fontName, '', 8);
            //$this->SetFontSize(5);
//            $this->SetTextColor(0, 0, 0);
            $this->SetXY(21, -19);
            if ($this->data['kategori_ttd'] == 'elektronik') {
                $this->writeHTMLCell(0, 10, 21, -13, '<p>Sesuai dengan ketentuan peraturan perundang-undangan yang berlaku,
dokumen ini telah ditandatangani secara elektronik menggunakan
sertifikat elektronik yang diterbitkan oleh BSrE sehingga tidak diperlukan tandatangan dengan stempel basah.</p>');
            } else {
                $this->writeHTMLCell(0, 10, 21, -19);
            }
//            $this->Write(0, 'Sesuai dengan ketentuan peraturan perundang-undangan yang berlaku, dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh BSrE sehingga tidak diperlukan tandatangan dengan stempel basah.', '', false, 'L', false, 0, false, true, 0, 0, '');
            $this->setY(-19);
            $this->Image(Storage::disk('kodeqr')->path($this->data['qr']), 3, '', 17, 17, 'PNG', $this->data['link'], '', false, 300, '', false, false, 0, false, false, false);
        }
    }
}

?>
