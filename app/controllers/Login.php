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
             * - full     → Dashboard, Berikan Voucher, Tukar Voucher (tokomerchant & customer)
             * - giver    → Hanya Berikan Voucher (customer)
             * - redeemer → Hanya Tukar Voucher (tokomerchant)
             */
                $userConfig = array(
                    'herman' => array(
                        'tokomerchant' => 'full',
                        'level'        => 'full',
                        'redirect'     => base_url . '/home'
                    ),
                     'wardi' => array(
                        'tokomerchant' => 'KK',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),

                    'liana' => array(
                        'tokomerchant' => 'KK',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    'ratna' => array(
                        'tokomerchant' => 'KK',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    //HC
                    'endang' => array(
                        'tokomerchant' => 'HC',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),

                    //MB
                    'helat' => array(
                        'tokomerchant' => 'MB',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    'yuli' => array(
                        'tokomerchant' => 'MB',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    //BTOUR
                    'monica' => array(
                        'tokomerchant' => 'BTOUR',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    //BMN
                    'fayziah' => array(
                        'tokomerchant' => 'BMN',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    'jerry' => array(
                        'tokomerchant' => 'BMN',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),
                    'weelan' => array(
                        'tokomerchant' => 'BMI',
                        'level'        => 'redeemer',
                        'redirect'     => base_url . '/tstukarvoucher'
                    ),

                    //giver berikan voucher (CUSTOMER)
                    //DKI
                    'diana' => array(
                        'tokomerchant' => 'customer',
                        'level'        => 'giver',
                        'redirect'     => base_url . '/tsberikanvoucher'
                    ),
                    //JBR
                    'susan' => array(
                        'tokomerchant' => 'customer',
                        'level'        => 'giver',
                        'redirect'     => base_url . '/tsberikanvoucher'
                    ),
                    //BMI
                    'erma' => array(
                        'tokomerchant' => 'customer',
                        'level'        => 'giver',
                        'redirect'     => base_url . '/tsberikanvoucher'
                    ),
                );

                $username = $result['data']['username'];

                // 4. Jika user ada di mapping → gunakan setting, jika tidak → default full access
                if (isset($userConfig[$username])) {
                    $_SESSION['tokomerchant'] = $userConfig[$username]['tokomerchant'];
                    $_SESSION['level']        = $userConfig[$username]['level'];
                    header('Location: ' . $userConfig[$username]['redirect']);
                } else {
                    // Default jika user tidak di-mapping khusus
                    header('Location: ' . base_url . '/login');
                    exit;
                }


        exit; // hentikan eksekusi setelah redirect
    }
}
