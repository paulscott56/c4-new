<?php
namespace C4\Core\View;

class BaseLayout 
{
	
	private $pageHeader;
	
	private $documentBodyTop;
	
	private $documentBodyBottom;
	
	private $pageFooter;
	
	private $pageEnd;
	
	private $HTMLdocument = null;
	
	private $pageLanguage = 'en';
	
	private $pageCharset = 'utf-8';
	
	private $pageTitle = 'C4';
	
	private $metaContentDescription = '';
	private $metaContentAuthor = 'C4 team';
	private $siteName = 'C4';
	private $footerString = '&copy; C4 2012';
	private $pageContent = '';
	
	public function documentFactory()
	{
		// populate defaults
		$this->pageHeader = '<!DOCTYPE html>
                             <html lang="'.$this->pageLanguage.'">
                             <head>
                                 <meta charset="'.$this->pageCharset.'">
                                 <title>'.$this->pageTitle.'</title>
                                 <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                 <meta name="description" content="'.$this->metaContentDescription.'">
                                 <meta name="author" content="'.$this->metaContentAuthor.'">

                                 <link href="../assets/css/bootstrap.css" rel="stylesheet">
                                 <style>
                                     body {
                                         padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
                                     }
                                 </style>
                                 <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">

                                 <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
                                 <!--[if lt IE 9]>
                                     <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
                                 <![endif]-->

                                 <!-- fav and touch icons -->
                                 <link rel="shortcut icon" href="images/favicon.ico">
                                 <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
                                 <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
                                 <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
                             </head>';
		
		$this->documentBodyTop = '<body>
                                      <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">'.$this->siteName.'</a>
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-user"></i> Username
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="#">Profile</a></li>
              <li class="divider"></li>
              <li><a href="#">Sign Out</a></li>
            </ul>
          </div>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Sidebar</li>
              <li class="active"><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
                                      ';
		
		
		$this->documentBodyBottom = '<div class="container-fluid">'.$this->pageContent.'</div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
      </div><!--/row--> <hr>';
		$this->pageFooter = '<footer>
                                 <p>'.$this->footerString.'</p>
                             </footer>
                         </div><!-- /container -->';
		$this->pageEnd = '<!-- javascript ================================================== -->
                          <!-- Placed at the end of the document so the pages load faster -->
                          <!-- script src="../assets/js/bootstrap.js"></script -->
                          <script src="../assets/js/jquery.js"></script>
                          <script src="../assets/js/bootstrap-transition.js"></script>
                          <script src="../assets/js/bootstrap-alert.js"></script>
                          <script src="../assets/js/bootstrap-modal.js"></script>
                          <script src="../assets/js/bootstrap-dropdown.js"></script>
                          <script src="../assets/js/bootstrap-scrollspy.js"></script>
                          <script src="../assets/js/bootstrap-tab.js"></script>
                          <script src="../assets/js/bootstrap-tooltip.js"></script>
                          <script src="../assets/js/bootstrap-popover.js"></script>
                          <script src="../assets/js/bootstrap-button.js"></script>
                          <script src="../assets/js/bootstrap-collapse.js"></script>
                          <script src="../assets/js/bootstrap-carousel.js"></script>
                          <script src="../assets/js/bootstrap-typeahead.js"></script>
                      </body>
                  </html>';
		
		$this->HTMLdocument .= $this->pageHeader;
		$this->HTMLdocument .= $this->documentBodyTop;
		$this->HTMLdocument .= $this->documentBodyBottom;
		$this->HTMLdocument .= $this->pageFooter;
		$this->HTMLdocument .= $this->pageEnd;
		return $this->HTMLdocument; 
		 
	}
	
	public function setPageContent($content) 
	{
		$this->pageContent = $content;
	}
}