<?php
function sql_help()
{
	print "\$sql = new Sql;<br>\n";
	print "\$res = \$sql->init( serverAddr, userName, password, dataBase ); //return true or error<br>\n";
	print "\$res = \$sql->connect(); //return true or error<br>\n";
	print "\$sql->disconnect();<br>\n";
	print "\$res = \$sql->selectDB( dataBase ); //return true or error<br>\n";
	print "\$res = \$sql->deleteData( table, query, debug = false ); //return true or error<br>\n";
	print "\$res = \$sql->addData( table, query, debug = false ); //return true or error<br>\n";
	print "\$res = \$sql->updateData( table, query, where, debug = false ); //return true or error<br>\n";
	print "\$data = \$sql->getData( table, query, sort = null, random = false, limit = null, limitStart = null, debug = false ); //return Array or error. (reverse sotring if first symbol sort = ! )<br>\n";
	print "\$string = \$sql->getErrorString(); //return error string<br>\n";
	print "\$id = \$sql->getLastInsertID(); //return last ID or 0 from non AUTO_INCREMENT fields or false<br>\n";
}

class Sql
{
	private $serverAddr				= "localhost";
	private $userName				= "";
	private $password				= "";
	private $dataBase				= "";
	private $connect_db				= "";
	private $success				= false;

	public function init( $serverAddr, $userName, $password, $dataBase = "" )
	{
		if( $serverAddr == "" )		return "SQL ERROR: serverAddr";
		if( $userName == "" )		return "SQL ERROR: userName";
		if( $password == "" )		return "SQL ERROR: password";
		if( $dataBase == "" )		return "SQL ERROR: dataBase";

		$this->serverAddr			= $serverAddr;
		$this->userName				= $userName;
		$this->password				= $password;
		$this->dataBase				= $dataBase;

		return true;
	}

	public function selectDB( $dataBase )
	{
		if( $dataBase == "" )		return "SQL ERROR: dataBase";
		if( $this->success ){
			mysqli_close( $this->connect_db );
		}
		$this->dataBase				= $dataBase;
		return Sql::connect();
	}

	public function connect()
	{
		mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
		$this->connect_db = mysqli_connect( $this->serverAddr, $this->userName, $this->password, $this->dataBase);
		
		if( $this->connect_db == false ){
			$this->success = false;
			return false;
		}

		$this->success = mysqli_select_db( $this->connect_db, $this->dataBase );
		return $this;
	}

	public function disconnect()
	{
		mysqli_close( $this->connect_db );
	}

	public function getConnectErrorString()
	{
		return mysqli_connect_error();
	}

	public function getErrorString()
	{
		return mysqli_error( $this->connect_db );
	}

	public function deleteData( $table, $query, $debug = false )
	{
		$counter = 0;
		$total = count($query);
		$where = "WHERE ";
		foreach($query as $key => $val){
			$counter++;
			if(is_numeric($val)){
				$where = $where." "."`$key` = $val";
			}else{
				$where = $where." "."`$key` LIKE '$val'";
			}

			if($counter == $total){
				$where = $where;
			}else{
				$where = $where." AND ";
			}
		}

		$delete = "DELETE FROM "."`$table`".$where.";";
		if( $debug ) print_r( $delete );
		mysqli_query( $this->connect_db,"SET NAMES `utf8`" );
		mysqli_query( $this->connect_db,"SET CHARACTER SET `utf8`" );
		mysqli_query( $this->connect_db,"SET SESSION collation_connection = `utf8_general_ci`" );
		return mysqli_query( $this->connect_db, $delete);
	}

	public function addData( $table, $query, $debug = false )
	{
		$counter = 0;
		$insert_f = true;
		$total = count($query);
		$data = "";
		$colls = "";
		foreach($query as $key => $val){
			$counter++;
			if(is_numeric($key)){
				$data = $data . "'$val'";
				if($counter != $total){
					$data = $data . ",";
				}else{
				   $data = $data;
				}
			}else{
				$insert_f = false;
				$data = $data . "'$val'";
				$colls = $colls. "`$key`"." ";
				if($counter != $total) {
					$data = $data . ",";
					$colls = $colls.",";
				}else{
					$data = $data;
					$colls = $colls;
				}
			}
		}

		if($insert_f == false){
			$colls = " (".$colls.") ";
		}

		$insert = "INSERT INTO "."`$table`"."$colls"." VALUES (". $data .");";
		if( $debug ) print_r( $insert );
		mysqli_query($this->connect_db,"SET NAMES `utf8`");
		mysqli_query($this->connect_db,"SET CHARACTER SET `utf8`");
		mysqli_query($this->connect_db,"SET SESSION collation_connection = `utf8_general_ci`");
		return mysqli_query($this->connect_db, $insert);
	}

