<?php

/**
 * @package     Techjoomla.Libraries
 * @subpackage  DB Synchronization
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2023 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
 
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Load language file, defines.php, subscriptions and preferences model, backend base class
Factory::getLanguage()->load('lib_db_sync', JPATH_SITE, null, false, true);

 /**
 * DB Migration
 *
 * @package     Techjoomla.Libraries
 * @subpackage  DB Synchronization
 * @since       1.0
 */
trait LibDBSync
{
	/**
	 * Function to sync database
	 *
	 * @param   INT  $databaseFilenameWithPath  get file path
	 *
	 * @return  
	 */
	public function syncDatabase($databaseFilenameWithPath)
	{
		$app      = Factory::getApplication();
		$db = Factory::getDBO();
		$dbPrefix = $app->getCfg('dbprefix');

		$oldTables = $db->setQuery('SHOW TABLES')
						->loadColumn();

		$xmlData = simplexml_load_file($databaseFilenameWithPath) or die(Text::sprintf('LIB_DB_SYNC_FAIL_TO_LOAD_FILE', (string)$databaseFilenameWithPath));

		foreach ($xmlData->table as $key => $table) 
		{
			$generatedTablename = str_replace("#__", $dbPrefix, $table['name']);
			$db = Factory::getDBO();

			if (!in_array($generatedTablename, $oldTables))
			{
				// Inserting new table
				$query = "CREATE TABLE IF NOT EXISTS `". $table['name'] ."` ";
				$db = Factory::getDBO();

				$query .= "( ";
				$columnQuery = '';
				$columnCount  = 0;

				foreach ($table->column as $column) 
				{
					$columnName = $column['name'];
					if ($columnCount != 0)
					{
						$columnQuery .= ", ";
					}
					$columnCount++;

					$columnCommonQuery = $this->getColumnQuery($column);
					$columnQuery .= $columnCommonQuery;
				}

				$query .= $columnQuery;
				if (isset($table->primary) && $table->primary)
				{
					$primaryColumn = $table->primary;
					$primaryColumnQuery = ", PRIMARY KEY (`" . $primaryColumn['column_name'] . "`)";
					$query .= $primaryColumnQuery;
				}

				if (isset($table->key) && $table->key)
				{
					$keyColumn = $table->key;
					$keyColumnQuery = ", KEY `" . $keyColumn['name'] ."` (`" . $keyColumn['column_name'] . "`)";
					$query .= $keyColumnQuery;
				}

				$query .= " ) ";

				if (isset($table['engine']) && $table['engine'])
				{
					$query .= " ENGINE=" . $table['engine'];
				}

				if (isset($table['charset_name']) && $table['charset_name'])
				{
					$query .= " DEFAULT CHARSET=" . $table['charset_name'];
				}

				if (isset($table['collation_name']) && $table['collation_name'])
				{
					$query .= " DEFAULT COLLATE=" . $table['collation_name'];
				}

				if (isset($table['auto_increment']) && $table['auto_increment'])
				{
					$query .= " AUTO_INCREMENT =" . $table['auto_increment'];
				}

				$db->setQuery($query);

				try 
				{
					$db->execute();
				} catch (\Exception $th) {
					$app->enqueueMessage( Text::sprintf('LIB_DB_SYNC_CREATE_TABLE_ERROR', (string)$table['name']), 'Error');

					continue;
				}
			}
			else 
			{
				$db = Factory::getDBO();
				$columnInfo = $db->getTableColumns($table['name'], false);
				$oldColumns  = array_keys($columnInfo);

				foreach ($table->column as $column)
				{
					$columnName = $column['name'];
					// Inserting new columns
					if (isset($column['old_name']) && $column['old_name'] && in_array($column['old_name'], $oldColumns))
					{
						$db = Factory::getDBO();
						$query = "ALTER TABLE `" . $table['name'] . "` CHANGE  `" . $column['old_name'] . '` ';
						$columnCommonQuery = $this->getColumnQuery($column);
						$query .= $columnCommonQuery;
						$db->setQuery($query);

						try 
						{
							$db->execute();
						} catch (\Exception $th) {
							$app->enqueueMessage( Text::sprintf('LIB_DB_SYNC_UPDATE_COLUMN_ERROR', (string)$table['name'], (string)$column['name']), 'Error');

							continue;
						}
					} else if (!in_array($columnName, $oldColumns))
					{
						$query = "ALTER TABLE `" . $table['name'] . "` ADD ";

						$columnCommonQuery = $this->getColumnQuery($column);
						$query .= $columnCommonQuery;

						if (isset($column['after']) && $column['after'])
						{
							$query .= " AFTER " . $column['after'];
						}

						$db->setQuery($query);

						try 
						{
							$db->execute();
						} catch (\Exception $th) {
							$app->enqueueMessage( Text::sprintf('LIB_DB_SYNC_CREATE_COLUMN_ERROR', (string)$table['name'], (string)$column['name']), 'Error');

							continue;
						}
					}
					else 
					{
						// If column already present then check any update
						$isUpdate = $this->checkColumnIsUpdated($column, $columnInfo[(string)$columnName]);
						if ($isUpdate)
						{
							if (isset($column['is_null']) && $column['is_null'] && $column['default'])
							{
								$db = Factory::getDBO();
								$query = $db->getQuery(true);
								$fields = array(
									$db->qn((string)$columnName) . ' = ' . $db->q($column['default']),
								);

								$conditions = array(
									$db->qn((string)$columnName) . ' IS NULL',
								);

								$query->update($db->qn((string)$table['name']))->set($fields)->where($conditions);
								$db->setQuery($query);
								try 
								{
									$db->execute();
								} catch (\Exception $th) {
									$app->enqueueMessage( Text::sprintf('LIB_DB_SYNC_UPDATE_COLUMN_NULL_to_NOT_NULL_ERROR', (string)$table['name'], (string)$column['name']), 'Error');

									continue;
								}
							}

							$db = Factory::getDBO();
							$query = "ALTER TABLE `" . $table['name'] . "` CHANGE `" . $columnName . '` ';
							$columnCommonQuery = $this->getColumnQuery($column);
							$query .= $columnCommonQuery;
							$db->setQuery($query);

							try 
							{
								$db->execute();
							} catch (\Exception $th) {
								$app->enqueueMessage( Text::sprintf('LIB_DB_SYNC_UPDATE_COLUMN_ERROR', (string)$table['name'], (string)$column['name']), 'Error');

								continue;
							}
						}
					}
				}
			}

			// This code for create index
			$db = Factory::getDBO();
			$query      = "SHOW INDEX FROM ". $table['name'] ;
			$db->setQuery($query);
			$indexList = $db->loadObjectList();
			$oldIndexes = [];

			if ($indexList)
			{
				foreach ($indexList as $index) {
					array_push($oldIndexes, $index->Key_name);
				}
			}

			foreach ($table->index as $index)
			{
				if (!in_array($index['name'], $oldIndexes))
				{
					$db = Factory::getDBO();
					$query      = "CREATE INDEX ". $index['name'] ." ON ". $table['name'] ." ( ". $index['column_names'] . " ) ";
					$db->setQuery($query);

					try 
					{
						$db->execute();
					} catch (\Exception $th) {
						$app->enqueueMessage( Text::sprintf('LIB_DB_SYNC_CREATE_INDEX_ERROR', (string)$table['name'], (string)$index['name']), 'Error');

						continue;
					}
				}
			}
		}

		// $app->enqueueMessage(Text::_('LIB_DB_SYNC_SUCCESS_MESSAGE'), 'Success');
	}

