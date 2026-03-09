<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cartProducts
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2024 TechJoomla. All rights reserved.
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

$lang = Factory::getLanguage();
$lang->load('plg_system_quick2cartproducts', JPATH_ADMINISTRATOR);

/**
 * Quick2cartProducts
 *
 * @package     Quick2cartProducts
 * @subpackage  site
 * @since       1.0
 */
class PlgFinderQuick2cartProducts extends Adapter 
{
    /**
     * The plugin identifier.
     *
     * @var    string
     * @since  2.5
     */
    protected $context = 'Products';

    /**
     * The extension name.
     *
     * @var    string
     * @since  2.5
     */
    protected $extension = 'com_quick2cart';

    /**
     * The sublayout to use when rendering the results.
     *
     * @var    string
     * @since  2.5
     */
    protected $layout = 'product';

    /**
     * The type of content that the adapter indexes.
     *
     * @var    string
     * @since  2.5
     */
    protected $type_title = 'Product';

    /**
     * The table name.
     *
     * @var    string
     * @since  2.5
     */
    protected $table = '#__kart_items';

    /**
     * The field the published state is stored in.
     *
     * @var    string
     * @since  2.5
     */
    protected $state_field = 'state';

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Method to setup the indexer to be run.
     *
     * @return  boolean  True on success.
     *
     * @since   2.5
     */
    protected function setup()
    {
        return true;
    }

    /**
     * Method to remove the link information for items that have been deleted.
     *
     * @param   string  $context  The context of the action being performed.
     * @param   Table   $table    A Table object containing the record to be deleted
     *
     * @return  boolean  True on success.
     *
     * @since   2.5
     * @throws  \Exception on database error.
     */
    public function onFinderDelete($context, $table)
    {
        if ($context === 'com_quick2cart.product') {
            $id = $table->item_id;
        } elseif ($context === 'com_finder.index') {
            $id = $table->link_id;
        } else {
            return true;
        }

        // Remove item from the index.
        return $this->remove($id);
    }

    /**
     * Smart Search after save content method.
     * Reindexes the link information for a category that has been saved.
     * It also makes adjustments if the access level of the category has changed.
     *
     * @param   string   $context  The context of the category passed to the plugin.
     * @param   Table    $row      A Table object.
     * @param   boolean  $isNew    True if the category has just been created.
     *
     * @return  void
     *
     * @since   2.5
     * @throws  \Exception on database error.
     */
    public function onFinderAfterSave($context, $row, $isNew): void
    {
        // We only want to handle categories here.
        if ($context === 'com_quick2cart.product') {
            // Check if the access levels are different.
            if (!$isNew && $this->old_access != $row->access) {
                // Process the change.
                $this->itemAccessChange($row);
            }

            // Reindex the Quick2cartProducts item.
            $this->reindex($row->item_id);

            // Check if the parent access level is different.
            if (!$isNew && $this->old_cataccess != $row->access) {
                $this->categoryAccessChange($row);
            }
        }
    }

    /**
     * Smart Search before content save method.
     * This event is fired before the data is actually saved.
     *
     * @param   string   $context  The context of the product passed to the plugin.
     * @param   Table    $row      A Table object.
     * @param   boolean  $isNew    True if the product is just about to be created.
     *
     * @return  boolean  True on success.
     *
     * @since   2.5
     * @throws  \Exception on database error.
     */
    public function onFinderBeforeSave($context, $row, $isNew)
    {
        // We only want to handle categories here.
        if ($context === 'com_quick2cart.product') {
            // Query the database for the old access level and the parent if the item isn't new.
            if (!$isNew) {
                $this->checkItemAccess($row);
                $this->checkCategoryAccess($row);
            }
        }

        return true;
    }

