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

	protected $_columns = array('id', 'slug', 'title');
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
        $form->setElement(new MiniMVC_Form_Element_Text('title',
                        array('label' => $i18n->{namelcfirst}FormTitleLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->{namelcfirst}FormTitleError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->{namelcfirst}FormSubmitLabel)));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = "CREATE TABLE {table} (
					  id int(11) NOT NULL auto_increment,
                      slug varchar(255) NOT NULL,
                      title varchar(255) NOT NULL,
					  PRIMARY KEY  (id),
                      UNIQUE (slug)
					) ENGINE=INNODB DEFAULT CHARSET=utf8";

                $this->_db->query($sql);
            case 1:
        }
        return true;
    }

    /**
     * Deletes the table for this model
     */
    public function uninstall($installedVersion = 'max')
    {

        SWITCH ($installedVersion) {
            case 'max':
            case 1:
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