	/**
	 * Function to get column query
	 *
	 * @param   Array  $column information array
	 *
	 * @return  String
	 */
	public function getColumnQuery($column)
	{
		$columnQuery = " `". $column['name'] . "` " . $column['data_type'];
		if (isset($column['size']) && $column['size'])
		{
			$columnQuery .= "(" . $column['size'] . ") ";
		}

		if (isset($column['unsigned']) && $column['unsigned'])
		{
			$columnQuery .= " unsigned ";
		}

		if (isset($column['charset_name']) && $column['charset_name'])
		{
			$columnQuery .= " CHARACTER SET " . $column['charset_name'];
		}

		if (isset($column['collation_name']) && $column['collation_name'])
		{
			$columnQuery .= " COLLATE " . $column['collation_name'];
		}

		if (isset($column['is_null']) && (int)$column['is_null'])
		{
			if (!(isset($column['default']) && $column['default']))
			{
				$columnQuery .= " NULLABLE ";
			}
		}
		else 
		{
			$columnQuery .= " NOT NULL ";
		}

		if (isset($column['auto_increment']) && $column['auto_increment'])
		{
			$columnQuery .= " AUTO_INCREMENT ";
		}

		if (isset($column['default']) && $column['default'])
		{
			if ($column['default'] == 'null')
			{
				$columnQuery .= " DEFAULT NULL";
			}
			else if ($column['default'] == 'CURRENT_TIMESTAMP')
			{
				$columnQuery .= " DEFAULT CURRENT_TIMESTAMP";
			}
			else 
			{
				$columnQuery .= " DEFAULT '" . $column['default'] ."'";
			}
		}

		if (isset($column['comment']) && $column['comment'])
		{
			$columnQuery .= " COMMENT '" . $column['comment'] ."'";
		}

		return $columnQuery;
	}

