<?php

class BerikanVocherController extends Controller
{
    private $tsberikanVocherModel;

    public function __construct()
    {
        $this->tsberikanVocherModel = $this->model('Ts_BerikanVocherModel');
    }

    public  function getcabang(){
            try {
        // Retrieve data from the model
            $data = $this->tsberikanVocherModel->getcabang(); // Assuming this method exists in your model
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in BerikanVocherController::getcabang: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function getDataCustomer(){
               try {
        // Retrieve data from the model
            $data = $this->tsberikanVocherModel->getDataCustomer(); // Assuming this method exists in your model
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in BerikanVocherController::listdata: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function getnopo(){
        try {
        // Retrieve data from the model
            $data = $this->tsberikanVocherModel->getnopo(); // Assuming this method exists in your model
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in BerikanVocherController::getnopo: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }


        //untuk validasi voucher
        public function cekkodevoucher(){
            try {   
        // Retrieve data from the model
            $data = $this->tsberikanVocherModel->cekkodevoucher(); // Assuming
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in BerikanVocherController::cekkodevoucher: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
        }


    public function saveData()
    {
        try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->tsberikanVocherModel->saveData();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in BerikanVocherController::saveData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }




   public function listdata() {
    try {
        // Retrieve data from the model
        $data = $this->tsberikanVocherModel->listdata(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in BerikanVocherController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }


    public function getdetailberivoucher(){
            try {
        // Retrieve data from the model
        $data = $this->tsberikanVocherModel->getdetailberivoucher(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in BerikanVocherController::getdetailberivoucher: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }


        public function postingdata(){
                    try {
                // Retrieve data from the model
                $data = $this->tsberikanVocherModel->postingdata(); // Assuming this method exists in your model
                // Check if data is empty
                if (empty($data)) {
                    $this->sendJsonResponse([], 200); // Return an empty array if no data found
                    return;
                }

                // Send the data as a JSON response
                $this->sendJsonResponse($data, 200);
            } catch (Throwable $e) {
                error_log('Error in BerikanVocherController::postingdata: ' . $e->getMessage());
                $this->sendErrorResponse('Internal server error', 500);
            }
        }




        public function getdatadetailprint(){
            try {       
        // Retrieve data from the model
                $data = $this->tsberikanVocherModel->getdatadetailprint(); // Assuming this method exists in your model
                // Check if data is empty
                if (empty($data)) {
                    $this->sendJsonResponse([], 200); // Return an empty array if no data found
                    return;
                }

                // Send the data as a JSON response
                $this->sendJsonResponse($data, 200);
            } catch (Throwable $e) {
                error_log('Error in BerikanVocherController::getdatadetailprint: ' . $e->getMessage());
                $this->sendErrorResponse('Internal server error', 500);
            }   
        }
    //end kode baru

   
}
