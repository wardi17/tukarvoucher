<?php

class LoginModel {
	
	private $table ="[um_db].[dbo].a_user";
	private $db;
	private $db2;
	public function __construct()
	{
		$this->db = new Database;
	}

	public function checkLogin($data)
{
    $username = addslashes($data["username"]);
    $pass = addslashes($data["password"]);

    $datas = [];
    $errors = [];

    // 1. Cek username ada atau tidak
    $queryUser = "SELECT pass, email, id_user AS userid, id_cust AS nama 
                  FROM $this->table 
                  WHERE email = '".$username."'";
    $sqlUser = $this->db->baca_sql($queryUser);

    $usernameExists = false;
    $passwordCorrect = false;
    $userid = null;
    $nama = null;

    if (odbc_fetch_row($sqlUser)) {
        $usernameExists = true;
        $passDB = odbc_result($sqlUser, "pass");
        $userid = odbc_result($sqlUser, "userid");
        $nama = odbc_result($sqlUser, "nama");

        if ($passDB === $pass) {
            $passwordCorrect = true;
        }
    }

    // 2. Tentukan pesan kesalahan
    if (!$usernameExists) {
        $errors[] = "Username salah";
    }
    if ($usernameExists && !$passwordCorrect) {
        $errors[] = "Password salah";
    }

    // 3. Jika username dan password benar → login berhasil
    if ($usernameExists && $passwordCorrect) {
        $datas[] = [
            'username' => $nama,
            'id_user' => $userid
        ];
    }

    // 4. Return hasil
    if (!empty($datas)) {
        return [
            'success' => true,
            'data' => $datas[0],
            'errors' => []
        ];
    } else {
        // Jika username tidak ada, kita juga cek password validitas dummy
        // supaya pesan error bisa tampil dua-duanya jika perlu
        if (!$usernameExists) {
            // Cek password valid atau tidak (opsional, jika ingin tampilkan dua error sekaligus)
            $queryPass = "SELECT pass FROM $this->table WHERE pass = '".$pass."'";
            $sqlPass = $this->db->baca_sql($queryPass);
            if (!odbc_fetch_row($sqlPass)) {
                $errors[] = "Password salah";
            }
        }

        return [
            'success' => false,
            'data' => null,
            'errors' => $errors
        ];
    }
}

	
	
	public function getDataDivisi(){
		$query ="SELECT DISTINCT divisi_budget FROM  $this->table WHERE divisi_budget <>'NULL'";
		$result =$this->db2->baca_sql2($query);
			
			$data =[];
			while(odbc_fetch_row($result)){
				$data[] = array(
					"divisi_budget"=>rtrim(odbc_result($result,'divisi_budget')),

				);
				
				}
				
		return $data;
	}

}