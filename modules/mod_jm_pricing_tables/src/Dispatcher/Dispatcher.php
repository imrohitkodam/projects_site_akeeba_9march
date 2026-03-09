<?php

/*
 * Copyright (C) joomla-monster.com
 * Website: http://www.joomla-monster.com
 * Support: info@joomla-monster.com
 *
 * JM Pricing Tables is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JM Pricing Tables is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JM Pricing Tables. If not, see <http://www.gnu.org/licenses/>.
*/

namespace Joomla\Module\JMPricingTables\Site\Dispatcher;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Input\Input;
use Joomla\Registry\Registry;

/**
 * Dispatcher class for mod_jm_pricing_tables
 *
 * @since  5.2.0
 */

class Dispatcher implements DispatcherInterface
{
    protected $module;   
    protected $app;

    public function __construct(\stdClass $module, CMSApplicationInterface $app, Input $input)
    {
        $this->module = $module;
        $this->app = $app;
    }
    
    public function dispatch()
    {
		$language = $this->app->getLanguage();
        $language->load('mod_jm_pricing_tables', JPATH_BASE . '/modules/mod_jm_pricing_tables');
        $params = new Registry($this->module->params);

        $app = Factory::getApplication();
		$wa = $app->getDocument()->getWebAssetManager();
		$wa->getRegistry()->addRegistryFile('media/mod_jm_pricing_tables/joomla.asset.json');

        $moduleId = $this->module->id;
		$id = 'jmm-pricing-' . $moduleId;

		$data = trim( $params->get('items') );

		$json_data = ( !empty($data) ) ? json_decode($data) : false;

		if ($json_data === false) {
			echo Text::_('MOD_JM_PRICING_NO_ITEMS');
			return false;
		}

		$field_pattern = '#^jform\[params\]\[([a-zA-Z0-9\_\-]+)\]#i';

		$output_data = array();
		foreach ($json_data as $item) {
			$item_obj = new \stdClass();
			foreach($item as $field) {
				if (preg_match($field_pattern, $field->name, $matches)) {
					$attr = $matches[1];
					if (isset($item_obj->$attr)) {
						if (is_array($item_obj->$attr)) {
							$temp = $item_obj->$attr;
							$temp[] = $field->value;
							$item_obj->$attr = $temp;
						} else {
							$temp = array($item_obj->$attr);
							$temp[] = $field->value;
							$item_obj->$attr = $temp;
						}
					} else {
						$item_obj->$attr = $field->value;
					}
				}
			}
			$output_data[] = $item_obj;
		}

		$elements = count($output_data);

		if( $elements === 0 ) {
			echo Text::_('MOD_JM_PRICING_NO_ITEMS');
			return false;
		}

		$theme = $params->get('theme', 1);
		$theme_class = ( $theme == 1 ) ? 'default' : 'override';

		if( $theme == 1 ) { //default
			$wa->useStyle('mod_jm_pricing_tables');

			$i = 0;
			$style = '';
			foreach($output_data as $item) {
				$i++;
				if( !empty($item->price_color) ) {

					$color = $item->price_color;

					$style .= '#' . $id . '.default .item-' . $i . ' .jmm-price {'
									. 'background: ' . $color . ';'
									. '}';
				}
			}
		}

		if( !empty($style) ) {
			$wa->addInlineStyle($style);
		}
		$span_size = $params->get('span_size', '1');
		$mod_class_suffix = $params->get('moduleclass_sfx', '');

		require ModuleHelper::getLayoutPath('mod_jm_pricing_tables', $params->get('layout', 'default'));
    }
}
