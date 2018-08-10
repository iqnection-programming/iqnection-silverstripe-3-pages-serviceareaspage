<?php

namespace IQnection\ServiceAreasPage;

use SilverStripe\Assets\Image;
use SilverStripe\Forms;
use SilverStripe\Core\Injector\Injector;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use IQnection\FormPage\FormRecipient;

class ServiceAreasPage extends \Page
{
	private static $table_name = 'ServiceAreasPage';
	
	private static $db = [
		"SidebarBottom" => "HTMLText",
		"ThankYouText" => "HTMLText",
		"MapType" => "Varchar(255)",
		"FirstName_Control" => "Varchar(255)",
		"LastName_Control" => "Varchar(255)",
		"Address_Control" => "Varchar(255)",
		"Address2_Control" => "Varchar(255)",
		"City_Control" => "Varchar(255)",
		"State_Control" => "Varchar(255)",
		"ZipCode_Control" => "Varchar(255)",
		"Phone_Control" => "Varchar(255)",
		"Email_Control" => "Varchar(255)",
		"Comments_Control" => "Varchar(255)",
		"BaseContent" => "HTMLText",
		"PageStructure" => "Text",
		'BaseMetaTitle' => 'Varchar(255)',
		'BaseMetaKeywords' => 'Text',
		'BaseMetaDescription' => 'Text',
		'BasePageTitle' => 'Varchar(255)',
		"FromEmail" => 'Varchar(255)',
	];
	
	private static $defaults = [
		'ShowInMenus' => false,
		'Sequence' => '9999'
	];
	
	private static $has_one = [
		"MapIcon" => Image::class
	];
	
	private static $has_many = [
		"ServiceAreasPageLocations" => Model\ServiceAreasPageLocation::class,
		"FormRecipients" => FormRecipient::class,
		"ServiceAreasPageFormSubmissions" => Model\ServiceAreasPageFormSubmission::class
	];
	
	private static $owns = [
		'MapIcon'
	];
	
	private static $allowed_children = [
		ServiceAreasChildPage::class
	];
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$fields->addFieldToTab("Root.Sidebar", Forms\HTMLEditor\HTMLEditorField::create("SidebarContent", "Sidebar Content Top")
			->addExtraClass('stacked')); 
		$fields->addFieldToTab("Root.Sidebar", Forms\HTMLEditor\HTMLEditorField::create("SidebarBottom", "Sidebar Content Bottom")
			->addExtraClass('stacked')); 
		
		$fields->addFieldToTab('Root.MapDetails', Forms\GridField\GridField::create(
			'ServiceAreasPageLocations',
			'Locations',
			$this->ServiceAreasPageLocations(),
			Forms\GridField\GridFieldConfig_RecordEditor::create()->addComponent(
				new GridFieldSortableRows('SortOrder')
			)
		));
		
		$fields->addFieldToTab("Root.MapDetails", Injector::inst()->create(Forms\FileHandleField::class, 'MapIcon') );
		 
