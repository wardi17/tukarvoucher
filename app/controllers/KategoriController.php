<?php

class KategoriController  extends Controller
{
    private $msKategoriModel;

    public function __construct()
    {
        $this->msKategoriModel = $this->model('MsKategoriModel');
    }


    public function getidtampil(){
            try {
        // Retrieve data from the model
            $data = $this->msKategoriModel->getidtampil(); // Assuming this method exists in your model
            // Check if data is empty
            if (empty($data)) {
                $this->sendJsonResponse([], 200); // Return an empty array if no data found
                return;
            }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
        } catch (Throwable $e) {
            error_log('Error in KategoriController::listdata: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }

    
    public function saveData()
    {
        try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->msKategoriModel->saveData();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in KategoriController::saveData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }




   public function listdata() {
    try {
        // Retrieve data from the model
        $data = $this->msKategoriModel->getAllKategori(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in KategoriController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }




    public function UpdateData(){
          try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->msKategoriModel->UpdateData();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in KategoriController::updateData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }



    
   public function DeleteData() {
    try {
        // Retrieve data from the model
        $data = $this->msKategoriModel->DeleteData(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in KategoriController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
    }


    public function GetdocumentByID(){
		try {
        // Retrieve data from the model
        $data = $this->msKategoriModel->GetdocumentByID(); // Assuming this method exists in your model
        // Check if data is empty
        if (empty($data)) {
            $this->sendJsonResponse([], 200); // Return an empty array if no data found
            return;
        }

        // Send the data as a JSON response
        $this->sendJsonResponse($data, 200);
    } catch (Throwable $e) {
        error_log('Error in KategoriController::listdata: ' . $e->getMessage());
        $this->sendErrorResponse('Internal server error', 500);
    }
	}


    public function TambahStokdata(){
         try {
            // Validasi request (harus POST dan JSON)
            $this->validateRequest();

            // Simpan data via model
            $response = $this->msKategoriModel->TambahStokdata();

            // Kirim response sukses
            $this->sendJsonResponse(!empty($response) ? $response : null);
        } catch (InvalidArgumentException $e) {
            // Error validasi input

            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            // Error umum atau sistem
            error_log('Error in KategoriController::GagaltambahstokData: ' . $e->getMessage());
            $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
