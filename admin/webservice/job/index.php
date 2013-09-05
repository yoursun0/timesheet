<?php
include_once("../../config.php");
include_once('../../lib.php');

class job {
	/**
	 * database object
	 *
	 * @var CS_Database
	 */
	private $db;
	private function dbConnect(){
		if (isset($this->db)) {
			return $this->db;
		}
		global $DB_CONFIG;
		$this->db = new CS_Database($DB_CONFIG);
	}
	public function addReferral($job_id,$main_ans,$ext_ans){
		$this->dbConnect();
	}
}

$server = new SoapServer(null,array('uri'=>""));
$server->setClass('job');
$server->handle();
?>