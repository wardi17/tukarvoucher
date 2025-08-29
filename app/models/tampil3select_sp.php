
 function listlaporan(){
        try{

         $rawData = file_get_contents("php://input");
          $post = json_decode($rawData, true);

           $tgl_from = $post["tgl_from"];
           $tgl_to = $post["tgl_to"];
           $userid = $post["userid"];
            $inventaris_type = $post["inventaris_type"];

           $date_from = $this->formatdate($tgl_from);
           $date_to = $this->formatdate($tgl_to)." 23:59:59";
  
           $query="USP_LaporanInventarsi '{$date_from}','{$date_to}'";

           $this->consol_war($query);
            $result = $this->db->baca_sql($query);
            if (!$result) {
                throw new Exception("Query execution failed: " . odbc_errormsg($this->db));
            }

            $data = [
                'DetailTransaksi' => [],
                'RingkasanTotal' => [],
                'RingkasanKategori' => []
            ];

            // result set 1
            while ($row = odbc_fetch_array($result)) {
                $data['DetailTransaksi'][] = $row;
            }

            // pindah ke result set 2
            if (odbc_next_result($result)) {
                while ($row = odbc_fetch_array($result)) {
                    $data['RingkasanTotal'][] = $row;
                }
            }

            // pindah ke result set 3
            if (odbc_next_result($result)) {
                while ($row = odbc_fetch_array($result)) {
                    $data['RingkasanKategori'][] = $row;
                }
            }


            return $data;
         } catch(Exception $e){
         error_log("Error in getAllInventaris: " . $e->getMessage());
        
        // Return an empty array or handle the error as needed
        return [];
        }
    }