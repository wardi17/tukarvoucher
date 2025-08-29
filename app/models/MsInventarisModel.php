
<?php
date_default_timezone_set('Asia/Jakarta'); 
 class MsInventarisModel  extends Models{
       protected $table_user ="[um_db].[dbo].a_user";
        protected $table_ms ="[crm-bmi].[dbo].ms_Inventaris";
        protected $table_msdetail ="[crm-bmi].[dbo].ms_InventarisDetail";
        protected $table_kt ="[crm-bmi].[dbo].ms_KategoriInvenaris";


   public function GetKatgori() {
    try {
        // Siapkan query SQL
        $query = "
            SELECT 
                KategoriID,
                NamaKategori
            FROM $this->table_kt 
            ORDER BY KategoriID ASC
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
            $kategoriID = rtrim(odbc_result($result, 'KategoriID'));
            $namaKategori = rtrim(odbc_result($result, 'NamaKategori'));

            $datas[] = [
                "id"   => $kategoriID,
                "name" => $kategoriID . " | " . $namaKategori,
            ];
        }
        return $datas;

    } catch (Exception $e) {
        // Catat error log untuk debug
        error_log("Error in GetKatgori: " . $e->getMessage());

        // Kembalikan array kosong jika gagal
        return [];
    }
}
  


    public function SaveData(){
             $nama_atter = "";
        $nama_atter_str = "";
        if (!empty($_FILES)) {
            $files = $_FILES['files'];
            $total = count($files['name']);
            for ($i = 0; $i < $total; $i++) {
                $file_name = $files['name'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_size = $files['size'][$i];
                $file_error = $files['error'][$i];
                $fileType = $files['type'][$i];

                if ($file_error !== UPLOAD_ERR_OK) {
                    return "Error uploading $file_name. Error code: $file_error<br>";
                }

                if(($fileType == "image/gif") || ($fileType == "image/jpeg") || ($fileType == "image/png") || ($fileType == "image/pjpeg")){
                            $upload_dir = '../public/uploads_attachfile/';
                            $new_nama = $file_name;
                            $destination = $upload_dir . $new_nama;
                            if (move_uploaded_file($file_tmp, $destination)) {
                           $nama_atter .= $new_nama . ",";
                            }
					    } 

            }

            $nama_atter_str = rtrim($nama_atter, ",");
        }

      
        $rawData = $_POST["datahider"];
        $post = json_decode($rawData, true);
            


        $InventarisID = $this->test_input($post["InventarisID"]);
        $NamaBarang   = $this->test_input($post["NamaBarang"]);
        $Kategori      = $this->test_input($post["Kategori"]);
        $Stok          =0;
        $StokMinimum  = $this->test_input($post["StokMinimum"]);
        $StokMaksimum = $this->test_input($post["StokMaksimum"]);
        $HargaPokok   = $this->test_input($post["HargaPokok"]);
        $userid       = $_SESSION['id_user'];

        $query = "INSERT INTO $this->table_ms(InventarisID,NamaBarang,KategoriID,Stok,StokMinimum,StokMaksimum,HargaPokok,userInput,document_files)
                  VALUES('{$InventarisID}','{$NamaBarang}','{$Kategori}','{$Stok}','{$StokMinimum}','{$StokMaksimum}','{$HargaPokok}','{$userid}','{$nama_atter_str}')
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



    public function getAllInventaris() {
    // Assuming you have a database connection set up
    try {
        // Prepare the SQL query
                $query = "
                SELECT 
                    a.InventarisID     AS InventarisID,
                    a.NamaBarang       AS NamaBarang,
                    b.KategoriID       AS KategoriID,
                    b.NamaKategori     AS NamaKategori,
                    a.Stok             AS Stok,
                    a.StokMinimum      AS StokMinimum,
                    a.StokMaksimum     AS StokMaksimum,
                    a.HargaPokok       AS HargaPokok,
                    a.userInput        AS userInput
                FROM $this->table_ms AS a
                LEFT JOIN $this->table_kt AS b
                    ON b.KategoriID = a.KategoriID
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
        error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
    }
}



      public function UpdateData(){
        $nama_atter = "";
        $nama_atter_str = "";
        if (!empty($_FILES)) {
            $files = $_FILES['files'];
            $total = count($files['name']);
            for ($i = 0; $i < $total; $i++) {
                $file_name = $files['name'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_size = $files['size'][$i];
                $file_error = $files['error'][$i];
                $fileType = $files['type'][$i];

                if ($file_error !== UPLOAD_ERR_OK) {
                    return "Error uploading $file_name. Error code: $file_error<br>";
                }

                if(($fileType == "image/gif") || ($fileType == "image/jpeg") || ($fileType == "image/png") || ($fileType == "image/pjpeg")){
                            // $upload_dir = '../public/uploads_attachfile/';
                            $new_nama = $file_name;
                            $destination = FOLDER.$new_nama;
                            if (move_uploaded_file($file_tmp, $destination)) {
                           $nama_atter .= $new_nama . ",";
                            }
					    } 

            }

            $nama_atter_str = rtrim($nama_atter, ",");
        }

         $rawData =  $_POST["datahider"];
        $post = json_decode($rawData, true);
     
       
        $InventarisID = $this->test_input($post["InventarisID"]);
        $NamaBarang   = $this->test_input($post["NamaBarang"]);
        $Kategori     = $this->test_input($post["Kategori"]);
      //  $Stok         = $this->test_input($post["Stok"]);
        $StokMinimum  = $this->test_input($post["StokMinimum"]);
        $StokMaksimum = $this->test_input($post["StokMaksimum"]);
        $HargaPokok   = $this->test_input($post["HargaPokok"]);
        $nama_files_old = $post["document_files_old"];

        $userid       = $_SESSION['id_user'];
        $UpdatedAt    = date("Y-m-d H:i:s");

         
     if (!empty($nama_files_old)) {
            $this->hapusFileupload($nama_files_old, $InventarisID);
            // Gabungkan nama file lama dengan koma
            $gabungan_lama = implode(",", $nama_files_old);
            // Gabungkan dengan string baru
            $new_nama_atter = empty($nama_atter_str) === true ? $gabungan_lama :  $gabungan_lama . "," . $nama_atter_str;
        } else {
            // Jika kosong, pakai string baru saja
            $new_nama_atter = $nama_atter_str;
        }


        $query ="UPDATE $this->table_ms SET NamaBarang='{$NamaBarang}', KategoriID='{$Kategori}',
        StokMinimum='{$StokMinimum}',StokMaksimum='{$StokMaksimum}', HargaPokok='{$HargaPokok}' ,userEdit='{$userid}',UpdatedAt='{$UpdatedAt}',
        document_files='{$new_nama_atter}'
        WHERE InventarisID ='{$InventarisID}'
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

        private function hapusFileupload($nama_files_old, $InventarisID)
        {
            // Ambil nama file dari database
            $query = "SELECT document_files FROM $this->table_ms WHERE InventarisID='{$InventarisID}'";
            
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
     
        $InventarisID = $this->test_input($post["InventarisID"]);

        $query = "SELECT document_files FROM $this->table_ms WHERE InventarisID='{$InventarisID}'";
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
        $query2   ="DELETE FROM $this->table_msdetail   WHERE InventarisID ='{$InventarisID}'";
        $query2 .="DELETE FROM $this->table_ms   WHERE InventarisID ='{$InventarisID}'";
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

   
        $query="SELECT document_files FROM $this->table_ms WHERE InventarisID='{$idinvetarsi}'";
        $sql =$this->db->baca_sql($query);
		$document_files=odbc_result($sql,"document_files");
        
            $data =["document_files" =>$document_files];
            return $data;
         } catch (Exception $e){
            error_log("Error in getAllInventaris: " . $e->getMessage());
        
            // Return an empty array or handle the error as needed
            return [];
         }
      }


      //tambah stok dan kedetailstok 19/08/2025
       public function TambahStokdata(){
         $rawData = $_POST["datahider"];
        $post = json_decode($rawData, true);
            
        //$this->consol_war($post);

        $InventarisID = $this->test_input($post["InventarisID"]);
        $qty         = $this->test_input($post["qty"]);
        $keterangan  = $this->test_input($post["keterangan"]);
        $userid       = $_SESSION['id_user'];
        $jenistransaksi = "MASUK";
        $FlagUpdateStock ="+";

        $query = "INSERT INTO $this->table_msdetail(InventarisID,Qty,JenisTransaksi,Keterangan,UserID,FlagUpdateStock)
                  VALUES('{$InventarisID}','{$qty}','{$jenistransaksi}','{$keterangan}','{$userid}','{$FlagUpdateStock}')
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