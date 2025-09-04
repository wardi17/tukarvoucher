<?php
date_default_timezone_set('Asia/Jakarta'); 
 class Ts_TukarVocherModel  extends Models{
       protected $table_user ="[um_db].[dbo].a_user";
       protected $table_beri ="[crm-bmi].[dbo].Ts_Berikan_Voucher";
       protected $table_beridtl ="[crm-bmi].[dbo].Ts_Berikan_VoucherDetail";
        protected $table_msvoucher ="[crm-bmi].[dbo].[ms_voucher]";

        protected $table_ts ="[crm-bmi].[dbo].Ts_Tukar_Voucher";
        protected $table_tsdtl ="[crm-bmi].[dbo].Ts_Tukar_VoucherDetail";


        /**
             * Cek apakah voucher ada di tabel tertentu
             */
            private function findVoucher($table, $kodevoucher, $extraCondition = '')
            {
                $where = "Kode_Voucher = '{$kodevoucher}'";
                if (!empty($extraCondition)) {
                    $where .= " AND {$extraCondition}";
                }

                $query = "SELECT Kode_Voucher FROM {$table} WHERE {$where}";
                $sql = $this->db->baca_sql($query);

                return odbc_fetch_row($sql) ? odbc_result($sql, "Kode_Voucher") : false;
            }

        public function cekkodevoucher(){
            $input = json_decode(file_get_contents('php://input'), true);

            $kodevoucher = isset($input['kodevoucher']) ? $input['kodevoucher'] : null;
  
            if (!$kodevoucher) {
                throw new InvalidArgumentException('Kode voucher is required');
            }

           try {
             // 1. Cek apakah voucher sudah pernah ditukar
           
                $kode = $this->findVoucher($this->table_tsdtl, $kodevoucher);
                if ($kode) {
                    return [
                        "status" => "already",
                        "message" => "Kode voucher sudah ditukar dan tidak bisa digunakan lagi.",
                        "Kode_Voucher" => $kode
                    ];
                }

                // 2. Jika belum ditukar, cek apakah voucher terdaftar di tabel berikan voucher detail (Status_posting='Y')

                $table_beri ="$this->table_beridtl  a
                LEFT JOIN $this->table_beri b ON a.Kode_Berikan=b.Kode_Berikan";
                $kode = $this->findVoucher($table_beri, $kodevoucher, "b.Status_posting='Y'");
                if ($kode) {
                    return [
                        "status" => "ok",
                        "message" => "Kode voucher valid dan bisa dipakai.",
                        "Kode_Voucher" => $kode
                    ];
                }

                // 3. Jika tidak ada di kedua tabel
                return [
                    "status" => "not_found",
                    "message" => "Kode voucher tidak terdaftar atau tidak valid.",
                    "Kode_Voucher" => null
                ];
           } catch (PDOException $e) {
            error_log('Database error in Ts_TukarVocherModel::cekkodevoucher: ' . $e->getMessage());
            throw new Exception('Database query error');
           }
        }


          private function getIdTransaki() {
            // Ambil 2 digit tahun berjalan, contoh: "25"
            $yearSuffix = substr(date("Y"), 2, 2); 

            // Query: ambil ID terakhir untuk tahun berjalan
            $query = "
                SELECT TOP 1 Kode_Tukar 
                FROM {$this->table_ts}
                WHERE SUBSTRING(Kode_Tukar, 8, 2) = '{$yearSuffix}'
                ORDER BY CAST(SUBSTRING(Kode_Tukar, 10, 4) AS INT) DESC
            ";

            $sql    = $this->db->baca_sql($query);
            $lastId = $sql ? odbc_result($sql, "Kode_Tukar") : null;

            // Prefix selalu: TSBEVO.[tahun]
            $prefix  = "TSTUVO." . $yearSuffix; 
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

        $Kode_Tukar = $this->getIdTransaki();
        
         if ($this->simpadataHider($Kode_Tukar,$post) == 1) {
                return $this->SimpanDetailVoucher($Kode_Tukar,$post);
         }
          return ['nilai' => 0, 'error' => 'Gagal Simpan Data Voucher'];
    }



      private function simpadataHider($Kode_Tukar,$post){

            $CustName = $this->test_input($post["custname"]);
            $notelpon = $this->test_input($post["notelpon"]);
            $Keterangan = $this->test_input($post["keterangan"]);
            $User_tukar_voucher =$this->test_input($post['username']);
            $TokoMerchant =  $this->test_input($post["toko_merchant"]);
    
            $query = "INSERT INTO $this->table_ts (Kode_Tukar, Toko_merchant, CustomerName,NoTelp, Keterangan,User_tukar_voucher)
                      VALUES ('{$Kode_Tukar}', '{$TokoMerchant}', '{$CustName}','{$notelpon}','{$Keterangan}','{$User_tukar_voucher}')";
        return $this->db->baca_sql($query) ?1 :0;
          
        }

          private function SimpanDetailVoucher($Kode_Tukar,$post){
          $vouchers = $post["vouchers"];
          $Toko_merchant = $_SESSION['tokomerchant'];
          $User_tukar_voucher = $_SESSION['username'];
          $Date_tukar_voucher = date('Y-m-d H:i:s'); // Format tanggal dan waktu saat ini
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
            $query = "INSERT INTO {$this->table_tsdtl} (Kode_Tukar, Kode_voucher,Date_Detail) VALUES ('{$Kode_Tukar}', '{$kodeVoucher}','{$Date_Detail}')";

            $query .=" UPDATE {$this->table_msvoucher} SET Toko_merchant ='{$Toko_merchant}',User_tukar_voucher='{$User_tukar_voucher}',Date_tukar_voucher='{$Date_tukar_voucher}'
              WHERE Kode_voucher = '{$kodeVoucher}'";
            if (!$this->db->baca_sql($query)) {

                $errorMessages[] = "Gagal menyimpan kode voucher '{$kodeVoucher}'.";
            }
        }

      // Kembalikan hasil penyimpanan
        if (empty($errorMessages)) {
            return ['nilai' => 1, 'error' => "berhasil simpan data"];
        } else {
                $this->db->baca_sql("DELETE FROM {$this->table_ts} WHERE Kode_Tukar = '{$Kode_Tukar}'");
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


    public function listdata() {
        
        $toko     = $_SESSION['tokomerchant'];
        $username = $_SESSION['username'] ;

        $kodeisi ="";
        if($toko !== "full"){
            $kodeisi =" WHERE  a.Toko_merchant='{$toko}' AND  a.User_tukar_voucher ='{$username}'";
        }


        try {
            $query = "SELECT 
                a.Kode_Tukar,
                a.Toko_merchant,
                a.CustomerName,
                a.NoTelp,
                a.Keterangan,
                a.User_tukar_voucher,
                a.Date_tukar_voucher,
                ISNULL([crm-bmi].[dbo].ConcatVoucher(a.Kode_Tukar), '') AS Vouchers
            FROM 
                $this->table_ts AS a 
                 {$kodeisi}   
            ORDER BY 
                a.Date_tukar_voucher DESC;
            ";
            
            //$this->consol_war($query);
         
            $this->db->baca_sql("SET ARITHABORT ON");
            $result = $this->db->baca_sql($query);
            $data = [];
            while (odbc_fetch_row($result)) {
               
                $kodeTukar = odbc_result($result, "Kode_Tukar");
                $tokoMerchant = odbc_result($result, "Toko_merchant");
                $customerName = odbc_result($result, "CustomerName");
                $noTelp = odbc_result($result, "NoTelp");
                $keterangan = odbc_result($result, "Keterangan");
                $userTukarVoucher = odbc_result($result, "User_tukar_voucher");
                $dateTukarVoucher = odbc_result($result, "Date_tukar_voucher");
                $vouchers = odbc_result($result, "Vouchers");
                $time = strtotime($dateTukarVoucher);
                    $formattedDate = $time ? date('d-m-y', $time) : null;

                $data[] = [
                    'Kode_Tukar' => $kodeTukar,
                    'Toko_merchant' => $tokoMerchant,
                    'CustomerName' => $customerName,
                    'NoTelp' => $noTelp,
                    'Keterangan' => $keterangan,
                    'User_tukar_voucher' => $userTukarVoucher,
                    'Date_tukar_voucher' => $formattedDate,
                    'Vouchers' => $vouchers // Ubah string menjadi array
                ];
            }

            //
            
           // $this->consol_war($data);
            return $data;
            
        } catch (PDOException $e) {
            error_log('Database error in Ts_TukarVocherModel::listdata: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Database query error'
            ];
        }
    }
 }