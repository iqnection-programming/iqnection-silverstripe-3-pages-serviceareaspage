<?php

namespace IQnection\ServiceAreasPage;

class ServiceAreasChildPage extends ServiceAreasPage
{
	private static $table_name = 'ServiceAreasChildPage';
	
	private static $allowed_children = [
		ServiceAreasChildPage::class
	];
	
	private static $defaults = [
		'ShowInMenus' => true,
	];
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->removeByName("Sidebar");
		$fields->removeByName("SidebarContent");
		$fields->removeByName("SidebarBottom");
		$fields->removeByName("ThankYouText");
		$fields->removeByName("MapDetails");
		$fields->removeByName("FormControls");
		$fields->removeByName("FormInformation");
		return $fields;
	}	
	
	public function isServiceChild()
	{
		return true;	
	}
	
	public function ThankYouText()
	{
		return $this->getParentServiceAreasPage()->ThankYouText;
	}	
}
