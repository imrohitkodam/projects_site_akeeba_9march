<?php
#-------------------------------------------------------------
# Copyright (C) 2014 by web-eau.net  	   	   	   	   	   	   	   	   	 
# Homepage   : www.web-eau.net	   	   	   	   	   	   		 
# Author     : Daniel	    		   	   	   	   	   	   	   	 
# Email      : daniel@web-eau.net 	   	   	   	   	   	   	     
# Version    : 3.0.0                      	   	    	   	   		 
# License    : http://www.gnu.org/copyleft/gpl.html GNU/GPL          
#-------------------------------------------------------------

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


$document = JFactory::getDocument();
$baseurl = JURI::root();
$document->addStyleSheet($baseurl.'plugins/content/responsive-tables/responsive-tables.css');

?>