<?php

/**
 * @method BlogTag getOne()
 * @method BlogTag getOneBy()
 * @method BlogTag loadOne()
 * @method BlogTag loadOneBy()
 * @method BlogTag loadOneWithRelations()
 * @method BlogTag loadOneWithRelationsBy()
 * @method BlogTag create()
 */
class BlogTagTable extends MiniMVC_Table
{

	protected $_table = 'blog_tag';
    protected $_model = 'BlogTag';

	protected $_columns = array('id', 'slug', 'title');
    protected $_relations = array('Posts' => array('BlogPost', 'tag_id', 'post_id', 'post_post_tag'));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;

    public function getForm($model = null)
    {
        $i18n = $this->registry->helper->i18n->get('Blog');

        if (!$model) {
            $model = $this->create();
        }
        $form = new MiniMVC_Form(array('name' => 'BlogTagForm', 'model' => $model));
        $form->setElement(new MiniMVC_Form_Element_Text('name',
                        array('label' => $i18n->BlogTagFormNameLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->BlogTagFormNameError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->BlogTagFormSubmitLabel)));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = 'CREATE TABLE `blog_tag` (
					  `id` int(11) NOT NULL auto_increment,
                      `slug` varchar(255) NOT NULL,
                      `title` varchar(255) NOT NULL,
					  PRIMARY KEY  (`id`),
                      UNIQUE (slug)
					) ENGINE=INNODB DEFAULT CHARSET=utf8';

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
                $sql = 'DROP TABLE `blog_tag`';
                $this->_db->query($sql);
        }
        return true;
    }

    /**
     *
     * @return BlogTagTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new BlogTagTable;
        }
        return self::$_instance;
    }
}
