<?php

/**
 * @method BlogComment getOne()
 * @method BlogComment getOneBy()
 * @method BlogComment loadOne()
 * @method BlogComment loadOneBy()
 * @method BlogComment loadOneWithRelations()
 * @method BlogComment loadOneWithRelationsBy()
 * @method BlogComment create()
 */
class BlogCommentTable extends MiniMVC_Table
{

	protected $_table = 'blog_comment';
    protected $_model = 'BlogComment';

	protected $_columns = array('id', 'user_id', 'message', 'username', 'email', 'website', 'post_id', 'created_at');
    protected $_relations = array('Post' => array('BlogPost', 'post_id', 'id', true));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;

    public function getForm($model = null)
    {
        $i18n = $this->registry->helper->i18n->get('Blog');

        if (!$model) {
            $model = $this->create();
        }
        $form = new MiniMVC_Form(array('name' => 'BlogCommentForm', 'model' => $model));
        $form->setElement(new MiniMVC_Form_Element_Text('name',
                        array('label' => $i18n->BlogCommentFormNameLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->BlogCommentFormNameError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->BlogCommentFormSubmitLabel)));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = 'CREATE TABLE `blog_comment` (
					  `id` int(11) NOT NULL auto_increment,
                      `user_id` int(11)  NOT NULL,
                      `message` text NOT NULL,
                      `username` varchar(255) NOT NULL,
                      `email` varchar(255) NOT NULL,
                      `website` varchar(255) NOT NULL,
                      `created_at` datetime NOT NULL,
                      `post_id` int(11)  NOT NULL,
					  PRIMARY KEY  (`id`),
                      INDEX (user_id),
                      INDEX (post_id)
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
                $sql = 'DROP TABLE `blog_comment`';
                $this->_db->query($sql);
        }
        return true;
    }

    /**
     *
     * @return BlogCommentTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new BlogCommentTable;
        }
        return self::$_instance;
    }
}
