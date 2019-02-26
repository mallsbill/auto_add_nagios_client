<?php
require_once("Config.php");

class Common
{ 
	public static $hostgroup_file_path = "/usr/local/nagios/etc/hostgroups.cfg";
	public static $allhosts_file_path = "/usr/local/nagios/etc/hosts-all.cfg";


	#public static $restart_service_command = "echo '' | /etc/init.d/nagios restart 2>&1";
	public static $restart_service_command = "sudo /etc/init.d/nagios restart";
	public static $region = "eu-west-1";


	public static function take_backup_to_git()
	{
		$output="No backcup taken for non-prod environments";
		if(Config::$environment=="prod_master")
		{
			$output=shell_exec("(cd /usr/local/nagios/etc && ./prod-backup.sh 2>&1)");
			var_dump($output);
		}
				
		return $output;
	}

	public static function get_auth_context()
	{
		$auth = base64_encode("nagiosadmin:nagiosadmin");
		$context = stream_context_create(array('http' => array('header' => "Authorization: Basic $auth")));
		return $context;
	}

	public static function get_down_hosts_list()
	{
		$context = Common::get_auth_context();
		$down_hosts_str=file_get_contents(Config::$down_host_json_url,false,$context);
		$down_hosts_json=json_decode($down_hosts_str,true);
		return $down_hosts_json;
	}
	
	public static function restart_nagios()
	{
		var_dump("restarting nagios service");
		exec(Common::$restart_service_command,$result,$output);
		var_dump($result);
		var_dump($output);
	}
}


?>
