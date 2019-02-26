<?php
require_once("Common.php");
require_once("Config.php");

class DBHandler
{

    function getConnection()
    {
	var_dump("establishing connection");

    	// Create connection
	$conn = new \MySQLi(Config::$servername, Config::$username, Config::$password, Config::$dbname);
	// Check connection
	if ($conn->connect_error) {
    		die("Connection failed: " . $conn->connect_error);
		var_dump("Connection failed");
	} 
	return $conn;
     }

    function showAllIPs()
    {	
	$conn=$this->getConnection();
	$sql="SELECT * FROM nagios_clients;";
        $result = $conn->query($sql);
	var_dump("total rows are $result->num_rows");
	while($row = mysqli_fetch_array($result))
	{ 
           var_dump("       " . $row['id'] . "      " . $row['ip'] . "      " . $row['app_name'] . "      " . $row['instance_id'] . " ");
	}
	$conn->close();
    }

    function isConfigExists($ip,$app_name)
    {
	if(exec('egrep '. $ip . ' ' . Common::$allhosts_file_path))
	{
		return true;
	}
	else
	{
		return false;
	}
    }

    function isRowExists($conn,$instance_id,$ip,$app_name)
    {
	if(empty($conn) || strlen($instance_id)==0 || strlen($ip)==0 || strlen($app_name)==0)
	{
		var_dump("one of the values is empty $instance_id $ip $app_name");
		return true;
	}
	
	$sql="SELECT * FROM nagios_clients where instance_id='" . $instance_id . "' and ip='" . $ip . "' and app_name='" . $app_name . "';";
	var_dump($sql);
        $result = $conn->query($sql);
       var_dump($result);
        if ($result->num_rows == 0) {
		return false;
	}
	else
	{
		return true;
	}
    }

    function deleteIps($str_ip_to_delete)
    {
	$conn=$this->getConnection();
	$sql="delete from nagios_clients where ip in (" . $str_ip_to_delete . ");";
	var_dump($sql);
	$result= $conn->query($sql);
	var_dump($result);
	$conn->close();
	return true;	
    }

    function insertClientDetails($inputXML)
    {

	$conn=$this->getConnection();

	if(!$this->isRowExists($conn,$inputXML->instance_id,$inputXML->ip,$inputXML->app_name))
	{
		$sql="insert into nagios_clients(instance_id,ip,app_name) values ('" . $inputXML->instance_id . "','" . $inputXML->ip . "','" . $inputXML->app_name . "');";
		var_dump($sql);
		$result = $conn->query($sql);
		var_dump($result);
		$conn->close();
		return true;
	}
	else
	{

		//DB has row but check if this master/slave machine has actually configuration available or not
		if(!$this->isConfigExists($inputXML->ip,$inputXML->app_name))
		{
			var_dump("Row exists in DB but config missing. Adding config on instance");
			$conn->close();
			return true;
		}
		else
		{
			$conn->close();
			return false;
		}
	}
    }   
}


?>
