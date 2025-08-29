<?php

class InventarisController extends Controller
{
    private $msInventarisModel;

    public function __construct()
    {
        $this->msInventarisModel = $this->model('MsInventarisModel');
    }


    public function GetKatgori(){
            try {
        // Retrieve data from the model
            $data = $this->msInventarisModel->GetKatgori(); // Assuming this method exists in your model
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in InventarisController::listdata: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }

    
    public function saveData()
    {
        try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->msInventarisModel->saveData();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in InventarisController::saveData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }





//batas kode

   public function listdata() {
    try {
        // Retrieve data from the model
        $data = $this->msInventarisModel->getAllInventaris(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in InventarisController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }




    public function UpdateData(){
          try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->msInventarisModel->UpdateData();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in InventarisController::updateData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }



    
   public function DeleteData() {
    try {
        // Retrieve data from the model
        $data = $this->msInventarisModel->DeleteData(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in InventarisController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }


    public function GetdocumentByID(){
		try {
        // Retrieve data from the model
        $data = $this->msInventarisModel->GetdocumentByID(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in InventarisController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
	}


    public function TambahStokdata(){
         try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->msInventarisModel->TambahStokdata();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in InventarisController::GagaltambahstokData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
