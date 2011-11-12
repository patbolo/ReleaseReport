<?php

class Ticket extends DataObject{
	static $db = array(
		'Key' => 'Varchar',
		'Summary' => 'Varchar',
		'Priority' => 'Varchar',
		'Status' => 'Varchar',
		'Assignee' => 'Varchar',
		'LastComment' => 'Varchar'
	);

	static $summary_fields = array(
		'Key' => 'Ticket',
		'Summary' => 'Summary',
		'Priority' => 'Priority',
		'Status' => 'Status',
		'Assignee' => 'Assignee',
		'LastComment' => 'LastComment'
	);

	function requireTable(){
		return false;
	}
}