    /**
     * Method to update the link information for items that have been changed
     * from outside the edit screen. This is fired when the item is published,
     * unpublished, archived, or unarchived from the list view.
     *
     * @param   string   $context  The context for the products passed to the plugin.
     * @param   array    $pks      An array of primary key ids of the products that has changed state.
     * @param   integer  $value    The value of the state that the products has been changed to.
     *
     * @return  void
     *
     * @since   2.5
     */
    public function onFinderChangeState($context, $pks, $value)
    {
        // We only want to handle Quick2cart Products here.
        // echo $context;die;
        if ($context === 'com_quick2cart.product') {
            /*
             * The products published state is tied to the parent products
             * published state so we need to look up all published states
             * before we change anything.
             */
            foreach ($pks as $pk) {
                $pk    = (int) $pk;
                $db = Factory::getDbo();
                $query = clone $this->getStateQuery();

                $query->where($db->quoteName('a.item_id') . ' = :PlgFinderQuick2cartProductsId')
                    ->bind(':PlgFinderQuick2cartProductsId', $pk, ParameterType::INTEGER);

                $db->setQuery($query);
                $item = $db->loadObject();

                // Translate the state.
                $state = $item->state;

                $temp = $this->translateState($value, $state);

                // Update the item.
                $this->change($pk, 'state', $temp);

                // Reindex the item.
                $this->reindex($pk);
            }
        }

        // Handle when the plugin is disabled.
        if ($context === 'com_plugins.plugin' && $value === 0) {
            $this->pluginDisable($pks);
        }
    }

    /**
     * Method to index an item. The item must be a Result object.
     *
     * @param   Result  $item  The item to index as a Result object.
     *
     * @return  void
     *
     * @since   2.5
     * @throws  \Exception on database error.
     */
    protected function index(Result $item)
    {
        // Check if the extension is enabled.
        if (ComponentHelper::isEnabled($this->extension) === false) {
            return;
        }

        $item->setLanguage();

        // Initialize the item parameters.
        $item->params = new Registry($item->params);

        $item->metadata = new Registry($item->metadata);

        /*
         * Add the metadata processing instructions based on the category's
         * configuration parameters.
         */

        // Add the meta author.
        $item->metaauthor = $item->metadata->get('author');

        // Handle the link to the metadata.
        $item->addInstruction(Indexer::META_CONTEXT, 'link');
        $item->addInstruction(Indexer::META_CONTEXT, 'metakey');
        $item->addInstruction(Indexer::META_CONTEXT, 'metadesc');
        $item->addInstruction(Indexer::META_CONTEXT, 'metaauthor');
        $item->addInstruction(Indexer::META_CONTEXT, 'author');

        // Trigger the onContentPrepare event.
        $item->summary = Helper::prepareContent($item->summary, $item->params);

        $helperobj        = new comquick2cartHelper;
        $db = Factory::getDBO();
		$query = "SELECT `product_id`, `parent`,`category` FROM `#__kart_items` WHERE item_id=" . $item->item_id;
		$db->setQuery($query);
		$res  = $db->loadAssoc();

        $catpage_Itemid = $helperobj->getitemid('index.php?option=com_quick2cart&view=category&prod_cat=' . $res['category']);
        $link = 'index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' . $res["product_id"] . "&Itemid=" . $catpage_Itemid;
        $item->route = $link;
        $item->url = $link;
        $item->title = $item->name;

        // Get the menu title if it exists.
        $title = $this->getItemMenuTitle($item->route);

        // Adjust the title if necessary.
        if (!empty($title) && $this->params->get('use_menu_title', true)) {
            $item->title = $title;
        }

        // Index the item.
        $this->indexer->index($item);
    }

    /**
     * Method to get the SQL query used to retrieve the list of content items.
     *
     * @param   mixed  $query  A DatabaseQuery object or null.
     *
     * @return  DatabaseQuery  A database object.
     *
     * @since   2.5
     */
    protected function getListQuery($query = null)
    {
        $db = Factory::getDbo();

        // Check if we can use the supplied SQL query.
        $query = $query instanceof DatabaseQuery ? $query : $db->getQuery(true);

        $query->select(
            $db->quoteName(
                [
                    'a.item_id',
                    'a.name',
                    'a.alias',
                    'a.metakey',
                    'a.metadesc',
                    'a.params',
					'a.state'
				]
            )
        )
            ->select(
                $db->quoteName(
                    [
                        'a.description',
                        'a.cdate',
                        'a.state',
                        'a.item_id',
                        'a.name',
                    ],
                    [
                        'summary',
                        'start_date',
                        'access',
                        'a.id',
                        'a.title',
                    ]
                )
            );

        // Handle the alias CASE WHEN portion of the query.
        $case_when_item_alias = ' CASE WHEN ';
        $case_when_item_alias .= $query->charLength($db->quoteName('a.alias'), '!=', '0');
        $case_when_item_alias .= ' THEN ';
        $a_item_id = $query->castAsChar($db->quoteName('a.item_id'));
        $case_when_item_alias .= $query->concatenate([$a_item_id, 'a.alias'], ':');
        $case_when_item_alias .= ' ELSE ';
        $case_when_item_alias .= $a_item_id . ' END AS slug';

        $query->select($case_when_item_alias)
            ->from($db->quoteName('#__kart_items', 'a'))
            ->where($db->quoteName('a.item_id') . ' >= 1');

        return $query;
    }

