<?php

class Release extends DataObject{
	static $db = array(
		'SHA' => 'Varchar(40)'
	);

	static $summary_fields = array(
		'SHA' => 'Release ID',
		'LastEdited' => 'Date'
	);

	static $default_sort = '"Created" DESC';
}