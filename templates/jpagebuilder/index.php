<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.JPageBuilder
 *
 * @author Joomla! Extensions Store
 * @copyright (C) 2025 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// Get parameter values
$customFavicon   = $this->params->get('customfavicon', 0);
if($customFavicon) {
	$faviconSvg   = $this->params->get('favicon_svg', '');
	$faviconSvg = StringHelper::substr($faviconSvg, 0, StringHelper::strpos($faviconSvg, '#'));
	
	$faviconIco   = $this->params->get('favicon_ico', '');
	$faviconIco = StringHelper::substr($faviconIco, 0, StringHelper::strpos($faviconIco, '#'));
	
	$faviconMask  = $this->params->get('favicon_mask', '');
	$faviconMask = StringHelper::substr($faviconMask, 0, StringHelper::strpos($faviconMask, '#'));
	
	$maskColor    = $this->params->get('favicon_color', '#000');
	
	// Fallbacks (if no custom icon, fall back to Cassiopeia defaults or none)
	if ($faviconSvg) {
		$this->addHeadLink($faviconSvg, 'icon', 'rel', ['type' => 'image/svg+xml']);
	}
	
	if ($faviconIco) {
		$this->addHeadLink($faviconIco, 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
	}
	
	if ($faviconMask) {
		$this->addHeadLink($faviconMask, 'mask-icon', 'rel', ['color' => $maskColor]);
	}
} else {
	// Add a default favicon
	$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'icon', 'rel', ['type' => 'image/x-icon']);
}

$option   = $app->getInput()->getCmd('option', '');
$view     = $app->getInput()->getCmd('view', '');
$layout   = $app->getInput()->getCmd('layout', '');
$task     = $app->getInput()->getCmd('task', '');
$itemid   = $app->getInput()->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$templatePath = 'templates/' . $this->template;

$siteLogo = $this->params->get('sitelogo', '');
$includeSiteName = $this->params->get('include_site_name', 1);
$showHeader = $this->params->get('showheader', 1);
$stickyHeader = $this->params->get('stickyheader', 0);
$stickyHeaderClass = $stickyHeader ? ' class="sticky-top"' : '';
$headerColor = $this->params->get('headercolor', '#212529');
$headerTextColor = $this->params->get('headertextcolor', '#FFFFFF');
$blackButtonColor = $this->params->get('menubuttoncolor', 0);
$minHeight =  $this->params->get('minheight', '960px');
$maxWidth =  $this->params->get('maxwidth', '');
$showFooter =  $this->params->get('showfooter', 1);
$footerColor = $this->params->get('footercolor', '#212529');
$containerSitebodyFullsize =  $this->params->get('containersitebodyfullsize', 1);
$mainmenuright = $this->params->get ( 'mainmenuright', 0 );
$customCssCode = $this->params->get ( 'customcsscode', '' );

// Menu customization settings
$customMenuStyles = $this->params->get ( 'custom_menu_styles', 0 );
$maxWidth = $this->params->get ( 'maxwidth', '' );
$headerColor = $this->params->get ( 'headercolor', '#212529' );
$headerTextColor = $this->params->get ( 'headertextcolor', '#FFFFFF' );
$menuTextColor = $this->params->get ( 'menu_text_color', '#333333' );
$menuBgColor = $this->params->get ( 'menu_bg_color', '#ffffff' );
$menuHoverTextColor = $this->params->get ( 'menu_hover_text_color', '#ffffff' );
$menuHoverBgColor = $this->params->get ( 'menu_hover_bg_color', '#cc0000' );
$submenuTextColor = $this->params->get ( 'submenu_text_color', '#333333' );
$submenuBgColor = $this->params->get ( 'submenu_bg_color', '#ffffff' );
$submenuHoverTextColor = $this->params->get ( 'submenu_hover_text_color', '#ffffff' );
$submenuHoverBgColor = $this->params->get ( 'submenu_hover_bg_color', '#003399' );
$menuBorderColor = $this->params->get ( 'menu_border_color', '#cccccc' );
$menuBorderWidth = $this->params->get ( 'menu_border_width', '1' );
$menuSeparatorColor = $this->params->get ( 'menu_separator_color', '#cccccc' );
$menuSeparatorWidth = $this->params->get('menu_separator_width', '1');
$menuActiveTextColor = $this->params->get('menu_active_text_color', '#ffffff');
$menuActiveBgColor = $this->params->get('menu_active_bg_color', '#aaaaaa');
$submenuSeparatorColor = $this->params->get('menu_horizontal_separator_color', '#cccccc');
$submenuSeparatorHeight = $this->params->get('menu_horizontal_separator_height', '1') . 'px';
$menuFontSize = $this->params->get('menu_font_size', '16') . 'px';
$menuFontWeight = $this->params->get('menu_font_weight', '400');
$menuTextTransformTop = $this->params->get('menu_text_transform_top', 'none');
$menuTextTransformSub = $this->params->get('menu_text_transform_sub', 'none');
$menuHoverMode = $this->params->get('menu_hover_mode', 'normal');
$menuBorderRadius = $this->params->get('menu_border_radius', '0');
$menuMargin = $this->params->get('menu_margin', '0');

