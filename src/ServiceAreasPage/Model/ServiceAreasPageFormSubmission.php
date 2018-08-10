<?php

namespace IQnection\ServiceAreasPage\Model;

use SilverStripe\ORM;
use SilverStripe\Forms;

// model to save a form submission into the database
class ServiceAreasPageFormSubmission extends ORM\DataObject
{
	private static $table_name = 'ServiceAreasPageFormSubmission';
	
	private static $db = [
		"FirstName" => "Varchar(255)", 
		"LastName" => "Varchar(255)", 
		"Address" => "Varchar(255)", 
		"Address2" => "Varchar(255)", 
		"City" => "Varchar(255)", 
		"State" => "Varchar(255)", 
		"ZipCode" => "Varchar(255)", 
		"Email" => "Varchar(255)", 
		"Phone" => "Varchar(255)", 
		"Comments" => "Text",
		"PageURL" => "Varchar(255)",
	];
	
	private static $has_one = [
		"ServiceAreasPage" => \IQnection\ServiceAreasPage\ServiceAreasPage::class
	];
	
	private static $summary_fields = [
		"Created.NiceUS" => "Date",
		"FirstName" => "First Name",
		"LastName" => "Last Name",
		"Email" => "Email Address",
		"Phone" => "Phone Number",
		"PageURL" => "Page URL"
	];
	
	private static $default_sort = "Created DESC";
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->insertBefore('FirstName', Forms\ReadonlyField::create("PageURL", "Page") );
		$fields->insertBefore('FirstName', Forms\DatetimeField_Readonly::create("Created", "Date") );
		$this->extend('updateCMSFields',$fields);
		return $fields;
	}
	
	public function canCreate($member = null, $context = array()) { return false; }
	public function canDelete($member = null, $context = array()) { return true; }
	public function canEdit($member = null, $context = array())   { return false; }
	public function canView($member = null, $context = array())   { return true; }
}



