<?php

use Ebansos\core\Controller;
use Ebansos\core\Flasher;

class Beranda extends Controller
{
    protected $judul = 'Beranda';
    public function index()
    {
        $data['judul'] = $this->judul;
        $data['navActive'] = 'beranda';
        $data['nikVal'] =  $data['hhVal'] = $data['bbVal'] = $data['ttttVal'] = '';
        $data['statusRegistrasi'] = $this->model('AdministrasiModel')->getStatusRegister()['registrasi'];
        if (isset($_SESSION['input'])) {
            $data = array_merge($data, $_SESSION['input']);
            unset($_SESSION['input']);
        }

        // Arrange HTML
        $this->view('templates/header', $data);
        $this->view('templates/navUmum', $data);
        $this->view('beranda/index', $data);
        $this->view(('templates/footer'));
    }

    // Methods that could be accessed without login
    public function cekData()
    {
        if (!empty($_POST)) {
            $data = $_POST;
            $data['status'] = 'success';
            $data['tanggalLahir'] = "{$data['tahun']}-{$data['bulan']}-{$data['hari']}";
            $data['penduduk'] = $this->model('PendudukModel')->getPenduduk($data);

            if ($data['penduduk']) {
                if ($data['penduduk']['tanggalMenerima'] == null && $data['penduduk']['jenisBansos'] == null) {
                    $data['penduduk']['tanggalMenerima'] = $data['penduduk']['jenisBansos'] = 'Belum pernah menerima bansos';
                    $data['status'] = 'warning';
                } else {

                    $data['penduduk']['tanggalMenerima'] = $this->translateDate($data['penduduk']['tanggalMenerima']);
                }
                Flasher::setFlash(
                    "<span class='fs-5'>Data berikut sudah terdaftar di sistem.</span><hr>
                    <div class='text-start'>NIK: <b>{$data['penduduk']['nik']}</b><br>
                    Nama: <b>{$data['penduduk']['nama']}</b><br>
                    Alamat: <b>{$data['penduduk']['alamatRumah']}</b><br>
                    Terakhir Menerima Bansos: <b>{$data['penduduk']['tanggalMenerima']}</b><br>
                    Jenis Bansos: <b>{$data['penduduk']['jenisBansos']}</b></div>",
                    '',
                    "{$data['status']}"
                );
                header('Location: ' . BASEURL);
            } else {
                Flasher::setFlash('<span class="fs-5">Data tersebut', 'tidak terdaftar di sistem kami.</span>', 'danger');
                header('Location: ' . BASEURL);
            }

            $_SESSION['input'] = array(
                'nikVal' => $data['nik'],
                'hhVal' => $data['hari'],
                'bbVal' => $data['bulan'],
                'ttttVal' => $data['tahun']
            );
        } else {
            header('Location: ' . BASEURL);
        }
    }
}

