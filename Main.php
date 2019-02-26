<?php
require_once("DBHandler.php");
require_once("Common.php");
require_once("ConfigHandler.php");

class Main 
{

    function process($inputXML)
    {
	$ip = $inputXML->ip;
	$instance_id = $inputXML->instance_id;

	$app_name=$inputXML->app_name;
	var_dump("app name inside inputXML is $app_name");
	if(strlen(trim($app_name))==0)
	{
		var_dump("querying ec2 describe tag from server and getting app name value");
		//get application name from instance id in to variable	
		$cmd='aws ec2 describe-tags --filters "Name=resource-id,Values=' . $instance_id . '" --region ' . Common::$region . ' | grep -B 1 \'"Key": "Application.*"\' | awk -v FS=: \'NR==1{print $2}\' | awk \'gsub(/"|,| /,"")\' ';
		$app_name = exec($cmd,$out);
		#$app_name = "NagiosandSPlunk";
		var_dump($cmd);

		$inputXML->app_name=$app_name;
	}
	var_dump("app name used is $app_name");
	$dbObj=new DBHandler();
//	$isRecordSaved=true;
	$isRecordSaved=$dbObj->insertClientDetails($inputXML);
		
	if($isRecordSaved)
	{
		$response = "ok." . $app_name . " is registered with nagios server";
		$confObj=new ConfigHandler();
		$response=$confObj->resetConfig($inputXML);
	}
	else
	{
		 $response = "Instance with app name " . $app_name . " is already registered with nagios server. Request Ignored.";
	}
	
	return $response;
    }   
}


?>
