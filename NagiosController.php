<?php
require_once("NagiosRestHandler.php");
		
$action = "";
if(isset($_GET["action"]))
	$action = $_GET["action"];
/*
controls the RESTful services
URL mapping
*/
switch($action){

	case "register":
		$nagiosRestHandler = new NagiosRestHandler();
		$nagiosRestHandler->registerInstance();
		break;

	case "show_all":
		var_dump("Controller : showing all  hosts");
		$nagiosRestHandler = new NagiosRestHandler();
		$nagiosRestHandler->show_all();
		break;

	case "delete_ip":
                var_dump("Controller : deleting ip");
                $nagiosRestHandler = new NagiosRestHandler();
                $nagiosRestHandler->delete_ip("'" . $_GET["ip"] . "'");
                break;

	case "delete_down_hosts":
		var_dump("Controller : deleting down hosts");
		$nagiosRestHandler = new NagiosRestHandler();
		$nagiosRestHandler->recycle();
		break;

	case "" :
		//404 - not found;
		break;
}
?>
