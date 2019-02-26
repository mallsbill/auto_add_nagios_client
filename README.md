# auto_add_nagios_client
This is draft. Although code is completed, Visit again once documentation is updated

AWS Auto scaling group dynamically creates and deletes instances in real time. Nagios Core has to dynamically and instantly know which instance is created so that it can monitor new instances in real time. Nagios client instances will use HTTP REST APIs to register itself to nagios server whenver new instance is created under Auto scaling group.

Below REST API is created and exposed which will automatically register new instance to nagios server and restart nagios service. This will instantly start monitoring for newly launched instance on nagios server

1. https://<nagios_hostname>/register/me - TO register new instance by passing XML with instance details
2. https://<nagios_hostname>/register/showall - Display all registered instance on nagios server
3. https://<nagios_hostname>/cleanup - Clean nagios host file for old and deleted instances. [Configurable]