class BerandaAuth extends Beranda
{
    public function index()
    {
        $data['judul'] = $this->judul;
        $data['navActive'] = 'beranda';
        
        $data['distrik'] = '';
        $data['lebar-cari'] = 'col-lg-3';
        $data['user'] = $_SESSION['user'];

        $this->view('templates/header', $data);
        if ($data['user']['tipeAkun'] == 1) {
            // When RT officer login
            $this->view('templates/navPengguna', $data);
            $data['lebar-cari'] = 'col-lg-7';
            $data['distrik'] = 'RW 0' . $data['user']['rw'] . ' RT 0' . $data['user']['rt'];
            $this->view('beranda/indexofficer', $data);
            $this->view('beranda/filters/filterRT', $data);
            $this->view('beranda/tabel', $data);
        } else if ($data['user']['tipeAkun'] == 2) {
            // When RW officer login
            $data['lebar-cari'] = 'col-lg-5';
            $data['distrik'] = 'RW 0' . $data['user']['rw'];
            $this->view('templates/navPengguna', $data);
            $this->view('beranda/indexofficer', $data);
            $this->view('beranda/filters/filterRW', $data);
            $this->view('beranda/tabel', $data);
        } else if ($data['user']['tipeAkun'] == 3 || $data['user']['tipeAkun'] == 5) {
            // When Superadmin login
            $data['riwayat'] = $this->model('RiwayatModel')->getRiwayat(5);
            $data['riwayat'] = $this->translateTime($data['riwayat']);

            $this->view('templates/navAdmin', $data);
            $this->view('beranda/indexofficer', $data);
            $this->view('beranda/filters/filter');
            $this->view('beranda/tabel', $data);
        } else if ($data['user']['tipeAkun'] == 4) {
            // When Special Officer login
            $data['nikVal'] =  $data['hhVal'] = $data['bbVal'] = $data['ttttVal'] = '';
            $data['statusRegistrasi'] = $this->model('AdministrasiModel')->getStatusRegister()['registrasi'];
            if (isset($_SESSION['input'])) {
                $data = array_merge($data, $_SESSION['input']);
                unset($_SESSION['input']);
            }
            $this->view('templates/navPengguna', $data);
            $this->view('beranda/indexspecial', $data);
        } else {
            $this->view('templates/navPengguna', $data);
            $this->view('beranda/index', $data);
        }
        $this->view('templates/footer');
    }

    public function tambahBansos()
    {
        if (!empty($_POST)) {
            $data = $_POST;
            $data['date'] = date('Y-m-d');
            $data['hashId'] = hash('md5', $data['nik']);
            $data['jenisBansos'] = $_SESSION['user']['namaAkun'];
            $data['penduduk'] = $this->model('PendudukModel')->getPenduduk($data);
            if ($data['penduduk']) {
                if ($data['penduduk']['tanggalMenerima'] == null && $data['penduduk']['jenisBansos'] == null) {
                    $this->model('BansosModel')->tambahBansosBaru($data);
                } else {
                    $this->model('BansosModel')->updateBansos($data);
                }
                $this->registerRiwayat($_SESSION['user'], 'Menambahkan Data Bansos', $data['nik']);
                Flasher::setFlash('<span class="fs-5">Anda telah menambahkan', 'bansos ' . $data['jenisBansos'] . ' kepada ' . $data['nik'] . '</span>', 'success');
                header('Location: ' . BASEURL);
            } else {
                Flasher::setFlash('<span class="fs-5">Anda gagal menambahkan', 'bansos ' . $data['jenisBansos'] . '</span>', 'danger');
                header('Location: ' . BASEURL);
            }

            $_SESSION['input'] = array(
                'nikVal' => $data['nik'],
                'hhVal' => $data['hari'],
                'bbVal' => $data['bulan'],
                'ttttVal' => $data['tahun']
            );
        } else {
            header('Location: ' . BASEURL);
        }
    }

    public function getRW()
    {
        if (!empty($_POST)) {
            echo json_encode($this->model('PendudukModel')->getDataRW());
        } else {
            header('Location: ' . BASEURL);
        }
    }

    public function getRT()
    {
        if (!empty($_POST)) {
            $data = array_merge($_POST, $_SESSION['user']);
            echo json_encode($this->model('PendudukModel')->getDataRT($data));
        } else {
            header('Location: ' . BASEURL);
        }
    }

    public function getHalaman()
    {
        if (!empty($_POST)) {
            $data = array_merge($_POST, $_SESSION['user']);
            echo json_encode($this->model('PendudukModel')->hitungBarisDikueri($data));
        } else {
            header('Location: ' . BASEURL);
        }
    }

    public function getDataTabelPenduduk()
    {
        if (!empty($_POST)) {
            $data = array_merge($_POST, $_SESSION['user']);
            $data['halaman'] = (($data['halaman']) - 1) * 25;
            echo json_encode($this->model('PendudukModel')->getDataPenduduk($data));
        } else {
            header('Location: ' . BASEURL);
        }
    }
}
