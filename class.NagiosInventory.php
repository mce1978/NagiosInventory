<?php

/* this is what you get */
class NagiosObjects {
	var $host = array();
	var $hostgroup = array();
	var $contact = array();
	var $contactgroup = array();
	var $timeperiod = array();
	var $command = array();
	var $service = array();
}

/**
 * User NagiosInventory
 * @property NagiosObjects $objects
 */

class NagiosInventory {
	
	private $objectsCacheFile;
	private $objects;
	
	function __construct($objectsCacheFile){
		$this->objectsCacheFile = $objectsCacheFile;

		if ( !file_exists($this->objectsCacheFile) ) {
			throw new Exception('Object.cache not found!' );
		}
		
		$this->LoadObjects();
	}
	
	/** @return NagiosObjects */
	public function GetObjects() { return $this->objects; }
	
	/*
	 * Based on "Nagios Stats"
	 */	
	public function LoadObjects() {

		$f = fopen( $this->objectsCacheFile, 'r' );
	
		if ( !$f ) { throw new Exception( 'Unable to open file: '.$this->objectsCacheFile ); return false; }
	
		$out = array();
	
		// Read Line-By-Line
		while ( ( $line = fgets( $f, 4096 ) ) !== false )
		{
			// Remove Excess Whitespace
			$line = trim( $line );
				
			// Skip Empty Lines
			if ( empty( $line ) ) continue;
				
			// Ignore Comments
			if ( substr( $line, 0, 1 ) == '#' ) continue;
				
			// New Object
			if ( substr( $line, -1 ) == '{' )
			{
				// Remove Define and trailing {
				if ( substr( $line, 0, 7 ) == 'define ' ) { $line = substr( $line, 7 ); }
				if ( substr( $line, -1 ) == '{' ) { $line = substr( $line, 0, -1 ); }
	
				$obj = new stdClass;
	
				// Set Type
				$type = trim( $line );
	
				continue;	// Next Line
			}
				
			// End Object
			if ( $line == '}' )
			{
				// Output Format
				if ( in_array( $type, array( 'host', 'hostgroup', 'contact', 'contactgroup', 'timeperiod', 'command' ) ) )
				{
					$this->objects->{$type}[$name] = $obj;
				} elseif( $type == 'service' )
				{
					// Add to Output
					$this->objects->{$type}[$command] = $obj;
				} else
				{
					// Add to Output
					$this->objects->{$type}[] = $obj;
				}
				
				continue;	// Next Line
			}
			
			// Split by Whitespace
			@list( $key, $value ) = preg_split( '/[\s,]+/', $line, 2 );
				
			// Set Name and Command
			if ( substr( $key, -5 ) == '_name' ) { $name = $value; }
			if ( $key == 'check_command' ) { $command = $value; }
				
			// Members
			if ( $key == 'members' ) { $value = preg_split( '/[\s,]+/', $value, 2 ); }
				
			// Append to Object
			$obj->{ $key } = $value;
		}
	}

	/* Composes data from several properties */
	public function Compose() {
	
		$hosts = array();
	
		foreach ($this->objects->host as $host) {
			$hosts[$host->host_name]->host = $host;
		}
	
		foreach ($this->objects->service as $service) {
				
			// Find command and parameters
			$params = explode("!",$service->check_command);
			$command = reset($params);
			array_shift($params);
			$service->check_command_base = $command;
			$service->check_command_parameters = !empty($params) ? implode("!",$params) : "";
	
			// Get command line
			$service->check_command_line = $this->objects->command[$command]->command_line;
				
			$hosts[$service->host_name]->service[] = $service;
		}
	
		foreach ($this->objects->hostgroup as $hg) {
			foreach ($hg->members as $hostnames) {
				$hostnameArr = explode(",",$hostnames);
				foreach ($hostnameArr as $hostname)
					$hosts[$hostname]->hostgroups[] = $hg->hostgroup_name;
			}
		}
	
		return $hosts;
	}	
}

?>