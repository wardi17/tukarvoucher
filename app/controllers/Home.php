<?php

class Home extends Controller{

    protected $userid;
    private $username;
      
    public function __construct()
	{	
	
		// $this->userid ="wardi";
		// $this->username ="123";

	
		if($_SESSION['session_login'] != 'sudah_login') {
			Flasher::setMessage('Login','Tidak ditemukan.','danger');
			header('location: '. base_url . '/login');
			exit;
		}else{
			$this->userid = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : "";
			$this->username =isset($_SESSION['username']) ? $_SESSION['username'] : "";
			if (empty($this->userid) || empty($this->username)) {
				header('location: '. base_url . '/login');
				exit(); // Pastikan script berhenti setelah redirect
			}
		} 
	} 

	public function index()
		{
		
			$data["userid"]= $this->userid;
			$data["username"]= $this->username;
			$data["pages"] ="admin_dashboard";
			$this->view('templates/header');
			$this->view('templates/sidebar', $data);
			$this->view('home/index',$data);
			$this->view('templates/footer');
		}


		public function index2(){
			$this->view('home/index2');
		}

  public function expordexcel(){
	    $this->view('home/expordexcel');
  }
 public function getharikerja(){

	$data= $this->model('homeModel')->getHarikerja($_POST);
	if(empty($data)){
		$data = null;
		echo json_encode($data);
	}else{
		echo json_encode($data);
	}

 }







   public function createdate(){
	$data= $this->model('homeModel')->saveData($_POST);
	if(empty($data)){
		$data = null;
		echo json_encode($data);
	}else{
		echo json_encode($data);
	}
   }


   public function tampildata(){
		$data= $this->model('homeModel')->GetTampil();
		if(empty($data)){
			$data = null;
			echo json_encode($data);
		}else{
			echo json_encode($data);
		}
   }



   public function Updatedate(){
	$data= $this->model('homeModel')->UpdateData($_POST);
	if(empty($data)){
		$data = null;
		echo json_encode($data);
	}else{
		echo json_encode($data);
	}
   }


   public function deletedate(){
	$data= $this->model('homeModel')->DeleteData($_POST);
	if(empty($data)){
		$data = null;
		echo json_encode($data);
	}else{
		echo json_encode($data);
	}
   }
}