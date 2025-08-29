
<?php
date_default_timezone_set('Asia/Jakarta'); 
 class MsKategoriModel  extends Models{
       protected $table_user ="[um_db].[dbo].a_user";
        protected $table_ms ="[crm-bmi].[dbo].ms_KategoriInvenaris";
    

    public function getidtampil(){
        return $this->getIdTransKategori();
    }

 private function getIdTransKategori() {
    // Ambil ID terakhir untuk tahun berjalan
    $yearSuffix = substr(date("Y"), 2, 2); // contoh: "25"
    $query = "
        SELECT TOP 1 KategoriID 
        FROM {$this->table_ms}
        WHERE SUBSTRING(KategoriID, 4, 2) = '{$yearSuffix}'
        ORDER BY CAST(SUBSTRING(KategoriID, 6, 4) AS INT) DESC
    ";

    $sql    = $this->db->baca_sql($query);
    $lastId = $sql ? odbc_result($sql, "KategoriID") : null;

    $prefix  = "KT." . $yearSuffix;  // hasil: KT.25
    $default = "0001";               // nomor awal 4 digit

    if (!empty($lastId) && strlen($lastId) >= 7) {
        // Ambil tahun (index 3-4) dan nomor urut (index 5-8)
        $lastYear   = substr($lastId, 3, 2);     // "25"
        $lastNumber = (int)substr($lastId, 5, 4); // "0001" → 1

        if ($lastYear === $yearSuffix) {
            $newNumber = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $newNumber = $default;
        }
    } else {
        $newNumber = $default;
    }

    return $prefix . $newNumber; // contoh: KT.250001 → KT.250002 dst
}





    public function SaveData(){

        $rawData = $_POST["datahider"];
        $post = json_decode($rawData, true);
    
        $KategoriID = $this->getIdTransKategori();
        $NamaKategori   = $this->test_input($post["NamaKategori"]);
        

        $query = "INSERT INTO $this->table_ms(KategoriID,NamaKategori)
                  VALUES('{$KategoriID}','{$NamaKategori}')
                ";
        
       
        $result = $this->db->baca_sql($query);
        // Buat response
        if ($result) {
            $pesan = [
                'nilai' => 1,
                'error' => 'Berhasil Simpan data'
            ];
        } else {
            $pesan = [
                'nilai' => 0,
                'error' => 'Data Gagal Ditambahkan'
            ];
        }

        return $pesan;
    }



    public function getAllKategori() {
    // Assuming you have a database connection set up
    try {
        // Prepare the SQL query
                $query = "
                SELECT 
                    a.KategoriID     AS KategoriID,
                    a.NamaKategori       AS NamaKategori
                FROM $this->table_ms AS a
            ";
            // Ensure $this->table_ms is properly defined


        //$this->consol_war($query);
        // Execute the query
        $result = $this->db->baca_sql($query);
        
        // Check if the query execution was successful
        if (!$result) {
            throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
        }

        // Fetch all results as an associative array
        $data = [];
        while ($row = odbc_fetch_array($result)) {
            $data[] = $row;
        }

        // Optional: Log or handle the data as needed
        //$this->consol_war($data); // Ensure this method is defined and does what you expect

        return $data;

    } catch (Exception $e) {
        
        // Log the error message for debugging
        error_log("Error in getAllKategori: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
    }
}



      public function UpdateData(){
     

         $rawData =  $_POST["datahider"];
        $post = json_decode($rawData, true);
     
       
        $KategoriID = $this->test_input($post["KategoriID"]);
        $NamaKategori   = $this->test_input($post["NamaKategori"]);
       


        $query ="UPDATE $this->table_ms SET NamaKategori='{$NamaKategori}' WHERE KategoriID ='{$KategoriID}'
        ";
  //$this->consol_war($query);
        $result = $this->db->baca_sql($query);
        // Buat response
        if ($result) {
            $pesan = [
                'nilai' => 1,
                'error' => 'Berhasil Update data'
            ];
        } else {
            $pesan = [
                'nilai' => 0,
                'error' => 'Data Gagal DiUpate'
            ];
        }

        return $pesan;
      }

        private function hapusFileupload($nama_files_old, $KategoriID)
        {
            // Ambil nama file dari database
            $query = "SELECT document_files FROM $this->table_ms WHERE KategoriID='{$KategoriID}'";
            
            $sql = $this->db->baca_sql($query);
            $document_files = odbc_result($sql, "document_files");

     
            // Ubah string menjadi array
            $list_files_db = array_map('trim', explode(",", $document_files));
            // Loop semua file dari database
            foreach ($list_files_db as $file_name) {
                // Jika file ini TIDAK ADA dalam nama_files_old, maka hapus
                if (!in_array($file_name, $nama_files_old)) {
                    $path = FOLDER . basename($file_name); // Pastikan hanya nama file, bukan path lengkap

                    if (file_exists($path)) {
                        unlink($path); // Hapus file
                    }
                }
            }
        }

    
      public function DeleteData(){
          $rawData = file_get_contents("php://input");
        $post = json_decode($rawData, true);
     
        $KategoriID = $this->test_input($post["KategoriID"]);

        $query = "SELECT document_files FROM $this->table_ms WHERE KategoriID='{$KategoriID}'";
        $sql = $this->db->baca_sql($query);
// Cek jika query berhasil dan ada hasil
        $document_files = "";
        if ($sql && odbc_fetch_row($sql)) {
            $document_files = odbc_result($sql, "document_files");
        }
        // Jika document_files tidak kosong
        if (!empty($document_files)) {
            // Ubah string jadi array, buang spasi
            $list_files_db = array_map('trim', explode(",", $document_files));

            foreach ($list_files_db as $file_name) {
                // Pastikan hanya nama file saja
                $path = FOLDER . basename($file_name);

                // Cek dan hapus jika file ada
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
        $query2 ="DELETE FROM $this->table_ms   WHERE KategoriID ='{$KategoriID}'";
            $result = $this->db->baca_sql($query2);
        // Buat response
        if ($result) {
            $pesan = [
                'nilai' => 1,
                'error' => 'Berhasil Delete data'
            ];
        } else {
            $pesan = [
                'nilai' => 0,
                'error' => 'Data Gagal Delete'
            ];
        }

        return $pesan;
      }




      public function GetdocumentByID(){
         $rawData = file_get_contents("php://input");
        $post = json_decode($rawData, true);
        $idinvetarsi = $post["idinvetarsi"];
        try{

   
        $query="SELECT document_files FROM $this->table_ms WHERE KategoriID='{$idinvetarsi}'";
        $sql =$this->db->baca_sql($query);
		$document_files=odbc_result($sql,"document_files");
        
            $data =["document_files" =>$document_files];
            return $data;
         } catch (Exception $e){
            error_log("Error in getAllKategori: " . $e->getMessage());
        
            // Return an empty array or handle the error as needed
            return [];
         }
      }


      //tambah stok dan kedetailstok 19/08/2025
       public function TambahStokdata(){
         $rawData = $_POST["datahider"];
        $post = json_decode($rawData, true);
            
        //$this->consol_war($post);

        $KategoriID = $this->test_input($post["KategoriID"]);
        $qty         = $this->test_input($post["qty"]);
        $keterangan  = $this->test_input($post["keterangan"]);
        $userid       = $_SESSION['id_user'];
        $jenistransaksi = "MASUK";

        $query = "INSERT INTO $this->table_ms(KategoriID,Qty,JenisTransaksi,Keterangan,UserID)
                  VALUES('{$KategoriID}','{$qty}','{$jenistransaksi}','{$keterangan}','{$userid}')
                ";
        
       
        $result = $this->db->baca_sql($query);
        // Buat response
        if ($result) {
            $pesan = [
                'nilai' => 1,
                'error' => 'Berhasil Tambah Stok data'
            ];
        } else {
            $pesan = [
                'nilai' => 0,
                'error' => 'Data Gagal Tambah Stok'
            ];
        }

        return $pesan;
       }

      //and
      
 }