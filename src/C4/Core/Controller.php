<?php
namespace C4\Core;
use C4\Core\View\BaseLayout;

use C4\Core\View;

class Controller 
{
	public function renderPage()
	{
		$baseLayout = new BaseLayout();
		return $baseLayout->documentFactory();
	}
}