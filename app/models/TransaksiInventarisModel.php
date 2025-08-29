<?php
include("MsInventarisModel.php");
class TransaksiInventarisModel extends MsInventarisModel{
    private $tabel_trans_pgl = "[crm-bmi].[dbo].ts_Pengambilan_Inventaris";


    public function getDataInventaris(){
        
         try {
        // Siapkan query SQL
        $query = "
            SELECT 
                InventarisID,NamaBarang
            FROM $this->table_ms 
            ORDER BY KategoriID ASC
        ";

    //    $this->consol_war($query);
        // Eksekusi query
        $result = $this->db->baca_sql($query);

        // Validasi hasil eksekusi query
        if (!$result) {
            throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
        }

        // Ambil data hasil query
        $datas = [];
        while (odbc_fetch_row($result)) {
             $InventarisID = rtrim(odbc_result($result, 'InventarisID'));
             $NamaBarang   = rtrim(odbc_result($result, 'NamaBarang'));
          

            $datas[] = [
                "id"         => $InventarisID,
                "namabarang" => $NamaBarang,
                
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


    public function getAllInventarisDetail(){
        try{

     
        $rawData = file_get_contents("php://input");
        $post = json_decode($rawData, true);
        $InventarisID = $this->test_input($post["IDinventaris"]);
        $query ="USP_GetInventarisWithStok '{$InventarisID}';
        ";
   
            // $this->consol_war($query);
             $result = $this->db->baca_sql2($query);
        
        // Check if the query execution was successful
        if (!$result) {
            throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
        }
           $data = [];
        while ($row = odbc_fetch_array($result)) {
            $data= $row;
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


    public function SaveTransaksi(){
        try{
          $rawData = file_get_contents("php://input");
          $post = json_decode($rawData, true);
        //  $this->consol_war($post);
          $InventariID = $post["InventariID"];
          $Stok = $post["Stok"];
          $Invernama = $post["Invernama"];
          $qty = $post["qty"];
          $ket  = $post["ket"];
          $userid = $_SESSION['id_user'];
        $jenistransaksi = "KELUAR";
        $FlagUpdateStock ="-";

        $query = "INSERT INTO $this->table_msdetail(InventarisID,Qty,JenisTransaksi,Keterangan,UserID,FlagUpdateStock)
                  VALUES('{$InventariID}','{$qty}','{$jenistransaksi}','{$ket}','{$userid}','{$FlagUpdateStock}')
                ";
          $query .="INSERT INTO $this->tabel_trans_pgl (InventarisID,UserID,Jumlah,Keterangan)
          VaLUES('{$InventariID}','{$userid}','{$qty}','{$ket}')";

            //$this->consol_war($query);
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
        } catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
        }
    }


    public function listtransaksi(){
          try{
                $query="SELECT TS.TransaksiID,TS.InventarisID,TS.UserID,TS.Jumlah,TS.TanggalPengambilan,TS.keterangan,
                 MS.NamaBarang,MS.Stok,US.nama, DT.TransaksiIDDetail 
                 FROM  $this->tabel_trans_pgl AS TS
                    LEFT JOIN $this->table_ms as MS
                    ON  MS.InventarisID = TS.InventarisID
                    LEFT JOIN  $this->table_user AS US
                    ON US.id_user = TS.UserID
                    LEFT JOIN $this->table_msdetail AS DT 
                    ON DT.InventarisID = TS.InventarisID 
                    AND DT.Qty = TS.Jumlah 
                    AND DT.UserID = TS.UserID 
                    AND DT.FlagUpdateStock = '-' 
                    WHERE TS.statusposting='N'
                  ORDER BY TransaksiID ASC ";

                 //$this->consol_war($query);
                $result = $this->db->baca_sql($query);
                
                // Check if the query execution was successful
                if (!$result) {
                    throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
                }
                $data = [];
                while ($row = odbc_fetch_array($result)) {
                    $data[]= $row;
                }

                // Optional: Log or handle the data as needed
                //$this->consol_war($data); // Ensure this method is defined and does what you expect

             return $data;
        }catch (Exception $e) {
            
            // Log the error message for debugging
            error_log("Error in getAllInventaris: " . $e->getMessage());
            
            // Return an empty array or handle the error as needed
            return [];
        }

    }

    public function UpdateTransaksi(){
        try{
          $rawData = file_get_contents("php://input");
          $post = json_decode($rawData, true);
          //$this->consol_war($post);
          $InventariID  = $post["InventariID"];
      
          $qty          = $post["qty"];
          $ket          = $post["ket"];
          $idtrans      = $post["idtrans"];
          $oldqty       = $post["oldqty"];
          $oldinventarsiid  = $post["oldinventarsiid"];
          $userid       = $_SESSION['id_user'];
         $date_update   = date("Y-m-d H:i:s");

         $transaksiiddetail =$post["transaksiiddetail"];
        //  $query ="DECLARE  @stok FLOAT;
        //        SET @stok =(select Stok from $this->table_ms  WHERE  InventarisID='{$oldinventarsiid}');
        //        UPDATE $this->table_ms SET Stok = @stok +'{$oldqty}' WHERE  InventarisID='{$oldinventarsiid}'
        //        ";
         $query   = "UPDATE  $this->table_msdetail  SET Qty='{$qty}',Keterangan='{$ket}'  
          WHERE TransaksiIDDetail ='{$transaksiiddetail}' AND InventarisID='{$InventariID}'";
          $query .="UPDATE  $this->tabel_trans_pgl 
          SET InventarisID='{$InventariID}',UserID_update='{$userid}',Jumlah='{$qty}',Keterangan='{$ket}',
            date_update='{$date_update}' WHERE TransaksiID='{$idtrans}'";
    //$this->consol_war($query);
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
        } catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
        }
    }


    public function DeleteTransaksi(){
      
  try{
      $rawData = file_get_contents("php://input");
          $post = json_decode($rawData, true);


        $idtrans = $this->test_input($post["idtrans"]);
        $transaksiiddetail = $this->test_input($post["transaksiiddetail"]);
         $query ="DELETE FROM $this->table_msdetail WHERE TransaksiIDDetail ='{$transaksiiddetail}' AND FlagUpdateStock='-' ";
        $query .="DELETE FROM $this->tabel_trans_pgl WHERE TransaksiID='{$idtrans}'";
        $result = $this->db->baca_sql($query);
            // Buat response
                if ($result) {
                    $pesan = [
                        'nilai' => 1,
                        'error' => 'Berhasil Hapus data'
                    ];
                } else {
                    $pesan = [
                        'nilai' => 0,
                        'error' => 'Data Gagal Hapus'
                    ];
                }

                return $pesan;
         } catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
        }
    }


    public function PostingTransaksi(){
          try{

       $rawData = file_get_contents("php://input");
          $post = json_decode($rawData, true);
  $idtrans = $this->test_input($post["idtrans"]);
        $userid       = $_SESSION['id_user'];
        $query ="UPDATE  $this->tabel_trans_pgl
        SET statusposting='Y',UserID_Posting='{$userid}',dateposting=GETDATE()
         WHERE TransaksiID='{$idtrans}'";


        $result = $this->db->baca_sql($query);
            // Buat response
                if ($result) {
                    $pesan = [
                        'nilai' => 1,
                        'error' => 'Berhasil Posting data'
                    ];
                } else {
                    $pesan = [
                        'nilai' => 0,
                        'error' => 'Data Gagal Posting'
                    ];
                }

                return $pesan;
         } catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
        }
    }


    public function listlaporan(){
        try{

         $rawData = file_get_contents("php://input");
          $post = json_decode($rawData, true);

           $tgl_from = $post["tgl_from"];
           $tgl_to = $post["tgl_to"];
           $userid = $post["userid"];
            $inventaris_type = $post["inventaris_type"];

           $date_from = $this->formatdate($tgl_from);
           $date_to = $this->formatdate($tgl_to)." 23:59:59";
  
           $query="USP_LaporanInventarisInOut '{$date_from}','{$date_to}','{$inventaris_type}'";

      
            $result = $this->db->baca_sql2($query);
            if (!$result) {
                throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
            }

            $data = [];
            // result set 1
            while ($row = odbc_fetch_array($result)) {
                $data[] = $row;
            }



                 //$this->consol_war($data);
            return $data;
         } catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
        }
    }


    public function laporan_tools(){
        try{                        
            $rawData = file_get_contents("php://input");
            $post = json_decode($rawData, true);
            $tanggal = $post["tanggal"];
            $userid = $post["userid"];
            $date = $this->formatdate($tanggal)." 23:59:59";
            $query = "USP_LaporanTools '{$date}'";
            //$this->consol_war($query);
            $result = $this->db->baca_sql2($query);
            if (!$result) {
                throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
            }
            $data = [];
            // result set 1
            while ($row = odbc_fetch_array($result)) {
                $data[] = $row;
            }
            //$this->consol_war($data);
            return $data;
        }
        catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        // Return an empty array or handle the error as needed
        return [];
        }
    }
}