function brightenColor($hexColor, $increment = 26) {
	// Ensure the color is in the correct hex format
	$hexColor = ltrim($hexColor, '#');
	
	// Split the hex color into RGB components
	$r = hexdec(substr($hexColor, 0, 2));
	$g = hexdec(substr($hexColor, 2, 2));
	$b = hexdec(substr($hexColor, 4, 2));
	
	// Increase each component by the increment, making sure not to exceed 255
	$r = min(255, $r + $increment);
	$g = min(255, $g + $increment);
	$b = min(255, $b + $increment);
	
	// Convert the RGB values back to a hex string
	return sprintf("#%02x%02x%02x", $r, $g, $b);
}

//load bootstrap collapse js (required for mobile menu to work)
HTMLHelper::_('bootstrap.collapse');
//dropdown needed for 2nd level menu items
HTMLHelper::_('bootstrap.dropdown');
//You could also load all of bootstrap js with this line, but it's not recommended because it's a lot of extra code that you probably don't need
//HTMLHelper::_('bootstrap.framework');

//Register our web assets (CSS/JS files) with the Web Asset Manager joomla.asset.json
$wa->useStyle('template.jpagebuilder.mainstyles');
$wa->useStyle('template.jpagebuilder.user');
$wa->useScript('template.jpagebuilder.scripts');

