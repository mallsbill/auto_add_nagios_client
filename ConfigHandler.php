<?php
require_once("Common.php");

class ConfigHandler 
{

    function resetConfig($inputXML) 
    {

	$ip = $inputXML->ip;
	$instance_id = $inputXML->instance_id;
	$app_name = $inputXML->app_name;
	var_dump($app_name);	
	if(strlen($ip)==0 || strlen($instance_id)==0 || strlen($app_name)==0)
		return false;

	//find if configuration is available for app
	$cmd_find_app_config_in_hostgroup='grep -n -A 1 -E "hostgroup_name.*' . $app_name . '" ' . Common::$hostgroup_file_path . ' | awk -v FS=- \'NR==2{print $1}\'';
	#$cmd_find_app_config_in_hostgroup='grep -n -A 1 -E "hostgroup_name.*' . $app_name . '" ' . Common::$hostgroup_file_path . '';
	var_dump("command is $cmd_find_app_config_in_hostgroup");
	$config_line_number=exec($cmd_find_app_config_in_hostgroup,$output,$ret_value);
	echo "command output is ";
	var_dump($output);
	var_dump("return vaulue $ret_value and line number to update in hostgroup file $config_line_number");

	if($config_line_number)
	{

		var_dump("updating configuration");
		$cmd_update_ip_in_hostgroup_file = "sed -i '" .  $config_line_number . "s/$/," . $ip . "/' " . Common::$hostgroup_file_path . "";
		var_dump("command is $cmd_update_ip_in_hostgroup_file");
		exec($cmd_update_ip_in_hostgroup_file,$output,$ret_value);
		var_dump("sed return value is $ret_value");
	 	var_dump("Machine type");
		var_dump($inputXML->platform);


		//add host in hosts file
		if($inputXML->platform=="Linux")
		{
			var_dump("adding linux host in main.mk");
			$template_file_path="/var/www/rest.com/public_html/templates/lin_hosts.template";
		}
		else
		{
			var_dump("adding windows host in main.mk");
			$template_file_path="/var/www/rest.com/public_html/templates/win_hosts.template";
		}

		$template_text=file_get_contents($template_file_path);
		$hostnode_text=str_replace("@@ip", $ip,$template_text);
		$hostnode_text=str_replace("@@app_name", $app_name,$hostnode_text);
		var_dump("hostnode $hostnode_text");
		
		$allhostfilecontent = file_get_contents(Common::$allhosts_file_path);
		$allhostfilecontent = str_replace("##########AUTOMATIC_NAGIOS_ADDED_HOST###############", "##########AUTOMATIC_NAGIOS_ADDED_HOST###############\n".$hostnode_text, $allhostfilecontent);
		$ret_value=file_put_contents(Common::$allhosts_file_path,$allhostfilecontent, FILE_USE_INCLUDE_PATH);

		print_r("result of file write command ");
	        var_dump($ret_value);

		$response="configuration updated";	
		Common::restart_nagios();
	}
	else
	{
		
		$response="Application configuration not available on server for " . $app_name . "";
	}
	
	return $response;
    }   
}


?>

