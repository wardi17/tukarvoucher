
<?php
date_default_timezone_set('Asia/Jakarta'); 
 class Ts_BerikanVocherModel  extends Models{
       protected $table_user ="[um_db].[dbo].a_user";
        protected $table_customer ="[crm-bmi].[dbo].[vw_AllCustomerActive]";
        protected $table_ms ="[crm-bmi].[dbo].[vw_AllCustomerActive]";
        protected $table_msvoucher ="[crm-bmi].[dbo].[ms_voucher]";
        protected $table_ts ="[crm-bmi].[dbo].Ts_Berikan_Voucher";
        protected $table_tsdtl ="[crm-bmi].[dbo].Ts_Berikan_VoucherDetail";


public function getcabang(){
    try {
        // Siapkan query SQL
        $query = "
          SELECT DISTINCT 
            SourceDB,
            CASE 
                WHEN SourceDB = 'bambi-bmi' THEN 'BMI'
                WHEN SourceDB = 'bambi-mg2' THEN 'MD'
                WHEN SourceDB = 'bambi04'   THEN 'BD'
                ELSE 'UNKNOWN'
            END AS cabang
        FROM $this->table_customer
        ORDER BY SourceDB ASC;
        ";


        // Eksekusi query
        $result = $this->db->baca_sql($query);

        // Validasi hasil eksekusi query
        if (!$result) {
            throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
        }

        // Ambil data hasil query
        $datas = [];
        while (odbc_fetch_row($result)) {
            $cabang = rtrim(odbc_result($result, 'cabang'));
            $SourceDB = rtrim(odbc_result($result, 'SourceDB'));

            $datas[] = [
                "SourceDB"   => $SourceDB,
                "cabang" => $cabang,
            ];
        }

        //$this->consol_war($datas);
        return $datas;

    } catch (Exception $e) {
        // Catat error log untuk debug
        error_log("Error in GetKatgori: " . $e->getMessage());

        // Kembalikan array kosong jika gagal
        return [];
    }
}
   public function getDataCustomer() {

    $rawData = file_get_contents("php://input");
    $post = json_decode($rawData, true);
    $sourceDB = $post["SourceDB"];

    try {
        // Siapkan query SQL
        $query = "
            SELECT 
              CustomerID,CustName,SourceDB
            FROM $this->table_customer  WHERE SourceDB='{$sourceDB}'
            ORDER BY CustomerID ASC
        ";

       // $this->consol_war($query);
        // Eksekusi query
        $result = $this->db->baca_sql($query);

        // Validasi hasil eksekusi query
        if (!$result) {
            throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
        }

        // Ambil data hasil query
        $datas = [];
        while (odbc_fetch_row($result)) {
            $CustomerID = rtrim(odbc_result($result, 'CustomerID'));
            $CustName = rtrim(odbc_result($result, 'CustName'));
            $SourceDB = rtrim(odbc_result($result, 'SourceDB'));

            $datas[] = [
                "id"   => $CustomerID,
                "name" => $CustomerID . " | " . $CustName,
                "db"   => $SourceDB
            ];
        }

        //$this->consol_war($datas);
        return $datas;

    } catch (Exception $e) {
        // Catat error log untuk debug
        error_log("Error in GetKatgori: " . $e->getMessage());

        // Kembalikan array kosong jika gagal
        return [];
    }
}
    

    public function cekkodevoucher(){
        $rawData = file_get_contents("php://input");
        $post = json_decode($rawData, true);
      //  $this->consol_war($post);
        $kodevoucher = $this->test_input($post["kodevoucher"]);
        try {

            $query = "SELECT Kode_Voucher 
                    FROM $this->table_msvoucher 
                    WHERE Kode_Voucher='{$kodevoucher}' AND jumlah_tukar_voucher = 1";
            $sql = $this->db->baca_sql($query);

            // Periksa apakah ada hasil
            if (odbc_fetch_row($sql)) {
                // Jika voucher ada
                $kode = odbc_result($sql, "Kode_Voucher");
                return [
                    "status" => "ok",
                    "message" => "Kode voucher ditemukan.",
                    "Kode_Voucher" => $kode
                ];
            } else {
                // Jika voucher tidak ada
                return [
                    "status" => "not_found",
                    "message" => "Kode voucher tidak ditemukan atau sudah tidak berlaku.",
                    "Kode_Voucher" => null
                ];
            }

        } catch (Exception $e) {
            error_log("Error in getAllInventaris: " . $e->getMessage());
            return [
                "status" => "error",
                "message" => "Terjadi kesalahan saat mengambil data voucher."
            ];
        }


    }


        private function getIdTransaki() {
            // Ambil 2 digit tahun berjalan, contoh: "25"
            $yearSuffix = substr(date("Y"), 2, 2); 

            // Query: ambil ID terakhir untuk tahun berjalan
            $query = "
                SELECT TOP 1 Kode_Berikan 
                FROM {$this->table_ts}
                WHERE SUBSTRING(Kode_Berikan, 8, 2) = '{$yearSuffix}'
                ORDER BY CAST(SUBSTRING(Kode_Berikan, 10, 4) AS INT) DESC
            ";

            $sql    = $this->db->baca_sql($query);
            $lastId = $sql ? odbc_result($sql, "Kode_Berikan") : null;

            // Prefix selalu: TSBEVO.[tahun]
            $prefix  = "TSBEVO." . $yearSuffix; 
            $default = "0001"; // nomor awal jika belum ada

            if (!empty($lastId) && strlen($lastId) >= 12) {
                // Ambil 4 digit terakhir (posisi 10–13, 1-based → substr index 9, length 4)
                $lastNumber = (int) substr($lastId, 9, 4);
                $newNumber  = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);
            } else {
                $newNumber  = $default;
            }

            return $prefix . $newNumber; 
            // Contoh hasil: TSBEVO.250001 → TSBEVO.250002
        }


    public function SaveData(){
       $rowData = file_get_contents("php://input");
        $post = json_decode($rowData, true);

        $Kode_Berikan = $this->getIdTransaki();
        
         if ($this->simpadataHider($Kode_Berikan,$post) == 1) {
                return $this->SimpanDetailVoucher($Kode_Berikan,$post);
         }
          return ['nilai' => 0, 'error' => 'Gagal Simpan Data Voucher'];
    }

        private function simpadataHider($Kode_Berikan,$post){
            $cabang = $this->test_input($post["cabang"]);
            $CustomerID = $this->test_input($post["customerid"]);
            $CustName = $this->test_input($post["custname"]);
            $SOTransacID = $this->test_input($post["noso"]);
            $Jumlah_berikan_voucher = $this->test_input($post["jumlah"]);
            $Keterangan = $this->test_input($post["keterangan"]);
            $User_kasih_voucher = $_SESSION['username'];
    
            $query = "INSERT INTO $this->table_ts (Kode_Berikan, cabang, CustomerID,CustName, SOTransacID, Jumlah_berikan_voucher, Keterangan, User_kasih_voucher)
                      VALUES ('{$Kode_Berikan}', '{$cabang}', '{$CustomerID}','{$CustName}','{$SOTransacID}', {$Jumlah_berikan_voucher}, '{$Keterangan}', '{$User_kasih_voucher}')";
        return $this->db->baca_sql($query) ?1 :0;
          
        }
      
    

    private function SimpanDetailVoucher($Kode_Berikan,$post){
        $vouchers = $post["vouchers"];
         $CustomerID = $this->test_input($post["customerid"]);
          $User_kasih_voucher = $_SESSION['username'];
          $Date_kasih_voucher = date('Y-m-d H:i:s'); // Format tanggal dan waktu saat ini
        $errorMessages = [];
        foreach ($vouchers as $voucher) {
            $kodeVoucher = $this->test_input($voucher["kodevoucher"]);
            $Date_Detail = date('Y-m-d H:i:s'); // Tanggal dan waktu saat ini
            // Cek apakah kode voucher sudah pernah digunakan
            if ($this->CekDetailKodeVoucer($kodeVoucher) == 1) {
                $errorMessages[] = "Kode voucher '{$kodeVoucher}' sudah pernah digunakan.";
                continue; // Lewati penyimpanan untuk kode voucher ini
            }

            // Simpan detail voucher
            $query = "INSERT INTO {$this->table_tsdtl} (Kode_Berikan, Kode_voucher,Date_Detail) VALUES ('{$Kode_Berikan}', '{$kodeVoucher}','{$Date_Detail}')";
            $query .=" UPDATE {$this->table_msvoucher} SET CustomerID ='{$CustomerID}',User_kasih_voucher='{$User_kasih_voucher}',Date_kasih_voucher='{$Date_kasih_voucher}',
             jumlah_tukar_voucher=0 WHERE Kode_voucher = '{$kodeVoucher}'";

            // $this->consol_war($query);
            if (!$this->db->baca_sql($query)) {
                $errorMessages[] = "Gagal menyimpan kode voucher '{$kodeVoucher}'.";
            }
        }

      // Kembalikan hasil penyimpanan
        if (empty($errorMessages)) {
            return ['nilai' => 1, 'error' => "berhasil simpan data"];
        } else {
             $this->db->baca_sql("DELETE FROM {$this->table_ts} WHERE Kode_Berikan = '{$Kode_Berikan}'");
            return ['nilai' => 0, 'error' => implode(" ", $errorMessages)];
        }
    }

      private function CekDetailKodeVoucer($Kode_voucher)
    {
        $query = "SELECT DISTINCT Kode_voucher FROM {$this->table_tsdtl} WHERE Kode_voucher = '{$Kode_voucher}'";
        $result = $this->db->baca_sql($query);
        $rows = odbc_fetch_array($result);
        return $rows > 0 ? 1 : 0;
    }



    public function listdata(){
     
        $toko  =$_SESSION['tokomerchant'];
        $username =  $_SESSION['username'] ;

        $kodeisi ="";
        if($toko !== "full"){
            $kodeisi =" WHERE User_kasih_voucher ='{$username}'";
        }
        
        try {
            $query = "SELECT 
                        ts.Kode_Berikan,
                             CASE 
                            WHEN  ts.cabang = 'bambi-bmi' THEN 'BMI'
                            WHEN  ts.cabang = 'bambi-mg2' THEN 'MD'
                            WHEN  ts.cabang = 'bambi04'   THEN 'BD'
                            ELSE 'UNKNOWN'
                        END AS cabang,
                        ts.CustomerID,
                        ts.CustName,
                        ts.SOTransacID,
                        ts.Jumlah_berikan_voucher,
                        ts.Keterangan,
                        ts.User_kasih_voucher,
                        ts.Date_kasih_voucher,
                        ts.Status_posting,
                        COUNT(tsd.Kode_voucher) AS Jumlah_Voucher_Terpakai
                    FROM 
                        {$this->table_ts} ts
                    LEFT JOIN 
                        {$this->table_tsdtl} tsd ON ts.Kode_Berikan = tsd.Kode_Berikan
                     {$kodeisi}   
                    GROUP BY 
                        ts.Kode_Berikan, ts.cabang, ts.CustomerID,ts.CustName, ts.SOTransacID, 
                        ts.Jumlah_berikan_voucher, ts.Keterangan, 
                        ts.User_kasih_voucher, ts.Date_kasih_voucher,ts.Status_posting
                    ORDER BY 
                        ts.Date_kasih_voucher DESC";

            //die(var_dump($query));
            $result = $this->db->baca_sql($query);
            $datas = [];
            while (odbc_fetch_row($result)) {
                $Kode_Berikan = rtrim(odbc_result($result, 'Kode_Berikan'));
                $cabang = rtrim(odbc_result($result, 'cabang'));
                $CustomerID = rtrim(odbc_result($result, 'CustomerID'));
                $CustName = rtrim(odbc_result($result, 'CustName'));
                $SOTransacID = rtrim(odbc_result($result, 'SOTransacID'));
                $Jumlah_berikan_voucher = odbc_result($result, 'Jumlah_berikan_voucher');
                $Keterangan = rtrim(odbc_result($result, 'Keterangan'));
                $User_kasih_voucher = rtrim(odbc_result($result, 'User_kasih_voucher'));
                $Date_kasih_voucher = rtrim(odbc_result($result, 'Date_kasih_voucher'));
                $Jumlah_Voucher_Terpakai = odbc_result($result, 'Jumlah_Voucher_Terpakai');
                $Status_posting = rtrim(odbc_result($result, 'Status_posting'));
                  $time = strtotime($Date_kasih_voucher);
                    $formattedDate = $time ? date('d-m-y', $time) : null;
                     $date_berikan = $time ? date('d/m/Y', $time) : null;
                $datas[] = [
                    "Kode_Berikan" => $Kode_Berikan,
                    "cabang" => $cabang,
                    "CustomerID" => $CustomerID,
                    "CustName" => $CustName,
                    "SOTransacID" => $SOTransacID,
                    "Jumlah_berikan_voucher" => $Jumlah_berikan_voucher,
                    "Keterangan" => $Keterangan,
                    "User_kasih_voucher" => $User_kasih_voucher,
                    "Date_kasih_voucher" =>  $formattedDate,
                    "date_berikan"=>$date_berikan,
                    "Jumlah_Voucher_Terpakai" => $Jumlah_Voucher_Terpakai,
                    "Status_posting" => $Status_posting,
                    "username" => $_SESSION['username']
                ];
            }       

            //$this->consol_war($datas);
            return $datas;
        } catch (Exception $e) {
            error_log("Error in listdata: " . $e->getMessage());
            return [];
        }
    }




    public function getdetailberivoucher(){     
        $rawData = file_get_contents("php://input");
        $post = json_decode($rawData, true);

        $Kode_Berikan = $this->test_input($post["kode"]);
     
        try {
            $query = "SELECT Kode_voucher FROM {$this->table_tsdtl} WHERE Kode_Berikan = '{$Kode_Berikan}'";
            $result = $this->db->baca_sql($query);
            $datas = [];
            while (odbc_fetch_row($result)) {
                $Kode_voucher = rtrim(odbc_result($result, 'Kode_voucher'));
               
                $datas[]=["Kode_voucher" => $Kode_voucher];
            
            }       

             //$this->consol_war($datas);
            return $datas;
        } catch (Exception $e) {
            error_log("Error in getdetailberivoucher: " . $e->getMessage());
             return [];
        }

       
    }


    public function postingdata(){
        $rawData = file_get_contents("php://input");    
        $post = json_decode($rawData, true);
        $Kode_Berikan = $this->test_input($post["kode"]);
        $User_posting = $_SESSION['username'];
        $dateposting = date('Y-m-d H:i:s'); // Format tanggal dan waktu saat ini
            
        try {
            $query = "UPDATE {$this->table_ts} SET Status_posting ='Y',User_posting='{$User_posting}', Date_posting='{$dateposting}' WHERE Kode_Berikan = '{$Kode_Berikan}'";
            // $this->consol_war($query);
            if ($this->db->baca_sql($query)) {
                return ['nilai' => 1, 'error' => 'Berhasil Posting Data'];
            } else {
                return ['nilai' => 0, 'error' => 'Gagal Posting Data'];
            }
        } catch (Exception $e) {
            error_log("Error in postingdata: " . $e->getMessage());
            return ['nilai' => 0, 'error' => 'Terjadi kesalahan saat memproses data.'];
        }
        //batas kode baru

    }
   


    public function getdatadetailprint(){
        $rawData = file_get_contents("php://input");
        $post = json_decode($rawData, true);

        $Kode_Berikan = $this->test_input($post["kode"]);
     
        try {
            $query = "SELECT Kode_voucher FROM {$this->table_tsdtl} WHERE Kode_Berikan = '{$Kode_Berikan}'";
            $result = $this->db->baca_sql($query);
            $datas = [];
            while (odbc_fetch_row($result)) {
                $Kode_voucher = rtrim(odbc_result($result, 'Kode_voucher'));
               
                $datas[]=["Kode_voucher" => $Kode_voucher];
            
            }       

             //$this->consol_war($datas);
            return $datas;
        } catch (Exception $e) {
            error_log("Error in getdatadetailprint: " . $e->getMessage());
             return [];
        }
    }
      //and
      
 }