	/**
	 * Function to old column and new column is not updated
	 *
	 * @param   Array  $columnNewInfo new column information array
	 * @param   Array  $columnOldInfo old column information array
	 *
	 * @return  String
	 */
	public function checkColumnIsUpdated($columnNewInfo, $columnOldInfo)
	{
		$updateColumnWithSize = $columnNewInfo['data_type'] . '(' . $columnNewInfo['size'] . ')';
		if ($columnOldInfo->Type == 'int' && $columnNewInfo['data_type'] == 'int')
		{

		}
		else if ($columnNewInfo['data_type'] == 'tinyint' && $columnOldInfo->Type == 'tinyint')
		{
			
		}
		else if ($columnNewInfo['data_type'] == 'BOOLEAN' && $columnOldInfo->Type == 'tinyint(1)')
		{
			
		}
		else if ($columnOldInfo->Type == 'int unsigned' && $columnNewInfo['data_type'] == 'int' && $columnNewInfo['unsigned'])
		{

		}
		else if ($columnNewInfo['data_type'] == 'datetime' && $columnOldInfo->Type == 'datetime')
		{
			
		}
		else if ($columnNewInfo['data_type'] == 'timestamp' && $columnOldInfo->Type == 'timestamp')
		{
			
		}
		else if ($columnNewInfo['data_type'] == 'text' && $updateColumnWithSize != $columnOldInfo->Type)
		{
			
		}
		else if ($updateColumnWithSize != $columnOldInfo->Type)
		{
			return 'sizeIssue';
		}

		if ($columnNewInfo['collation_name'])
		{
			if ($columnNewInfo['collation_name'] != $columnOldInfo->Collation)
			{
				return 'collation_name';
			}
		}

		if ($columnNewInfo['is_null'] == '0' && $columnOldInfo->Null != 'NO')
		{
			return 'is_null_0';
		}
		else if ($columnNewInfo['is_null'] == '1' && $columnOldInfo->Null != 'YES')
		{
			return 'is_null_1';
		}

		if ($columnNewInfo['default'])
		{
			if ($columnNewInfo['default'] == 'null' && is_null($columnOldInfo->Default))
			{

			}
			else if ($columnNewInfo['default'] == 'CURRENT_TIMESTAMP' && $columnOldInfo->Default == 'CURRENT_TIMESTAMP')
			{

			}
			else if ($columnNewInfo['default'] != $columnOldInfo->Default)
			{
				return 'default';
			}
		}

		if ($columnNewInfo['comment'])
		{
			if ($columnNewInfo['comment'] != $columnOldInfo->Comment)
			{
				return 'comment';
			}
		}

		return false;
	}
}
