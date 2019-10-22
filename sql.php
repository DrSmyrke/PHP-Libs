<?php
function connectDB($user_name, $pass, $db){
    $connect_db = mysqli_connect('localhost', $user_name, $pass, $db) or die("Не могу соединиться с MySQL.");
	mysqli_select_db( $connect_db, $db );
    return $connect_db;
}
 
function r_normal_array_DB($data_query){
    for ($data = []; $row = mysqli_fetch_assoc($data_query); $data[] = $row){};
    return $data;
}

function selectDB($username, $userpassword, $tablename)
{
	$connect_db = connectDB( $username, $userpassword, $tablename );
	return $connect_db;
}

function deleteDataDB($connect_db, $table, $query, $debug = false){
    $counter = 0;
    $total = count($query); 
    $where = "WHERE ";
    foreach($query as $key => $val){
        $counter++;
        if(is_numeric($val)){
            $where = $where." "."`$key` = $val"; 
        } else{
            $where = $where." "."`$key` LIKE '$val'"; 
        }
        
        if($counter == $total){
            $where = $where;
        } else{
            
            $where = $where." AND ";
        }
    }
    
    $delete = "DELETE FROM "."`$table`".$where.";"; 
    if($debug) print_r($delete);
    mysqli_query($connect_db,"SET NAMES `utf8`"); 
	mysqli_query($connect_db,"SET CHARACTER SET `utf8`");
	mysqli_query($connect_db,"SET SESSION collation_connection = `utf8_general_ci`");
    return mysqli_query($connect_db, $delete);
}

function addDataDB($connect_db, $table, $query, $debug = false){
    $counter = 0;
    $insert_f = true;
    $total = count($query);
    $data = "";
    $colls = "";
    foreach($query as $key => $val){
        $counter++;
        if(is_numeric($key)){
            $data = $data . "'$val'";
            if($counter != $total) 
            {
                $data = $data . ",";
            }
            else{
               $data = $data; 
            }
        }
        else{
            $insert_f = false;
            $data = $data . "'$val'";
            $colls = $colls. "`$key`"." ";
            if($counter != $total) 
            {
                $data = $data . ",";
                $colls = $colls.",";
            }
            else{
              $data = $data; 
              $colls = $colls;
            }
        }
        
        
    }
    
    if($insert_f == false){
        $colls = " (".$colls.") ";
    }
    

    
    $insert = "INSERT INTO "."`$table`"."$colls"." VALUES (". $data .");"; 
    if($debug) print_r($insert);
    mysqli_query($connect_db,"SET NAMES `utf8`"); 
	mysqli_query($connect_db,"SET CHARACTER SET `utf8`");
	mysqli_query($connect_db,"SET SESSION collation_connection = `utf8_general_ci`");
    return mysqli_query($connect_db, $insert);
}

function updateDataDB($connect_db, $table, $query, $where, $debug = false)
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
    foreach($where as $key => $val){
        if(!is_numeric($val)){
           $cond = $cond."`$key`"." LIKE "."`$val`"."\n"; 
        }else{
            $cond = $cond."`$key`"." = "."$val"."\n"; 
        }
        
        if(!$counter == $total){
            $cond = $cond." AND ";
        }else{
            $cond = $cond;
        }
       
    }
    
    $update = "UPDATE "."`$table` SET "." $data ".$cond.";"; 
    if($debug) print_r($update);
    //print_r("UPDATE  `tempD`.`tb_color` SET  `color` =  'Многоцветный 2' WHERE  `tb_color`.`id` =6;");
    mysqli_query($connect_db,"SET NAMES `utf8`"); 
	mysqli_query($connect_db,"SET CHARACTER SET `utf8`");
	mysqli_query($connect_db,"SET SESSION collation_connection = `utf8_general_ci`");
    return mysqli_query($connect_db, $update);
    
}

function getDataDB($connect_db, $table, $query, $sort = null, $random = false, $limit = null, $limitStart = null, $debug = false){
    $counter = 0;
    $where = "";
    $data = "";
    $total = count($query);
    $where_f = true;
    
    if(array_key_exists(0,$query) && $query[0] == '*'){
        $data = " * ";    
        foreach($query as $key => $val){
            $counter++;
            $where_buf = "OR";
            if(!is_numeric($key)){
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
                    
                    if($counter == $total){
                       $where = $where;
                    } else{
                       $where = $where." ".$where_buf." ";
                    }
                
            }
        }
    }else{
        foreach($query as $key => $val){
            $counter++;   
            $where_buf = "OR";
            if(is_numeric($key)){
               if($counter == $total){
                    $data = $data."`$key`";
                } else{
                   $data = $data."`$key`,";
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
            }
           if($counter == $total){
                $data = $data."`$key`";
                $where = $where;
            } else{
               $data = $data."`$key`,";
               $where = $where." ".$where_buf." ";
            }
        }


    }
	
    $select = "SELECT ".$data." FROM "."`$table`"." ".$where;
	
	if( !is_null($sort) && $random ) $select .= " ORDER BY $sort ASC";
	if( is_null($sort) && $random ) $select .= " ORDER BY RAND()";
	if( !is_null($limit) && is_numeric($limit) ){
		$select .= " LIMIT ";
		if( !is_null($limitStart) && is_numeric($limitStart) ) $select .= $limitStart.",";
		$select .= " ".$limit;
	}
	
	$select .= ";";
    if($debug) print_r($select);
	mysqli_query($connect_db,"SET NAMES `utf8`"); 
	mysqli_query($connect_db,"SET CHARACTER SET `utf8`");
	mysqli_query($connect_db,"SET SESSION collation_connection = `utf8_general_ci`");
    
    $result = mysqli_query($connect_db, $select);
    if(!$result) return array();
    return r_normal_array_DB($result);
    
}

?>