    /**
     * Method to get a SQL query to load the published and access states for
     * a category and its parents.
     *
     * @return  DatabaseQuery  A database object.
     *
     * @since   2.5
     */
    protected function getStateQuery()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $db->quoteName(
                [
                    'a.item_id',
                    'a.state',
                ]
            )
        )
            ->select(
                $db->quoteName(
                    [
                        'a.' . $this->state_field,
                        'a.state',
                        'a.state',
                        'a.state',
                        'a.item_id',
                        'a.name',
                    ],
                    [
                        'state',
                        'cat_state',
                        'published',
                        'a.published',
                        'a.id',
                        'a.title'
                    ]
                )
            )
            ->from($db->quoteName('#__kart_items', 'a'))
            ->join(
                'INNER',
                $db->quoteName('#__kart_items', 'c'),
                $db->quoteName('c.item_id') . ' = ' . $db->quoteName('a.item_id')
            );

        return $query;
    }

    /**
     * Method to get a content item to index.
     *
     * @param   integer  $id  The id of the content item.
     *
     * @return  Result  A Result object.
     *
     * @since   2.5
     * @throws  \Exception on database error.
     */
    protected function getItem($id)
    {
        // Get the list query and add the extra WHERE clause.
        $query = $this->getListQuery();
        $query->where('a.item_id = ' . (int) $id);

        // Get the item to index.
        $this->db->setQuery($query);
        $item = $this->db->loadAssoc();

        // Convert the item to a result object.
        $item = ArrayHelper::toObject((array) $item, Result::class);

        // Set the item type.
        $item->type_id = $this->type_id;

        // Set the item layout.
        $item->layout = $this->layout;

        return $item;
    }

    /**
     * Method to get the query clause for getting items to update by id.
     *
     * @param   array  $ids  The ids to load.
     *
     * @return  QueryInterface  A database object.
     *
     * @since   2.5
     */
    protected function getUpdateQueryByIds($ids)
    {
        // Build an SQL query based on the item ids.
        $query = $this->db->getQuery(true)
            ->where('a.item_id IN(' . implode(',', $ids) . ')');

        return $query;
    }

    /**
     * Method to update index data on access level changes
     *
     * @param   Table  $row  A Table object
     *
     * @return  void
     *
     * @since   2.5
     */
    protected function itemAccessChange($row)
    {
        $query = clone $this->getStateQuery();
        $query->where('a.item_id = ' . (int) $row->id);

        // Get the access level.
        $this->db->setQuery($query);
        $item = $this->db->loadObject();

        // Set the access level.
        $temp = max($row->access, $item->cat_access);

        // Update the item.
        $this->change((int) $row->id, 'access', $temp);
    }

    /**
     * Method to update index data on published state changes
     *
     * @param   array    $pks    A list of primary key ids of the content that has changed state.
     * @param   integer  $value  The value of the state that the content has been changed to.
     *
     * @return  void
     *
     * @since   2.5
     */
    protected function itemStateChange($pks, $value)
    {
        /*
         * The item's published state is tied to the category
         * published state so we need to look up all published states
         * before we change anything.
         */
        foreach ($pks as $pk) {
            $query = clone $this->getStateQuery();
            $query->where('a.item_id = ' . (int) $pk);

            // Get the published states.
            $this->db->setQuery($query);
            $item = $this->db->loadObject();

            // Translate the state.
            $temp = $this->translateState($value, $item->cat_state);

            // Update the item.
            $this->change($pk, 'state', $temp);
        }
    }	
}
