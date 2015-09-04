<?
	class ServiceAreasChildPage extends ServiceAreasPage
	{
		private static $allowed_children = array(
			"ServiceAreasChildPage"
		);
		
		private static $defaults = array(
			'ShowInMenus' => true,
			'Sequence' => '0'
		);
		
		public function getCMSFields()
		{
			$fields = parent::getCMSFields();
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
			return $this->getPointer()->ThankYouText;
		}	
	}
	
	class ServiceAreasChildPage_Controller extends ServiceAreasPage_Controller
	{
		function PageCSS()
		{
			$CSS = parent::PageCSS();
			$CSS[] = 'iq-serviceareaspage/css/pages/ServiceAreasPage.css';
			$this->extend('updatePageCSS',$CSS);
			return $CSS;
		}
		
		function PageJS()
		{
			$JS = parent::PageJS();
			$JS[] = 'iq-serviceareaspage/javascript/pages/ServiceAreasPage.js';
			$this->extend('updatePageJS',$JS);
			return $JS;
		}
	}
?>