		$fields->addFieldToTab("Root.MapDetails", Forms\DropdownField::create("MapType", "Map Display Type", array("ROADMAP"=>"Roadmap","SATELLITE"=>"Satellite","HYBRID"=>"Hybrid","TERRAIN"=>"Terrain"),"Roadmap"));
		
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("FirstName_Control", "First Name", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("LastName_Control", "Last Name", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("Address_Control", "Address", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("Address2_Control", "Address 2", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("City_Control", "City", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("State_Control", "State", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("ZipCode_Control", "Zip Code", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("Phone_Control", "Phone Number", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("Email_Control", "Email Address", array("Enabled" => "Enabled", "Required" => "Required")));
		$fields->addFieldToTab("Root.FormControls.Fields", Forms\CheckboxSetField::create("Comments_Control", "Comments", array("Enabled" => "Enabled", "Required" => "Required")));
		
		$fields->addFieldToTab('Root.FormSubmissions', Forms\GridField\GridField::create(
			'ServiceAreasPageFormSubmissions',
			'Submissions',
			$this->ServiceAreasPageFormSubmissions(),
			Forms\GridField\GridFieldConfig_RecordEditor::create()->addComponent(
				$exportBtn = new Forms\GridField\GridFieldExportButton()
			)
		));
		$ExportFields = array(
			"Created" => "Date",
			"FirstName" => "First Name",
			"LastName" => "Last Name",
			"Address" => "Address", 
			"Address2" => "Address 2",
			"City" => "City",
			"State" => "State",
			"ZipCode" => "Zip Code",
			"Email" => "Email Address",
			"Phone" => "Phone Number",
			"PageURL" => "Page URL",
			"Comments" => "Comments"
		);
		$this->extend('updateExportFields',$ExportFields);
		$exportBtn->setExportColumns($ExportFields);
		
		$fields->addFieldToTab("Root.FormControls.Recipients", Forms\LiteralField::create("Desc1", "<h3>Forms will be submitted to all addresses below.</h3><br>"));
		$fields->addFieldToTab('Root.FormControls.Recipients', Forms\EmailField::create('FromEmail','Notification From Email') );
		$fields->addFieldToTab('Root.FormControls.Recipients', Forms\GridField\GridField::create(
			'FormRecipients',
			'Recipients',
			$this->FormRecipients(),
			Forms\GridField\GridFieldConfig_RecordEditor::create()->addComponent(
				new GridFieldSortableRows('SortOrder')
			)
		));

		$fields->addFieldToTab("Root.FormControls.ThankYouText", Forms\HTMLEditor\HTMLEditorField::create("ThankYouText", "Text on Submission")
			->addExtraClass('stacked'));
		
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\LiteralField::create("M0", "<h1>This tool lets you create service area pages with base content.  YOU MUST REFRESH the admin area after saving this page to see your new content.</h1>"));
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\LiteralField::create("M1", "<p>Enter some content below, and it will be the default content for all pages made with this tool.</p><p>You may optionally add {NAME} to the content, and it will be magically replaced with the page name</p>"));
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\HTMLEditor\HTMLEditorField::create("BaseContent", "Base Content")
			->addExtraClass('stacked'));
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\TextField::create("BasePageTitle", "Base Page Title")); 
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\TextField::create("BaseMetaTitle", "Base Meta Title")); 
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\TextAreaField::create("BaseMetaKeywords", "Base Meta Keywords")); 
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\TextAreaField::create("BaseMetaDescription", "Base Meta Description"));    
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\LiteralField::create("M2", "<br><br><h3>Below you can enter page names for automatic generation.</h3><p>One page name per line</p><p>First level pages can simply be typed on a line.</p><p>Children pages must start with a tilde ~ for each level of nesting.</p><h4>Example:</h4><p>Heading Page Name</p><p>~Child Page Name</p><p>~Child Page Name</p><p>~~Sub Child Page Name</p><p>Heading Page Name</p><p>~Child Page Name</p>"));
		$fields->addFieldToTab("Root.Developer.PageCreation", Forms\TextAreaField::create("PageStructure", "Page Structure")); 
		$this->extend('updateCMSFields',$fields);
		return $fields;
	}	
	
	public function isServiceAreas()
	{
		return true;	
	}
	
	public function PrintChildren($factor=false, $children=false, $level=1)
	{
		$children = $children ? $children : $this->Children();
		$output = "";
		if(count($children))
		{
			$output .= "<ul class='level_".$level."'>";
			$counter = 1;
			foreach($children as $child)
			{
				if($level > 1 || (($factor == 'odd' && $counter % 2 != 0) || ($factor == 'even' && $counter % 2 == 0)))
				{
					$output .= "<li><a href='".$child->Link()."'>".$child->MenuTitle."</a>";
					$output .= $this->PrintChildren(false,$child->Children(),$level+1);			
					$output .= "</li>";	
				}
				$counter++;
			}
			$output .= "</ul>";
		}
		return $output;
	}
	
	public function GetIconThumb()
	{
		if ( ($this->MapIcon()->Exists()) && ($img = $this->MapIcon()) )
		{
			return $img->SetWidth(25);
		}
	}
	
	public function getRequiredFields()
	{
		$availFields = array(
			'FirstName',
			'LastName',
			'Address',
			'Address2',
			'City',
			'State',
			'ZipCode',
			'Phone',
			'Email',
			'Comments'
		);			
		$reqFields =array();
		foreach($availFields as $availField)
		{
			$fieldControls = $this->{$availField.'_Control'};
			if ( ($fieldControls) && (preg_match('/Required/',$fieldControls)) )
			{
				$reqFields[] = $availField;
			}
		}
		$this->extend('updateRequiredFields',$reqFields);
		return $reqFields;
	}
	
	public function isFieldEnabled($FieldName)
	{
		return (preg_match('/Enabled/',$this->{$FieldName.'_Control'}));
	}
	
	public function NeedsForm()
	{
		$pointer = $this->getParentServiceAreasPage();
		return $pointer->FirstName_Control || $pointer->LastName_Control || $pointer->Address_Control || $pointer->City_Control || $pointer->State_Control || $pointer->ZipCode_Control || $pointer->Phone_Control || $pointer->Email_Control || $pointer->Comments_Control;	
	}
	
	public function getParentServiceAreasPage()
	{
		if (get_class($this) == ServiceAreasPage::class)
		{
			return $this;
		}
		return $this->Parent()->getParentServiceAreasPage();
	}
	
	public function onBeforeWrite()
	{
		parent::onBeforeWrite();
		if ($this->ID)
		{
			$this->createPageStructure();
		}
	}
	
	public function ServiceAreasPageLevel()
	{
		return $this->getBreadcrumbItems(20,'ServiceAreasPage',1)->Count()+1;
	}
	
	public function createPageStructure()
	{
		$BaseParentID = $this->record['ID'];
		$BaseContent = $this->record['BaseContent'] ? $this->record['BaseContent'] : $this->original['BaseContent'];
		$BaseMetaTitle = $this->record['BaseMetaTitle'] ? $this->record['BaseMetaTitle'] : $this->original['BaseMetaTitle'];
		$BasePageTitle = $this->record['BasePageTitle'] ? $this->record['BasePageTitle'] : $this->original['BasePageTitle'];
		$BaseMetaKeywords = $this->record['BaseMetaKeywords'] ? $this->record['BaseMetaKeywords'] : $this->original['BaseMetaKeywords'];
		$BaseMetaDescription = $this->record['BaseMetaDescription'] ? $this->record['BaseMetaDescription'] : $this->original['BaseMetaDescription'];
		$PageStructure = $this->record['PageStructure'];
		$this->setField("BaseContent", "");
		$this->setField("PageStructure", "");
		$this->setField("BasePageTitle", "");
		$this->setField("BaseMetaTitle", "");
		$this->setField("BaseMetaKeywords", "");
		$this->setField("BaseMetaDescription", "");
		
		if ($PageStructure)
		{
			$default_pages = explode("\n", $PageStructure);
			$curr_level_page = array();
			$level_sequence = array(
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
			);
			
			foreach ($default_pages as $page)
			{					
				$page_name = trim($page);
					
				$level = 1;
				while (preg_match("/^~/", $page_name))
				{
					$page_name = substr($page_name, 1);
					$level++;
				}
				$level_sequence[$level] += 10;

				$new_page = new ServiceAreasChildPage();
				
				$BasePageTitle = $BasePageTitle ? $BasePageTitle : $page_name;
				$new_page->Title = str_replace("{NAME}",$page_name,$BasePageTitle);
				$new_page->MenuTitle = $page_name;
				$new_page->Content = str_replace("{NAME}",$page_name,$BaseContent);
				$new_page->MetaTitle = str_replace("{NAME}",$page_name,$BaseMetaTitle);
				$new_page->MetaKeywords = str_replace("{NAME}",$page_name,$BaseMetaKeywords);
				$new_page->MetaDescription = str_replace("{NAME}",$page_name,$BaseMetaDescription);
				$new_page->Status = 'Published';
				$new_page->Sort = $level_sequence[$level];
				if ($level > 1 && $curr_level_page[($level-1)])
				{
					$parent = $curr_level_page[($level-1)];
					$new_page->ParentID = $parent->ID;
				} else {
					$new_page->ParentID = $BaseParentID;
				}
				$new_page->write();
				$new_page->writeToStage('Stage');
				$new_page->doPublish();
				$new_page->flushCache();
				
				$curr_level_page[$level] = $new_page;
			}
		}
	}
}
