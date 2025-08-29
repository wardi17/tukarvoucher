<?php

class Report extends Controller{


    protected $userid;
	protected $username;

    public function __construct()
	{	
		
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

		   $this->view('templates/header');		
			$data["pages"]="lap";
			$this->view('templates/sidebar', $data);
			 $this->view('report/index',$data);
			 $this->view('templates/footer');
	
    }



	public function tools(){
		  $data["userid"]= $this->userid;
		$data["username"]= $this->username;

		   $this->view('templates/header');		
			$data["pages"]="laptools";
			$this->view('templates/sidebar', $data);
			 $this->view('report/tools',$data);
			 $this->view('templates/footer');
	}
	

}