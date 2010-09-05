<?php

/**
 * @method CmsArticle getOne()
 * @method CmsArticle getOneBy()
 * @method CmsArticle loadOne()
 * @method CmsArticle loadOneBy()
 * @method CmsArticle create()
 */
class CmsArticleTable extends MiniMVC_Table
{

	protected $_table = 'cms_article';
    protected $_model = 'CmsArticle';

	protected $_columns = array('id', 'slug', 'title', 'teaser', 'content', 'status');
    protected $_relations = array();
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;

    public function getForm($model = null)
    {
        $i18n = $this->registry->helper->i18n->get('Cms');

        if (!$model) {
            $model = $this->create();
        }
        $form = new MiniMVC_Form(array('name' => 'CmsArticleForm', 'model' => $model));
        $form->setElement(new MiniMVC_Form_Element_Text('title',
                        array('label' => $i18n->CmsArticleFormTitleLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->CmsArticleFormTitleError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Textarea('teaser',
                        array('label' => $i18n->CmsArticleFormTeaserLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->CmsArticleFormTeaserError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Textarea('content',
                        array('label' => $i18n->CmsArticleFormContentLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->CmsArticleFormContentError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Select('status',
                        array('label' => $i18n->CmsArticleFormStatusLabel, 'errorMessage' => $i18n->CmsArticleFormStatusError, 'options' => array('draft' => $i18n->CmsArticleFormStatusOptionDraft, 'published' => $i18n->CmsArticleFormStatusOptionPublished)),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->CmsArticleFormStatusError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->CmsArticleFormSubmitLabel)));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = 'CREATE TABLE `cms_article` (
					  `id` int(11) NOT NULL auto_increment,
                      `slug` varchar(255) NOT NULL,
                      `title` varchar(255) NOT NULL,
                      `teaser` text NOT NULL,
                      `content` text NOT NULL,
                      `status` enum("draft", "published") NOT NULL,
					  PRIMARY KEY  (`id`),
                      UNIQUE (`slug`),
                      INDEX (`status`)
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

        SWITCH ($installed_version) {
            case 'max':
            case 1:
                $sql = 'DROP TABLE `cms_article`';
                $this->_db->query($sql);
        }
        return true;
    }

    /**
     *
     * @return CmsArticleTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new CmsArticleTable;
        }
        return self::$_instance;
    }
}
