<?php

namespace IQnection\ServiceAreasPage;

use SilverStripe\Forms;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;
use IQnection\FormUtilities\FormUtilities;

class ServiceAreasPageController extends \PageController
{
	private static $allowed_actions = [
		"ServiceAreasPageForm",
		"thanks"
	];
	
	public function init()
	{
		parent::init();
		$maps_url = "https://maps.googleapis.com/maps/api/js?sensor=false";
		if ($key = Config::inst()->get('ContactPage','google_maps_api_key'))
		{
			$maps_url .= "&key=".$key;
		}
		Requirements::javascript($maps_url);
	}
	
	public function PageCSS()
	{
		return array_merge(
			[
				"css/pages/Page.css",
				"css/pages/Page_extension.css",
				"css/form.css",
			],
			parent::PageCSS()
		);
	}
	
	public function PageJS()
	{
		return array_merge(
			["javascript/jquery.validate.nospam.js"],
			parent::PageJS()
		);
	}
	
	public function CustomJS()
	{
		$js = parent::CustomJS();
		$address_objects = array();
		if ($this->getParentServiceAreasPage()->ServiceAreasPageLocations()->Count())
		{
			foreach($this->getParentServiceAreasPage()->ServiceAreasPageLocations() as $Location) 
			{
				$address_objects[] = [
					"Title" => $Location->Title,
					"Address" => $Location->Address,
					"LatLng" => [
						$Location->MapLatitude,
						$Location->MapLongitude
					]
				];
			}
		}
		$js .= "\naddress_objects = ".json_encode($address_objects).";";
		if ($this->getParentServiceAreasPage()->MapIcon()->Exists())
		{
			$js .= "\nvar mapIcon=\"".$this->getParentServiceAreasPage()->MapIcon()->ScaleMaxWidth(25)->getURL()."\";";
		}
		return $js;
	}
	
	public function ServiceAreasPageForm()
	{
		// create the form fields
		$fields = Forms\FieldList::create();
		if ($this->getParentServiceAreasPage()->isFieldEnabled('FirstName')) $fields->push( Forms\TextField::create("FirstName", "First Name") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('LastName')) $fields->push( Forms\TextField::create("LastName", "Last Name") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('Address')) $fields->push( Forms\TextField::create("Address", "Address") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('Address2')) $fields->push( Forms\TextField::create("Address2", "Address (line 2)") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('City')) $fields->push( Forms\TextField::create("City", "City") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('State')) $fields->push( Forms\DropdownField::create("State", "State", FormUtilities::GetStates())
			->setValue("PA")
			->setEmptyString('-- Select --') );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('ZipCode')) $fields->push( Forms\TextField::create("ZipCode", "Zip Code") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('Email')) $fields->push( Forms\EmailField::create("Email", "Email Address") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('Phone')) $fields->push( Forms\TextField::create("Phone", "Phone Number") );
		if ($this->getParentServiceAreasPage()->isFieldEnabled('Comments')) $fields->push( Forms\TextareaField::create("Comments", "Comments") );
			 
		// create a form action
		$actions = Forms\FieldList::create(
			Forms\FormAction::create("SubmitServiceAreasForm", "Submit")
		);
		
		$validator = FormUtilities::RequiredFields($fields,$this->getParentServiceAreasPage()->getRequiredFields());
		
		return Forms\Form::create(
			$this, 
			"ServiceAreasPageForm", 
			$fields, 
			$actions,
			$validator
		);
	}

	public function SubmitServiceAreasForm( $data, $form )
	{
		// magical spam protection
		if( !FormUtilities::validateAjaxCode() )
		{
			$this->request->getSession()->set("FormInfo.ServiceAreasForm_ServiceAreasForm.data", $data);
			$form->addErrorMessage("Message", "Error, please enable javascript to use this form", "bad");
			return $this->redirectBack();	
		}	
		
		$SAPage = $this->getParentServiceAreasPage();
		
		// save submission for posterity
		$submission = new ServiceAreasPageFormSubmission();
		$form->saveInto($submission);
		$submission->ServiceAreasPageID = $SAPage->ID;
		$submission->PageURL = $_SERVER['HTTP_REFERER'];
		$submission->write();
		$this->extend('onAfterSubmit',$submission);			
		// send email to this address if specified
		$emails = $SAPage->FormRecipients();
		
		foreach($emails as $email)
		{
			FormUtilities::SendSSEmail($this, $email->Email, $data,$submission,$this->FromEmail);
		}
		
		// redirect to our thank you page
		return $this->redirect($this->Link("/thanks/"));
	}
	
	public function thanks()
	{
		return $this;
	}
}