//Set viewport meta tag for mobile responsiveness -- very important for scaling on mobile devices
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
	
	<?php $wa->addInlineStyle('main.sitebody{min-height:' . $minHeight . '}header nav.bg-dark{background-color:' . $headerColor . '!important}header nav.bg-dark,header nav.bg-dark a,header nav.bg-dark span{color:' . $headerTextColor . '!important}footer.bg-dark{background-color:' . $footerColor . '!important}');?>
	
	<?php if($maxWidth):
		$wa->addInlineStyle('div.container{max-width:' . $maxWidth . '}');
	endif;?>
	
	<?php if($blackButtonColor):
		$wa->addInlineStyle('nav.navbar-dark button.navbar-toggler{border-color:rgb(0 0 0 / 55%)}nav.navbar-dark button.navbar-toggler span.navbar-toggler-icon{filter:invert(1)}');
	endif;?>
	
	<?php if($mainmenuright):
		$wa->addInlineStyle('#mainmenu{flex: 0 1 auto}');
	endif;?>

	<?php
	if ($customMenuStyles) {
		$baseCSS = "
		    div.container { max-width: {$maxWidth}; }
		    header { background-color: {$headerColor}; color: {$headerTextColor}; }
		    
		    .navbar-nav .nav-link {
		        position: relative;
		        z-index: 1;
		        overflow: hidden;
		        color: {$menuTextColor} !important;
		        background-color: {$menuBgColor};
		        font-size: {$menuFontSize};
		        font-weight: {$menuFontWeight};
				border-radius: {$menuBorderRadius}px;
		        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
		    }

			#mainmenu > .navbar-nav > .nav-item > .nav-link {
		        margin: {$menuMargin}px;
			}
		    
		    .navbar-nav > li.nav-item > a.nav-link,
			.navbar-nav > li.nav-item > span.nav-link {
		        text-transform: {$menuTextTransformTop};
		    }
		    
		    .dropdown-menu > li.nav-item > a.nav-link {
		        text-transform: {$menuTextTransformSub};
		        transition: background-color 0.3s ease, color 0.3s ease;
		    }
		    
		    .dropdown-menu {
		        background-color: {$submenuBgColor};
		        border-radius: 5px;
		    }
		    .dropdown-menu .nav-item,
		    .dropdown-menu .nav-item a {
		        background-color: {$submenuBgColor};
		    }
		    .dropdown-menu .dropdown-item {
		        color: {$submenuTextColor} !important;
		        transition: background-color 0.3s ease, color 0.3s ease;
		    }
		    
		    .navbar-nav li.nav-item.active a {
		        text-decoration: none;
		    }
		    #mainmenu > .navbar-nav > li.nav-item.active > a,
		    #mainmenu > .navbar-nav > li.nav-item.active.dropdown.parent span.nav-link,
		    #mainmenu > .navbar-nav > li.nav-item.active.dropdown.parent ul.dropdown-menu li.nav-item.current.active a.dropdown-item,
		    #mainmenu > .navbar-nav > li.nav-item.active.dropdown.parent li.active.dropdown.parent > a.nav-link.dropdown-toggle.dropdown-item {
		        color: {$menuActiveTextColor} !important;
		        background-color: {$menuActiveBgColor} !important;
		        text-decoration: none;
		    }
		    
		    @media(min-width:992px) {
		        #mainmenu > .navbar-nav > .nav-item {
		            transition: border-color 0.3s ease;
		            border-left: {$menuSeparatorWidth}px solid {$menuSeparatorColor};
		        }
		        
		        #mainmenu > .navbar-nav > .nav-item:hover {
		            border-left: {$menuSeparatorWidth}px solid {$menuHoverBgColor};
		        }
		        
		        #mainmenu > .navbar-nav > li.nav-item.current.active,
		        #mainmenu > .navbar-nav > li.nav-item.active.dropdown.parent {
		            border-left: {$menuSeparatorWidth}px solid {$menuActiveBgColor};
		        }
		    }
		    
		    .dropdown-menu > li.nav-item + li.nav-item {
		        border-top: {$submenuSeparatorHeight} solid {$submenuSeparatorColor};
		    }
		    
		    @media (min-width: 992px) {
		        .navbar .dropdown-menu {
		            display: block;
		            opacity: 0;
		            visibility: hidden;
		            transform: translateY(10px);
		            transition: opacity 0.3s ease, transform 0.3s ease;
		        }
		        
		        .navbar .dropdown.show > .dropdown-menu,
		        .navbar .dropdown:hover > .dropdown-menu {
		            opacity: 1;
		            visibility: visible;
		            transform: translateY(0);
		        }
		        
		        .navbar .dropdown-toggle::after {
		            transition: transform 0.3s ease;
		        }
		        
		        .navbar .dropdown.show > .dropdown-toggle::after,
		        .navbar .dropdown:hover > .dropdown-toggle::after {
		            transform: rotate(180deg);
		        }
		    }";

		switch ($menuHoverMode) {
			case 'expand':
				$hoverCSS = "
				    .navbar-nav .nav-link::before,
				    .dropdown-menu > li.nav-item > a.nav-link::before {
				        content: '';
				        position: absolute;
				        inset: 0;
				        background: {$menuHoverBgColor};
				        transform: scaleY(0);
				        transform-origin: 50% 50%;
				        transition: transform .3s ease;
				        z-index: -1;
				    }
				    .dropdown-menu > li.nav-item > a.nav-link::before {
				        background: {$submenuHoverBgColor};
				    }
				    .navbar-nav .nav-link:hover::before,
				    .dropdown-menu > li.nav-item > a.nav-link:hover::before {
				        transform: scaleY(1);
				    }
				    .navbar-nav .nav-link:hover { color: {$menuHoverTextColor} !important; }
				    .dropdown-menu > li.nav-item > a.nav-link:hover { color: {$submenuHoverTextColor} !important; }
				    
				    #mainmenu > .navbar-nav > li.nav-item.dropdown:has(> .dropdown-menu:hover) > a.nav-link::before,
				    #mainmenu > .navbar-nav > li.nav-item.dropdown.show > a.nav-link::before,
				    #mainmenu > .navbar-nav > li.nav-item.dropdown.parent:has(> .dropdown-menu:hover) > span.nav-link::before,
				    #mainmenu > .navbar-nav > li.nav-item.dropdown.parent.show > span.nav-link::before {
				        transform: scaleY(1);
				        background: {$menuHoverBgColor};
				    }
				    #mainmenu > .navbar-nav > li.nav-item.dropdown:has(> .dropdown-menu:hover) > a.nav-link,
				    #mainmenu > .navbar-nav > li.nav-item.dropdown.show > a.nav-link,
				    #mainmenu > .navbar-nav > li.nav-item.dropdown.parent:has(> .dropdown-menu:hover) > span.nav-link,
				    #mainmenu > .navbar-nav > li.nav-item.dropdown.parent.show > span.nav-link {
				        color: {$menuHoverTextColor} !important;
				    }
				    
				    @media (min-width: 992px) {
				      #mainmenu > .navbar-nav > li.nav-item.dropdown:has(> .dropdown-menu:hover),
				      #mainmenu > .navbar-nav > li.nav-item.dropdown.show,
				      #mainmenu > .navbar-nav > li.nav-item.parent:has(> .dropdown-menu:hover),
				      #mainmenu > .navbar-nav > li.nav-item.parent.show {
				        border-left: {$menuSeparatorWidth}px solid {$menuHoverBgColor} !important;
				      }
				      #mainmenu > .navbar-nav > li.nav-item.active.dropdown:has(> .dropdown-menu:hover),
				      #mainmenu > .navbar-nav > li.nav-item.active.dropdown.show,
				      #mainmenu > .navbar-nav > li.nav-item.active.parent:has(> .dropdown-menu:hover),
				      #mainmenu > .navbar-nav > li.nav-item.active.parent.show {
				        border-left: {$menuSeparatorWidth}px solid {$menuHoverBgColor} !important;
				      }
				    }
				    #mainmenu > .navbar-nav > li.nav-item.active:hover {
				      border-left: {$menuSeparatorWidth}px solid {$menuHoverBgColor} !important;
				    }
				    #mainmenu > .navbar-nav > li.nav-item.active.current:hover {
				      border-left: {$menuSeparatorWidth}px solid {$menuHoverBgColor} !important;
				    }
				    .dropdown-menu > li.nav-item.dropdown:has(> .dropdown-menu:hover) > a.nav-link::before,
				    .dropdown-menu > li.nav-item.dropdown.show > a.nav-link::before,
				    .dropdown-menu > li.nav-item.dropdown.parent:has(> .dropdown-menu:hover) > a.nav-link::before,
				    .dropdown-menu > li.nav-item.dropdown.parent.show > a.nav-link::before {
				        transform: scaleY(1);
				        background: {$submenuHoverBgColor};
				    }
				    .dropdown-menu > li.nav-item.dropdown:has(> .dropdown-menu:hover) > a.nav-link,
				    .dropdown-menu > li.nav-item.dropdown.show > a.nav-link,
				    .dropdown-menu > li.nav-item.dropdown.parent:has(> .dropdown-menu:hover) > a.nav-link,
				    .dropdown-menu > li.nav-item.dropdown.parent.show > a.nav-link {
				        color: {$submenuHoverTextColor} !important;
				    }";
				break;
			case 'slide':
				$hoverCSS = "
				    .navbar-nav .nav-link::before {
				        content: '';
				        position: absolute;
				        inset: 0;
				        background: {$menuHoverBgColor};
				        transform: translateX(-100%);
				        transition: transform 0.3s ease;
				        z-index: -1;
				    }
				    .navbar-nav .nav-link:hover::before {
				        transform: translateX(0);
				    }
				    .navbar-nav .nav-link:hover { color: {$menuHoverTextColor} !important; }";
				break;
			case 'fade':
				$hoverCSS = "
				    .navbar-nav .nav-link::before {
				        content: '';
				        position: absolute;
				        inset: 0;
				        background: {$menuHoverBgColor};
				        opacity: 0;
				        transition: opacity 0.3s ease;
				        z-index: -1;
				    }
				    .navbar-nav .nav-link:hover::before {
				        opacity: 1;
				    }
				    .navbar-nav .nav-link:hover { color: {$menuHoverTextColor} !important; }";
				break;
			case 'underline':
				$hoverCSS = "
					.navbar-nav .nav-link { position: relative; }
					
					.navbar-nav .nav-link:not(.dropdown-toggle)::before {
					    content: '';
					    position: absolute;
					    left: 0;
					    bottom: 0;
					    width: 0;
					    height: 2px;
					    background: {$menuHoverBgColor};
					    transition: width 0.3s ease;
					}
					
					.navbar-nav .nav-link:not(.dropdown-toggle):hover::before {
					    width: 100%;
					}
					
					.navbar-nav .nav-link:hover {
					    color: {$menuHoverTextColor} !important;
				}";
				break;
			case 'outline':
				$hoverCSS = "
                .navbar-nav .nav-link {
                    border: 1px solid transparent;
                    transition: border-color 0.3s ease, color 0.3s ease;
                }
                .navbar-nav .nav-link:hover {
                    border-color: {$menuHoverBgColor};
                    color: {$menuHoverTextColor} !important;
                }";
				break;
			case 'glow':
				$hoverCSS = "
                .navbar-nav .nav-link {
                    transition: box-shadow 0.3s ease, color 0.3s ease;
                }
                .navbar-nav .nav-link:hover {
                    color: {$menuHoverTextColor} !important;
                    box-shadow: 0 0 8px {$menuHoverBgColor};
                }";
				break;
			default: // normal
				$hoverCSS = "
				    .navbar-nav .nav-link:hover,
				    .nav-item:has(.dropdown-menu:hover) > .nav-link {
				        color: {$menuHoverTextColor} !important;
				        background-color: {$menuHoverBgColor};
				    }
				    .dropdown-menu .dropdown-item:hover {
				        color: {$submenuHoverTextColor} !important;
				        background-color: {$submenuHoverBgColor};
				    }";
				break;
		}
		$wa->addInlineStyle($baseCSS . $hoverCSS);
		$wa->addInlineScript("!function(){function e(){return window.innerWidth>=992}function t(){var t=document.querySelectorAll('.navbar-nav .dropdown-toggle');e()?(t.forEach(function(e){e.hasAttribute('data-bs-toggle')&&(e.dataset.bsToggleSaved=e.getAttribute('data-bs-toggle'),e.removeAttribute('data-bs-toggle')),window.bootstrap&&bootstrap.Dropdown&&(bootstrap.Dropdown.getInstance(e)||null)&&bootstrap.Dropdown.getInstance(e).dispose(),e.setAttribute('aria-expanded','false')}),document.querySelectorAll('.navbar-nav .dropdown.show,.navbar-nav .dropdown-menu.show').forEach(function(e){e.classList.remove('show')})):t.forEach(function(e){e.hasAttribute('data-bs-toggle')||e.setAttribute('data-bs-toggle',e.dataset.bsToggleSaved||'dropdown'),window.bootstrap&&bootstrap.Dropdown&&bootstrap.Dropdown.getOrCreateInstance(e)})}document.addEventListener('click',function(t){if(e()){var o=t.target.closest('.navbar-nav .dropdown-toggle');o&&(t.preventDefault(),t.stopImmediatePropagation())}},!0);function o(){t()}var n;window.addEventListener('resize',function(){clearTimeout(n),n=setTimeout(o,120)}),'loading'===document.readyState?document.addEventListener('DOMContentLoaded',o):o()}();");
	} else {
		$wa->addInlineStyle('ul.dropdown-menu{background-color:' . brightenColor($headerColor) . '!important}ul.dropdown-menu a.dropdown-item{color:' . $headerTextColor . '!important}');
	}
	?>
	
	<?php if(trim($customCssCode)):
		$wa->addInlineStyle($customCssCode);
	endif;?>
