<?php

class Login extends Controller 
{
    public function index() 
    {
        $data['title'] = 'Halaman Login';
        $this->view('login/login', $data);
    }

    public function prosesLogin() 
    {
        // 1. Validasi input kosong
        if (empty($_POST['username']) || empty($_POST['password'])) {
            header('Location: ' . base_url . '/login');
            exit;
        }

        // 2. Ambil data user dari model
        $loginModel = $this->model('LoginModel');
        $result = $loginModel->checkLogin($_POST); 
        // Hasil array: ['success'=>bool, 'data'=>array, 'errors'=>array]

        if (!$result['success']) {
            // Jika login gagal
            $errorMessages = implode(', ', $result['errors']);
            Flasher::setMessage('Login gagal', $errorMessages, 'danger');
            header('Location: ' . base_url . '/login');
            exit;
        }

        // 3. Jika login berhasil → set session umum
        $_SESSION['id_user']       = $result['data']['id_user'];
        $_SESSION['username']      = $result['data']['username'];
        $_SESSION['session_login'] = 'sudah_login';

        /**
         * Level User:
         * - full     → Dashboard, Berikan Voucher, Tukar Voucher
         * - giver    → Hanya Berikan Voucher
         * - redeemer → Hanya Tukar Voucher
         */
        $userConfig = array(
            'wardi' => array(
                'tokomerchant' => 'KK',
                'level'        => 'full',       // semua menu
                'redirect'     => base_url . '/home'
            ),
            'herman' => array(
                'tokomerchant' => 'BMN',
                'level'        => 'giver',      // hanya berikan voucher
                'redirect'     => base_url . '/tsberikanvoucher'
            ),
            'weelan' => array(
                'tokomerchant' => 'BMI',
                'level'        => 'redeemer',   // hanya tukar voucher
                'redirect'     => base_url . '/tstukarvoucher'
            )
        );

        $username = $result['data']['username'];

        // 4. Jika user ada di mapping → gunakan setting, jika tidak → default full access
        if (isset($userConfig[$username])) {
            $_SESSION['tokomerchant'] = $userConfig[$username]['tokomerchant'];
            $_SESSION['level']        = $userConfig[$username]['level'];
            header('Location: ' . $userConfig[$username]['redirect']);
        } else {
            // Default jika user tidak di-mapping khusus keluar menu
               header('Location: ' . base_url . '/login');
        }

        exit; // hentikan eksekusi setelah redirect
    }
}
