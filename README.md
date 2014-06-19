NagiosInventory
===============

The NagiosInventory class/demo lets you to do an inventory (or export) your Nagios configuration. There is no need to parse the many cfg-files to get the complete picture. The input to the class is the path to the Nagios objects.cache file which contains all configuration that is currently running in Nagios. After initializing the class all configuration is available as arrays in the NagiosObjects class. You get access to all of your hosts, hostgroups, contacts, contactgroups, timeperiods, commands and services.

If you ever wanted to export all of your Nagios configuration to Excel to get a list of all your monitored services, then the demo show how that can be done. First it loads the configuration, then it combines a "list" of all of your hosts and their services. The data that is most wanted is the printed to a csv-file that can easily be imported to Excel.

Contact: http://www.marcusnyberg.com

