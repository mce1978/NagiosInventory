<?php
    
require('class.NagiosInventory.php' );

$ni = new NagiosInventory(getcwd().'\objects.cache');

//All hosts
//print_r($ni->GetObjects()->host); die();

//All commands 
//print_r($ni->GetObjects()->command); die();


// Print as csv-file
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=nagios-inventory.csv");
header("Pragma: no-cache");
header("Expires: 0");

$hosts = $ni->Compose();

foreach ($hosts as $hostname => $stdHost) {
	$h = $stdHost->host;
	
	if(!isset($stdHost->service)) continue;

	// Fix some variables before print
	$hostgroups = isset($stdHost->hostgroups) ? implode($stdHost->hostgroups,",") : "-";
	$alias = isset($h->alias) && $h->alias != "" ? $h->alias : "-";	
	
	foreach ($stdHost->service as $s) {
		
		// Fix some variables before print
		$parameters = isset($s->check_command_parameters) && $s->check_command_parameters != "" ? $s->check_command_parameters : "-";
		$notes_url = isset($s->notes_url) && $s->notes_url != "" ? $s->notes_url : "-";
		
		// Print host stuff
		echo $hostgroups.";";
		echo $h->host_name.";";
		echo $alias.";";
		echo $h->address.";";
		
		// Print service stuff
		echo $s->service_description.";";
		echo $s->check_command_base.";";
		echo $parameters.";";
		echo $s->check_command_line.";";
		echo $s->contact_groups.";";
		echo $s->notification_period.";";
		echo $notes_url;
				
		echo "\r\n";
} } 
