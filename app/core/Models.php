<?php

class Models{
	public $db;

	public function __construct()
	{
	
		$this->db = new Database;
	}
	public function consol_war($data){
		      echo "<pre>";
              print_r($data);
              echo "</pre>";
              die();
	}


    protected function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
		}

	

	protected function select($file,$table){
		
		$select = "SELECT ".$file." FROM ".$table;
		return $select;

	}

	protected function orderby($file){
		return " ORDER BY ".$file;
	}
	


	protected function requiremodels($model)
	{
		
		return require_once '../app/models/' . $model . '.php';
	}


	protected function  formatdate($string){
		$date =DateTime::createFromFormat('d/m/Y', $string);
		$format  = $date->format('Y-m-d');
		return $format;
	}
}