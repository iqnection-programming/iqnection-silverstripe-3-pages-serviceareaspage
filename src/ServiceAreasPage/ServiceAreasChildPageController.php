<?php

namespace IQnection\ServiceAreasPage;

class ServiceAreasChildPageController extends ServiceAreasPageController
{
	public function PageCSS()
	{
		return array_merge(
			parent::PageCSS(),
			['css/pages/ServiceAreasPage.css']
		);
	}
	
	public function PageJS()
	{
		return array_merge(
			parent::PageJS(),
			['javascript/pages/ServiceAreasPage.js']
		);
	}
}
	