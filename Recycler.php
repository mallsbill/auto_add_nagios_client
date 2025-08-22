<?php
require_once("Common.php");
require_once("DBHandler.php");

class Recycler 
{

    function initiate_recycle() 
    {	
	var_dump("\n\n******************************************************\nInitiating Recycle : " . date("Y-m-d h:i:s a"));
	
	var_dump("taking backup of configuration files");
        $backup_result=Common::take_backup_to_git();
        var_dump($backup_result);
	
	var_dump("Recycling");
	$arr_ip_to_delete=array();
	//get json with list of down IPs
	$down_ip_json_arr= Common::get_down_hosts_list(); 
	//var_dump($down_ip_json_arr);
	
	//iterate and find ip down since more than 10 minuites
	foreach($down_ip_json_arr['data']['hostlist'] as $ipObj)
	{
		var_dump($ipObj['name']);
		$minuite_since_stopped=round($ipObj['last_hard_state_change'] / 60000);
		var_dump($minuite_since_stopped);
		if($minuite_since_stopped > Config::$min_before_down_hosts_deleted)
		{
			var_dump("yes");
			//find if it is only one in host group
			$hasSiblings=exec("grep " . $ipObj['name'] . " " . Common::$hostgroup_file_path . " | grep ,");
			if($hasSiblings)
			{
				array_push($arr_ip_to_delete,"'" . $ipObj['name'] . "'");
				var_dump("removing ip " . $ipObj['name'] . "");

				// Mettre l'hôte en downtime avant suppression (15 min par défaut)
				Common::set_host_downtime($ipObj['name'], 15);

				$cmd_rm_from_hostgroup_with_comma="sed -i -e s/,*" . $ipObj['name'] . "[^0-9]/\",\"/ig " . Common::$hostgroup_file_path . "";
				$cmd_rm_from_hostgroup_without_comma="sed -i -e s/" . $ipObj['name'] . "[^0-9]/\"\"/ig " . Common::$hostgroup_file_path . "";
				$cmd_rm_starting_comma="sed -i -e /.*members/s/[^0-9],//ig " . Common::$hostgroup_file_path . "";
				$cmd_rm_from_hostgroup_if_ip_is_last="sed -i -e s/,*" . $ipObj['name'] . "$/\"\"/ig " . Common::$hostgroup_file_path . "";

				var_dump($cmd_rm_from_hostgroup_with_comma);
				var_dump($cmd_rm_from_hostgroup_without_comma);
				var_dump($cmd_rm_starting_comma);
				var_dump($cmd_rm_from_hostgroup_if_ip_is_last);

				//remove from hostgroups with comma
				exec($cmd_rm_from_hostgroup_with_comma);
				//remove from hostgroup without comma
				exec($cmd_rm_from_hostgroup_without_comma);
				//remove starting comma if any
				exec($cmd_rm_starting_comma);
				//remove from hostgroup if IP is at the end of line
				exec($cmd_rm_from_hostgroup_if_ip_is_last);

				var_dump("removing host definition from host file");
				//remove host definition
				$cmd_get_host_defination_block_lines='grep -A 2 -B 4 -n ' . $ipObj['name'] . '$ ' . Common::$allhosts_file_path . ' | awk -v ORS="" -F "-|:" \'{print $1"d;";}\'';
				var_dump("command to find line number to delete in hosts file");
				var_dump($cmd_get_host_defination_block_lines);
				$sed_format_line_num_to_delete=exec($cmd_get_host_defination_block_lines);
				var_dump("line numbers to delete are " . $sed_format_line_num_to_delete);
				//delete host defintion from host file
				exec("sed -i '" . $sed_format_line_num_to_delete . "' " . Common::$allhosts_file_path . "");

				//restart nagios service
				Common::restart_nagios();			
			}
			else
			{
				var_dump("stand alone down host " . $ipObj['name'] . "");
			}
		}
		else
		{
			var_dump("no");
		}
	}
	$objDB=new DBHandler();
	$str_ip_to_delete=implode(",",$arr_ip_to_delete);
	$objDB->deleteIps($str_ip_to_delete);
	var_dump($str_ip_to_delete);
	return $str_ip_to_delete;
    }   
}
//$a=new Recycler();
//$a->initiate_recycle();

?>
