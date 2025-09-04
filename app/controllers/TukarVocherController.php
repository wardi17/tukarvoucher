<?php

class TukarVocherController extends Controller
{
    private $tstukarVocherModel;

    public function __construct()
    {
        $this->tstukarVocherModel = $this->model('Ts_TukarVocherModel');
    }



        //untuk validasi voucher
        public function cekkodevoucher(){
            try {   
        // Retrieve data from the model
            $data = $this->tstukarVocherModel->cekkodevoucher(); // Assuming
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in TukarVocherController::cekkodevoucher: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
        }


    public function saveData()
    {
        try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->tstukarVocherModel->saveData();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in TukarVocherController::saveData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }




   public function listdata() {
    try {
        // Retrieve data from the model
        $data = $this->tstukarVocherModel->listdata(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in TukarVocherController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }


    public function getdetailberivoucher(){
            try {
        // Retrieve data from the model
        $data = $this->tstukarVocherModel->getdetailberivoucher(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in TukarVocherController::getdetailberivoucher: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }


        public function postingdata(){
                    try {
                // Retrieve data from the model
                $data = $this->tstukarVocherModel->postingdata(); // Assuming this method exists in your model
                // Check if data is empty
                if (empty($data)) {
                    $this->sendJsonResponse([], 200); // Return an empty array if no data found
                    return;
                }

                // Send the data as a JSON response
                $this->sendJsonResponse($data, 200);
            } catch (Throwable $e) {
                error_log('Error in TukarVocherController::postingdata: ' . $e->getMessage());
                $this->sendErrorResponse('Internal server error', 500);
            }
        }




        public function getdatadetailprint(){
            try {       
        // Retrieve data from the model
                $data = $this->tstukarVocherModel->getdatadetailprint(); // Assuming this method exists in your model
                // Check if data is empty
                if (empty($data)) {
                    $this->sendJsonResponse([], 200); // Return an empty array if no data found
                    return;
                }

                // Send the data as a JSON response
                $this->sendJsonResponse($data, 200);
            } catch (Throwable $e) {
                error_log('Error in TukarVocherController::getdatadetailprint: ' . $e->getMessage());
                $this->sendErrorResponse('Internal server error', 500);
            }   
        }
    //end kode baru



    public function gettokomerchat(){
        
    

          try {       
        // Retrieve data from the model
                $data =[
                        'HC','KK','MB','BTOUR','BMN'
                    ];                // Check if data is empty
                if (empty($data)) {
                    $this->sendJsonResponse([], 200); // Return an empty array if no data found
                    return;
                }

                // Send the data as a JSON response
                $this->sendJsonResponse($data, 200);
            } catch (Throwable $e) {
                error_log('Error in TukarVocherController::getdatadetailprint: ' . $e->getMessage());
                $this->sendErrorResponse('Internal server error', 500);
            }  
    }
   
}
