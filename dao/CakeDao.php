<?php
	
	require_once __DIR__ . '/../db/DBConnector.php';
	require_once __DIR__ . '/../db/DBWrapper.php';

	class CakeDao extends DBConnector {

		private $db;
		private $tableName = 'cakes';

		function __construct(){
			parent::__construct();
			$this->log = Logger::getLogger(__CLASS__);
			$this->db = new DBWrapper($this->dbc);
		}

		public function getCakesList(){
			$this->log->info(__FUNCTION__ . SPACE . METHOD_STARTS);
			$query = "SELECT * FROM $this->tableName";
			$db = $this->db;
			$resultMap = $db->selectOperation($query);
			$this->closeConnection();
			$result['rowCount'] = $resultMap['row_count'];
			for($iterator = 0; $iterator < $resultMap['row_count']; $iterator++){
				$result['resultData'][$iterator]['cakeId'] = $resultMap['result_data'][$iterator]['cake_id'];
				$result['resultData'][$iterator]['cakeName'] = $resultMap['result_data'][$iterator]['cake_name'];
				$result['resultData'][$iterator]['cakeStatus'] = $resultMap['result_data'][$iterator]['cake_status'];
			}
			$this->log->info(__FUNCTION__ . SPACE . METHOD_ENDS);
			return $result;
		}
		
	}
	
?>