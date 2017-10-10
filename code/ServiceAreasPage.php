<?
	
	class ServiceAreasLocation extends DataObject
	{
		private static $db = array( 
			"SortOrder" => "Int",
			"Title" => "Varchar(255)",
			"Address" => "Varchar(255)",
			"MapLatitude" => "Varchar(255)",
			"MapLongitude" => "Varchar(255)"
		);
		
		private static $has_one = array(
			"ServiceAreasPage" => "ServiceAreasPage"
		); 		
		
		private static $summary_fields = array(
			'Title' => 'Title',
			'Address' => 'Address'
		);
		
        public function getCMSFields()
        {
			$fields = parent::getCMSFields(); 
			
			$fields->addFieldToTab('Root.Main', new HiddenField('SortOrder',null,$fields->dataFieldByName('SortOrder')->Value()) );
			$fields->removeByName('MapLatitude');
			$fields->removeByName('MapLongitude');
			$this->extend('updateCMSFields',$fields);
			return $fields;
        }
		
		public function getLocation($address=false)
		{
			$google = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=";
			$url = $google.urlencode($address);
			
			$resp_json = $this->curl_file_get_contents($url);
			$resp = json_decode($resp_json, true);
	
			if($resp['status']='OK'){
				return $resp['results'][0]['geometry']['location'];
			}else{
				return false;
			}
		}
		
		private function curl_file_get_contents($URL)
		{
			$c = curl_init();
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_URL, $URL);
			$contents = curl_exec($c);
			curl_close($c);
	
			if ($contents) return $contents;
				else return FALSE;
		}
		
		function onAfterWrite()
		{
			parent::onAfterWrite();
			
			$location = $this->getLocation($this->Address);
			if ($location)
			{
				$this->MapLatitude = $location['lat'];
				$this->MapLongitude = $location['lng'];
				$this->write();
			}
			
			$this->extend('onAfterWrite',$this);
		}
		
		public function canCreate($member = null) { return true; }
		public function canDelete($member = null) { return true; }
		public function canEdit($member = null)   { return true; }
		public function canView($member = null)   { return true; }
	}

	// model to save a form submission into the database
	class ServiceAreasFormSubmission extends DataObject
	{
		private static $db = array( 
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
			"Date" => "SS_Datetime"
		);
		
		private static $has_one = array(
			"ServiceAreasPage" => "ServiceAreasPage"
		); 		
		
		private static $summary_fields = array(
			"Date.NiceUS" => "Date",
			"FirstName" => "First Name",
			"LastName" => "Last Name",
			"Email" => "Email Address",
			"Phone" => "Phone Number",
			"PageURL" => "Page URL"
		);
		
		private static $default_sort = "Date ASC";
		
        public function getCMSFields()
        {
			$fields = parent::getCMSFields();

			$fields->addFieldToTab('Root.Main', new DatetimeField_Readonly("Date", "Date:") );
			$fields->addFieldToTab('Root.Main', new TextField("FirstName", "First Name:") );
			$fields->addFieldToTab('Root.Main', new TextField("LastName", "Last Name:") );
			$fields->addFieldToTab('Root.Main', new TextField("Address", "Address:") );
			$fields->addFieldToTab('Root.Main', new TextField("Address2", "Address:") );
			$fields->addFieldToTab('Root.Main', new TextField("City", "City:") );
			$fields->addFieldToTab('Root.Main', new TextField("State", "State:") );
			$fields->addFieldToTab('Root.Main', new TextField("ZipCode", "Zip Code:") );
			$fields->addFieldToTab('Root.Main', new TextField("Email", "Email Address:") );
			$fields->addFieldToTab('Root.Main', new TextField("Phone", "Phone Number:") );
			$fields->addFieldToTab('Root.Main', new TextareaField("Comments", "Comments:") );
			
			$this->extend('updateCMSFields',$fields);
			return $fields;
        }
		
		public function canCreate($member = null) { return false; }
		public function canDelete($member = null) { return true; }
		public function canEdit($member = null)   { return false; }
		public function canView($member = null)   { return true; }
	}
	
	class ServiceAreasPage extends Page
	{
		private static $db = array(
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
			'BasePageTitle' => 'Varchar(255)'
		);
		
		private static $defaults = array(
			'ShowInMenus' => false,
			'Sequence' => '9999'
		);
		
		private static $has_one = array(
			"MapIcon" => "Image"
		);
		
		private static $has_many = array(
			"ServiceAreasLocations" => "ServiceAreasLocation",
			"FormRecipients" => "FormRecipient",
			"ServiceAreasFormSubmissions" => "ServiceAreasFormSubmission"
		);
		
		private static $allowed_children = array(
			"ServiceAreasChildPage"
		);
		
		public function isServiceAreas(){
			return true;	
		}
		
		public function PrintChildren($factor=false, $children=false, $level=1){
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
			if( $this->MapIconID ) {
				if( $img = $this->MapIcon() ) {
					if( $cropped = $img->SetWidth(25) )
						return $cropped->Filename;
				}
			}
			return "";
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
		
		function isFieldEnabled($FieldName)
		{;
			return (preg_match('/Enabled/',$this->{$FieldName.'_Control'}));
		}
		
		public function getCMSFields()
		{
			$fields = parent::getCMSFields();

			$fields->addFieldToTab("Root.Sidebar", new HTMLEditorField("SidebarContent", "Sidebar Content Top")); 
			$fields->addFieldToTab("Root.Sidebar", new HTMLEditorField("SidebarBottom", "Sidebar Content Bottom")); 
			
			$fields->addFieldToTab('Root.MapDetails', new GridField(
				'ServiceAreasLocations',
				'Locations',
				$this->ServiceAreasLocations(),
				GridFieldConfig_RecordEditor::create()->addComponent(
				new GridFieldSortableRows('SortOrder'),
					'GridFieldButtonRow'
				)
			));
			
			if(permission::check('ADMIN'))$fields->addFieldToTab("Root.MapDetails", new UploadField("MapIcon", "Map Marker Image"));
			 
			$fields->addFieldToTab("Root.MapDetails", new DropdownField("MapType", "Map Display Type", array("ROADMAP"=>"Roadmap","SATELLITE"=>"Satellite","HYBRID"=>"Hybrid","TERRAIN"=>"Terrain"),"Roadmap"));
			
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("FirstName_Control", "First Name", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("LastName_Control", "Last Name", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("Address_Control", "Address", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("Address2_Control", "Address 2", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("City_Control", "City", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("State_Control", "State", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("ZipCode_Control", "Zip Code", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("Phone_Control", "Phone Number", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("Email_Control", "Email Address", array("Enabled" => "Enabled", "Required" => "Required")));
			$fields->addFieldToTab("Root.FormControls.Fields", new CheckboxSetField("Comments_Control", "Comments", array("Enabled" => "Enabled", "Required" => "Required")));
			
			$fields->addFieldToTab('Root.FormSubmissions', new GridField(
				'ServiceAreasFormSubmissions',
				'Submissions',
				$this->ServiceAreasFormSubmissions(),
				GridFieldConfig_RecordEditor::create()->addComponent(
					$exportBtn = new GridFieldExportButton(),
					'GridFieldButtonRow'
				)
			));
			$ExportFields = array(
				"Date" => "Date",
				"FirstName" => "First Name",
				"LastName" => "Last Name",
				"Address" => "Address", 
 					
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
			
			$fields->addFieldToTab("Root.FormControls.Recipients", new LiteralField("Desc1", "<h3>Forms will be submitted to all addresses below.</h3><br>"));
			$fields->addFieldToTab('Root.FormControls.Recipients', new GridField(
				'FormRecipients',
				'Recipients',
				$this->FormRecipients(),
				GridFieldConfig_RecordEditor::create()->addComponent(
				new GridFieldSortableRows('SortOrder')	,
					'GridFieldButtonRow'
				)
			));

			$fields->addFieldToTab("Root.FormControls.ThankYouText", new HTMLEditorField("ThankYouText", "Text on Submission"));
			
			if( permission::check('ADMIN') )
			{
				$fields->addFieldToTab("Root.PageCreation", new LiteralField("M0", "<h1>This tool lets you create service area pages with base content.  YOU MUST REFRESH the admin area after saving this page to see your new content.</h1>"));
				$fields->addFieldToTab("Root.PageCreation", new LiteralField("M1", "<p>Enter some content below, and it will be the default content for all pages made with this tool.</p><p>You may optionally add {NAME} to the content, and it will be magically replaced with the page name</p>"));
				$fields->addFieldToTab("Root.PageCreation", new HTMLEditorField("BaseContent", "Base Content"));
				$fields->addFieldToTab("Root.PageCreation", new TextField("BasePageTitle", "Base Page Title")); 
				$fields->addFieldToTab("Root.PageCreation", new TextField("BaseMetaTitle", "Base Meta Title")); 
				$fields->addFieldToTab("Root.PageCreation", new TextAreaField("BaseMetaKeywords", "Base Meta Keywords")); 
				$fields->addFieldToTab("Root.PageCreation", new TextAreaField("BaseMetaDescription", "Base Meta Description"));    
				$fields->addFieldToTab("Root.PageCreation", new LiteralField("M2", "<br><br><h3>Below you can enter page names for automatic generation.</h3><p>One page name per line</p><p>First level pages can simply be typed on a line.</p><p>Children pages must start with a tilde ~ for each level of nesting.</p><h4>Example:</h4><p>Heading Page Name</p><p>~Child Page Name</p><p>~Child Page Name</p><p>~~Sub Child Page Name</p><p>Heading Page Name</p><p>~Child Page Name</p>"));
				$fields->addFieldToTab("Root.PageCreation", new TextAreaField("PageStructure", "Page Structure")); 
			}  
			 
			return $fields;
		}	
		
		public function NeedsForm()
		{
			$pointer = $this->getPointer();
			return $pointer->FirstName_Control || $pointer->LastName_Control || $pointer->Address_Control || $pointer->City_Control || $pointer->State_Control || $pointer->ZipCode_Control || $pointer->Phone_Control || $pointer->Email_Control || $pointer->Comments_Control;	
		}
		
		public function getPointer()
		{
			if ($this->ClassName == "ServiceAreasPage")
			{
				return $this;
			}
			return $this->Parent()->getPointer();
		}
		
		public function onBeforeWrite()
		{
			parent::onBeforeWrite();
			if ($this->ID)
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
	}
	
	class ServiceAreasPage_Controller extends Page_Controller
	{
		private static $allowed_actions = array(
			"ServiceAreasForm",
			"thanks"
		);
		
		public function init()
		{
			parent::init();
			Requirements::javascript("https://maps.googleapis.com/maps/api/js?key=AIzaSyAXy4BLGXyLMakRQbrMVrFxS2KiXSj51cM&sensor=false");
		}
		
		function PageCSS()
		{
			$dir = ViewableData::ThemeDir();			
			$CSSFiles = parent::PageCSS();
			$CSSFiles[] = "iq-basepages/css/pages/Page.css";
			$CSSFiles[] = $dir."/css/pages/Page.css";
			$CSSFiles[] = $dir."/css/form.css";
			$this->extend('updatePageCSS',$CSSFiles);
			return $CSSFiles;
		}
		
		function PageJS()
		{
			$jsFiles = parent::PageJS();
			$jsFiles[] = ViewableData::ThemeDir()."/javascript/jquery.validate.nospam.js";
			$this->extend('updatePageJS',$jsFiles);
			return $jsFiles;
		}
		
		function CustomJS()
		{
			$js = parent::CustomJS();
			if ($this->getPointer()->ServiceAreasLocations()->Count())
			{
				$Count = 1;
				foreach($this->getPointer()->ServiceAreasLocations() as $Location) 
				{
					$js .= '
address_objects['.$Count.'-1] = {
	"Title":"'.$Location->Title.'",
	"Address":"'.$Location->Address.'",
	"LatLng":['.$Location->MapLatitude.','.$Location->MapLongitude.']
};';
					$Count++;
				}
			}
			return $js;
		}
		
		public function ServiceAreasForm()
		{
			// create the form fields
			$fields = new FieldList();
			if ($this->getPointer()->isFieldEnabled('FirstName')) $fields->push( new TextField("FirstName", "First Name") );
			if ($this->getPointer()->isFieldEnabled('LastName')) $fields->push( new TextField("LastName", "Last Name") );
			if ($this->getPointer()->isFieldEnabled('Address')) $fields->push( new TextField("Address", "Address") );
			if ($this->getPointer()->isFieldEnabled('Address2')) $fields->push( new TextField("Address2", "Address (line 2)") );
			if ($this->getPointer()->isFieldEnabled('City')) $fields->push( new TextField("City", "City") );
			if ($this->getPointer()->isFieldEnabled('State')) $fields->push( new DropdownField("State", "State", array(""=>"State") + FormUtilities::GetStates(), "PA") );
			if ($this->getPointer()->isFieldEnabled('ZipCode')) $fields->push( new TextField("ZipCode", "Zip Code") );
			if ($this->getPointer()->isFieldEnabled('Email')) $fields->push( new EmailField("Email", "Email Address") );
			if ($this->getPointer()->isFieldEnabled('Phone')) $fields->push( new TextField("Phone", "Phone Number") );
			if ($this->getPointer()->isFieldEnabled('Comments')) $fields->push( new TextareaField("Comments", "Comments") );
				 
			// create a form action
			$actions = new FieldList(
				new FormAction("SubmitServiceAreasForm", "Submit")
			);
			
			$validator = FormUtilities::RequiredFields($fields,$this->getPointer()->getRequiredFields());
			
			return new Form($this, "ServiceAreasForm", $fields, $actions,$validator);
		}

		public function SubmitServiceAreasForm( $data, $form )
		{
			// magical spam protection
			if( !FormUtilities::validateAjaxCode() )
			{
				Session::set("FormInfo.ServiceAreasForm_ServiceAreasForm.data", $data);
				$form->addErrorMessage("Message", "Error, please enable javascript to use this form", "bad");
				return $this->redirectBack();	
			}	
			
			$SAPage = $this->getPointer();
			
			// save submission for posterity
			$submission = new ServiceAreasFormSubmission();
			$submission->ServiceAreasPageID = $SAPage->ID;
			$submission->FirstName = $data['FirstName'];
			$submission->LastName = $data['LastName'];
			$submission->Address = $data['Address'];
			$submission->Address2 = $data['Address2'];
			$submission->City = $data['City'];
			$submission->State = $data['State'];
			$submission->ZipCode = $data['ZipCode'];
			$submission->Email = $data['Email'];
			$submission->Phone = $data['Phone'];
			$submission->Comments = $data['Comments'];
			$submission->PageURL = $_SERVER['HTTP_REFERER'];
			$submission->Date = SS_Datetime::now();
			$submission->write();
						
			// send email to this address if specified
			$emails = $SAPage->FormRecipients();
			
			if( $emails )
			{
				foreach($emails as $email)
				{
					$EmailFormTo = $email->Email;
					FormUtilities::SendSSEmail($this, $EmailFormTo, $data,$submission);
				}
			}
			
			// redirect to our thank you page
			$this->redirect($this->Link("/thanks/"));
		}
		
		public function thanks()
		{
			return $this->Customise(array());
		}
	}

?>