<?php

	require_once __DIR__ . '/../utils/Constants.php';
	require_once __DIR__ . '/../log4php/Logger.php';
	Logger::configure(__DIR__ . '/../config/logger-config.xml');

	class DBWrapper {
		private $db;
		private $log; 
		function __construct($dbObj){
			$this->db = $dbObj;
			$this->log = Logger::getLogger(__CLASS__);
		}

		public function runQuery($query){
			$this->log->info(__FUNCTION__ . SPACE . METHOD_STARTS);
			$stmt = $this->db->prepare($query);
			$stmt->execute();
			$result=$stmt->fetchall(PDO::FETCH_ASSOC);
			$this->log->info(__FUNCTION__ . SPACE . METHOD_ENDS);
			return $result;
		}

		public function insertOperation($tableName, $inputData, $spl = ''){
			$this->log->info(__FUNCTION__ . SPACE . METHOD_STARTS);
	        $output = array();
	    	$query = "INSERT INTO $tableName ";
	    	$ipDataCopy = array_keys($inputData);

	    	$tablecolumns = implode(',', $ipDataCopy);
	        foreach ($inputData as $key => $value) {
	            $inputData[$key] = mysqli_real_escape_string($this->db, $value);
	        }
	    	$tablevalues = implode("','", $inputData);
	    	$query .= "( ".$tablecolumns." ) VALUES ( '".$tablevalues."')";
			$this->log->debug(QUERY . NEW_LINE . $query);
	    	
	        if(mysqli_query($this->db, $query)){
	            $last_insert_id = mysqli_insert_id($this->db);
	            $output = array("status" => "success", "last_insert_id" => $last_insert_id, "affected_rows" => mysqli_affected_rows($this->db));
	        }else{
	            $output = array("status" => "failed", "error_details" => mysqli_error($this->db), "affected_rows" => mysqli_affected_rows($this->db));
	        }
	        //file_put_contents("testlog.log", "\n".$query."\nOutput : ".print_r($output, true), FILE_APPEND | LOCK_EX);
	        
	        $this->log->debug(QUERY_RESULT . NEW_LINE . print_r($output, true));
			$this->log->info(__FUNCTION__ . SPACE . METHOD_ENDS);
	        return $output;
	    }

	    public function updateOperation($tableName, $inputData, $whereClause){
			$this->log->info(__FUNCTION__ . SPACE . METHOD_STARTS);
	        $whereQuery = array(); 
	        $wherePart = '';
	        $query = "UPDATE $tableName SET ";
	        foreach ($inputData as $key => $value) {
	            $inputData[$key] = mysqli_real_escape_string($this->db, $value);
	            $query .= $key." = '".$inputData[$key]."',"; 
	        }
	        $query = substr($query, 0, -1); //removing the last comma

	        foreach ($whereClause as $key => $value) {
	            $whereClause[$key] = mysqli_real_escape_string($this->db, $value);
	            $whereQuery[] = $key." = '".$whereClause[$key]."'"; 
	        }

	        if($whereQuery)
	            $wherePart = implode(" AND ", $whereQuery); //Only AND for now TODO for other types

	        $query .= "  WHERE ".$wherePart;
	        $this->log->debug(QUERY . NEW_LINE . $query);

	        if(mysqli_query($this->db, $query)){
	            //$last_insert_id = mysqli_insert_id($this->db);
	            $output = array("status" => "success", "affected_rows" => mysqli_affected_rows($this->db));
	        }else{
	            $output = array("status" => "failed", "error_details" => mysqli_error($this->db), "affected_rows" => mysqli_affected_rows($this->db));
	        }
	        //file_put_contents("testlog.log", "\n".$query."\nOutput : ".print_r($output, true), FILE_APPEND | LOCK_EX);

	        $this->log->debug(QUERY_RESULT . NEW_LINE . print_r($output, true));
			$this->log->info(__FUNCTION__ . SPACE . METHOD_ENDS);
	        return $output;
	    }

	    public function selectOperation($query) {
			$this->log->info(__FUNCTION__ . SPACE . METHOD_STARTS);
			$this->log->debug(QUERY . NEW_LINE . $query);
	    	$output = array();
	    	$result = mysqli_query($this->db, $query);
	    	$resultData = array();
	    	$numRows = mysqli_num_rows($result);
	    	$output['row_count'] = $numRows;
	    	if($numRows > 0){
	    		while($row = mysqli_fetch_assoc($result)){
	    			$resultData[] = $row;
	    		}
	    		$output['result_data'] = $resultData;
	    	}
			$this->log->info(__FUNCTION__ . SPACE . METHOD_ENDS);
	    	return $output;
	    }

	}

?>