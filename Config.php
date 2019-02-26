<?php


class Config
{

	//****************** Dev **********************************
        public static $servername = "XYZ_MYSQL_DB_SERVER.eu-west-1.rds.amazonaws.com";
        public static $username = "ADMIN";
        public static $password = "YourDBPassword@123";
        public static $dbname = "Nagios_DB";
        public static $environment = "dev";
        public static $min_before_down_hosts_deleted = 1 ;
	
	// This variable contains URL of your nagios dashboard. This will return details of all hosts which are currently in down state.
	// Replace dev-nagios with your actual nagios dashboard hostname.
        public static $down_host_json_url = "http://dev-nagios/nagios/cgi-bin/statusjson.cgi?query=hostlist&formatoptions=duration&details=true&hoststatus=down";

}


?>
