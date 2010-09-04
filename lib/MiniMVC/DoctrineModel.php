<?php
/**
 * MiniMVC_Model is the base class for all Models (Doctrine Records)
 */
class MiniMVC_Model extends Doctrine_Record
{
    /**
     *
     * @param array $options the options which will be passed th the form constructor
     * @return MiniMVC_Form
     */
	public function getForm($options = array())
	{
		$formClass = get_class($this).'Form';
		if (class_exists($formClass))
		{
			return new $formClass($this, $options);
		}
		return false;
	}

	public function installModel()
	{
		//$tableData = $this->getTable()->getExportableFormat();
        //var_dump($tableData);return;
        Doctrine_Core::createTablesFromArray(array(get_class($this)));
		//MiniMVC_Registry::getInstance()->db->getConnection()->export->createTable($tableData['tableName'], $tableData['columns'], $tableData['options']);
		return true;
	}

	public function updateModel($fromVersion)
	{
			
	}

	public function uninstallModel($fromVersion)
	{
		$tableData = $this->getTable()->getExportableFormat();
		MiniMVC_Registry::getInstance()->db->export->dropTable($tableData['tableName']);
		return true;
	}
}