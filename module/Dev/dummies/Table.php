<?php

/**
 * @method {name} getOne() getOne($id)
 * @method {name} getOneBy() getOneBy($field, $value, $order = null, $offset = 0)
 * @method {name} loadOne() loadOne($id, $reload = false)
 * @method {name} loadOneBy() loadOneBy($condition, $value = null, $order = null, $offset = 0)
 * @method {name} loadOneWithRelations() loadOneWithRelations($id, $relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = false)
 * @method {name} loadOneWithRelationsBy() loadOneWithRelationsBy($relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = true)
 * @method {name} create() create($data = array())
 */
class {name}Table extends MiniMVC_Table
{

	protected $_table = '{table}';
    protected $_model = '{name}';

	protected $_columns = array({columns_list});
    protected $_relations = array();
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;

    public function getForm($model = null)
    {
        $i18n = $this->registry->helper->i18n->get('{modlc}');

        if (!$model) {
            $model = $this->create();
        }
        $form = new MiniMVC_Form(array('name' => '{name}Form', 'model' => $model));
        {columns_form}
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->{namelcfirst}FormSubmitLabel)));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0, $targetVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = "CREATE TABLE {table} (
                      {columns_sql}
					  PRIMARY KEY (id)
					) ENGINE=INNODB DEFAULT CHARSET=utf8";

                $this->_db->query($sql);
            case 1:
                if ($targetVersion && $targetVersion <= 1) break;
            /* //for every new version add your code below (including the lines "case NEW_VERSION:" and "if ($targetVersion && $targetVersion <= NEW_VERSION) break;")

                $sql = "ALTER TABLE {table} (
					  ADD something VARCHAR(255)";

                $this->_db->query($sql);

            case 2:
                if ($targetVersion && $targetVersion <= 2) break;
             */
        }
        return true;
    }

    /**
     * Deletes the table for this model
     */
    public function uninstall($installedVersion = 0, $targetVersion = 0)
    {

        SWITCH ($installedVersion) {
            case 0:
            /* //for every new version add your code directly below "case 0:", beginning with "case NEW_VERSION:" and "if ($targetVersion >= NEW_VERSION) break;"
            case 2:
                if ($targetVersion >= 2) break;
                $sql = "ALTER TABLE {table} DROP something";
                $this->_db->query($sql);
             */
            case 1:
                if ($targetVersion >= 1) break;
                $sql = "DROP TABLE {table}";
                $this->_db->query($sql);
        }
        return true;
    }

    /**
     *
     * @return {name}Table
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new {name}Table;
        }
        return self::$_instance;
    }
}
