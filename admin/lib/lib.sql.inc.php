<?php
class CS_Database {
	/** 
	 * @var PDO
	 */
	public $db;	
	
	public function __construct($cfg) {
		$this->connect($cfg['host'] ,$cfg['user'], $cfg['password'], $cfg['schema'], $cfg['charset'], $cfg['type']);
	}
	public function connect($host,$user,$passwd,$schema,$charset="utf8",$type="mysql") {
		$dsn = "$type:host=$host;dbname=$schema";
		$this->db = new PDO($dsn,$user,$passwd);
		$this->execute("SET NAMES $charset");
	}

	//Extents Database Methods
	public function getArray($sql,$num_of_col=false) {
		if ($num_of_col >= 2) {
			if (false !== ($rs = $this->query($sql))) {
				$out = array();
				if ($num_of_col == 2) {
					while(($row = $rs->fetch(PDO::FETCH_NUM))) {
						$rowidx = array_shift($row); 
						$out[$rowidx] = array_shift($row);
					}
				} else {
					while(($row = $rs->fetch(PDO::FETCH_ASSOC))) {
						$rowidx = array_shift($row); 
						$out[$rowidx] = $row; 
					}
				}
				return $out;
			} else {
				return false;
			}
		} elseif ($num_of_col == 1) {
			return $this->getCol($sql);
		} else {
			return $this->getAssoc($sql);	
		}
	}
	public function getVars($sql) {
		$rs = $this->getRow($sql);	
		foreach ($rs as $key=>$val){
			global $$key;
			if (isset($$key) && P_Config::Debug ) {
				echo "warning : function getVars() overwrite the variable `$key`<br />\n";
			}
			$$key = $val;
		}
	}
	public function autoExecuteInsert($table,$record) {
		foreach($record as $key=>$val) {$record[$key] = $this->quote($val);}
		return $this->executeInsert("INSERT INTO `$table` (`".join("`,`",array_keys($record))."`)VALUES(".join(",",array_values($record)).")");
	}
	public function autoExecuteReplace($table,$record) {
		foreach($record as $key=>$val) {$record[$key] = $this->quote($val);}
		return $this->executeInsert("REPLACE INTO `$table` (`".join("`,`",array_keys($record))."`)VALUES(".join(",",array_values($record)).")");
	}
	public function autoExecuteUpdate($table,$record,$where = false) {
		foreach ($record as $c=>$v) {$sql[] = "`$c`=".$this->quote($v);}
		return $this->executeUpdate("UPDATE `$table` SET ".join(",",$sql)." WHERE $where");
	}
	public function executeInsert($sql) {
		if (false === $this->execute($sql)) {
			return false;
		} else {
			return $this->getInsertID();
		}
	}
	public function executeUpdate($sql) {
		return $this->execute($sql);
	}
	public function executeDelete($sql) {
		return $this->execute($sql);		
	}
	
	//Core Database Methods
	public function quote($str,$type = PDO::PARAM_STR) {
		if (PHP_VERSION >= "6") {
			return $this->db->quote($str,$type);
		}
		
		if (!get_magic_quotes_gpc()) {
		    $str = addslashes($str);
		}
		if ($type == PDO::PARAM_STR) {
			return "'$str'";
		} else {
			return $str;
		}
	}
	public function execute($sql) {
		return $this->db->exec($sql);
	}
	public function query($sql) {
		return $this->db->query($sql);
	}
	public function getRow($sql) {
		if (false === ($rs = $this->db->query($sql))) {
			return false;
		} else {
			return $rs->fetch(PDO::FETCH_ASSOC);
		}
	}
	public function getOne($sql) {
		if (false === ($rs = $this->db->query($sql))) {
			return false;
		} else {
			return $rs->fetch(PDO::FETCH_COLUMN);
		}
	}
	public function getAssoc($sql) {
		if (false === ($rs = $this->db->query($sql))) {
			return false;
		} else {
			return $rs->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	public function getCol($sql) {
		if (false === ($rs = $this->db->query($sql))) {
			return false;
		} else {
			return $rs->fetchAll(PDO::FETCH_COLUMN);
		}
	}
	public function getInsertID() {
		return $this->db->lastInsertId();
	}
	public function getErrorMsg(){
		return $this->db->errorInfo();
	}
	public function getErrorCode(){
		return $this->db->errorCode();
	}
}
?>