</head>

<body class="site <?php echo $pageclass; ?>">
	<?php if($showHeader):?>
		<header<?php echo $stickyHeaderClass;?>>
	        <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
	            <div class="container">
	            	<?php if($siteLogo):?>
	            		<img data-href="<?php echo $this->baseurl; ?>/" src="<?php echo Uri::base(true) . '/' . StringHelper::substr($siteLogo, 0, StringHelper::strpos($siteLogo, '#'));?>" alt="Logo" aria-label="Logo" role="button" class="navbar-logo"/>
	            	<?php elseif(!$siteLogo && $includeSiteName):?>
		                <a href="<?php echo $this->baseurl; ?>/" class="navbar-brand"><?php echo ($sitename); ?></a>
	            	<?php endif;?>
	                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation">
	                	<span class="navbar-toggler-icon"></span>
	                </button>
	                <?php if ($this->countModules('menu')): ?>
	                <div class="collapse navbar-collapse" id="mainmenu">
	                	<jdoc:include type="modules" name="menu" style="none" />
	                </div>
	                <?php endif; ?>
	            </div>
	        </nav>
	    </header>
    <?php endif;?>

    <main class="sitebody">
        <div class="container<?php echo $containerSitebodyFullsize ? ' container-sitebody-fullsize' : '';?>">
            <div class="row">
            	<jdoc:include type="modules" name="breadcrumbs" style="none" />
            	<jdoc:include type="message" />
				<main>
					<jdoc:include type="component" />
				</main>
            </div>
        </div>
    </main>

	<?php if($showFooter):?>
	    <footer class="footer mt-auto py-3 bg-dark navbar-expand-lg">
	        <div class="container">
	            <?php if ($this->countModules('footer')) : ?>
	                <jdoc:include type="modules" name="footer" style="none" />
	            <?php endif; ?>
	        </div>
	    </footer>
    <?php endif;?>
</body>
</html>