	public function updateData( $table, $query, $where, $debug = false )
	{
		$counter = 0;
		$total = count($query);
		$data = "";
		foreach($query as $key => $val){
			$counter++;
			if( is_int($val) or is_float($val) ){
				$data = $data . "`$key` = ".$val ;
			}else{
				$data = $data . "`$key` = "."'$val'" ;
			}

			if($counter != $total){
				$data = $data . ",";
			}else{
				$data = $data;
			}
		}

		$cond = "WHERE ";
		$total = count($where);
		$counter = 0;
		foreach($where as $key => $val){
			$counter++;
			if(!is_numeric($val)){
			   $cond = $cond."`$key`"." LIKE "."'$val'"."\n";
			}else{
				$cond = $cond."`$key`"." = "."$val"."\n";
			}

			if( $counter != $total ){
				$cond = $cond." AND ";
			}else{
				$cond = $cond;
			}
		}

		$update = "UPDATE "."`$table` SET "." $data ".$cond.";";
		if( $debug ) print_r( $update );
		//print_r("UPDATE  `tempD`.`tb_color` SET  `color` =  'Многоцветный 2' WHERE  `tb_color`.`id` =6;");
		mysqli_query( $this->connect_db, "SET NAMES `utf8`" );
		mysqli_query( $this->connect_db, "SET CHARACTER SET `utf8`" );
		mysqli_query( $this->connect_db, "SET SESSION collation_connection = `utf8_general_ci`" );
		return mysqli_query( $this->connect_db, $update );
	}

	public function getData( $table, $query = array( "*" ), $sort = null, $random = false, $limit = null, $limitStart = null, $debug = false )
	{
		$counter = 0;
		$where = "";
		$data = "";
		$total = count($query);
		$where_f = true;

		if( array_key_exists( 0, $query ) && $query[0] == '*' ){
			$data = " * ";
			foreach($query as $key => $val){
				$counter++;
				$where_buf = "OR";
				if(!is_numeric($key)){
						if($where_f == true){
							$where = "WHERE ";
							$where_f = false;
						}
						if( is_string( $val ) ){
							if($val[0] == "&"){
								$val = substr($val,1);
								$where_buf = "AND";
							}
						}
						if( is_int($val) or is_float($val) ){
							$where = $where. "`$key`"." = ".$val."\n";
						}else {
							$where = $where."`$key`"." LIKE "."'$val'"."\n";
						}
						if($counter == $total){
						   $where = $where;
						}else{
						   $where = $where." ".$where_buf." ";
						}
				}
			}
		}else{
			foreach($query as $key => $val){
				$where_buf = "OR";
				if(is_numeric($key)){
					if(++$counter == $total){
						$data = $data."`$val`";
					}else{
					   $data = $data."`$val`,";
					}
				}else{
					if($where_f == true){
						$where = "WHERE ";
						$where_f = false;
					}
					if($val[0] == "&"){
						$val = substr($val,1);
						$where_buf = "AND";
					}
					if( is_int($val) or is_float($val) ){
						$where = $where. "`$key`"." = ".$val."\n";
					} else {
						$where = $where."`$key`"." LIKE "."'$val'"."\n";
					}
					if(++$counter == $total){
						$data = $data."`$key`";
						$where = $where;
					} else{
					   $data = $data."`$key`,";
					   $where = $where." ".$where_buf." ";
					}
				}
			}
		}

		$select = "SELECT ".$data." FROM "."`$table`"." ".$where;

		if( !is_null($sort) && !$random ){
			if( $sort[0] == "!" ){
				$sort = substr( $sort, 1 );
				$select .= " ORDER BY `$sort` DESC";
			}else{
				$select .= " ORDER BY `$sort` ASC";
			}
		}
		if( is_null($sort) && $random ) $select .= " ORDER BY RAND()";
		if( !is_null($limit) && is_numeric($limit) ){
			$select .= " LIMIT ";
			if( !is_null($limitStart) && is_numeric($limitStart) ) $select .= $limitStart.",";
			$select .= " ".$limit;
		}

		$select .= ";";
		if( $debug ) print_r( $select );
		mysqli_query( $this->connect_db, "SET NAMES `utf8`" );
		mysqli_query( $this->connect_db, "SET CHARACTER SET `utf8`" );
		mysqli_query( $this->connect_db, "SET SESSION collation_connection = `utf8_general_ci`" );

		$result = mysqli_query($this->connect_db, $select);
		if(!$result) return array();
		return Sql::r_normal_array_DB($result);
	}

	private function r_normal_array_DB( $data_query )
	{
		for ($data = []; $row = mysqli_fetch_assoc($data_query); $data[] = $row){};
		return $data;
	}

	public function getLastInsertID()
	{
		if( $this->success ){
			return mysqli_insert_id( $this->connect_db );
		}
		
		return false;
	}
}
?>
