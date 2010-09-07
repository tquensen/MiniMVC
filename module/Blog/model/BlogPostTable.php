<?php

/**
 * @method BlogPost getOne()
 * @method BlogPost getOneBy()
 * @method BlogPost loadOne()
 * @method BlogPost loadOneBy()
 * @method BlogPost loadOneWithRelations()
 * @method BlogPost loadOneWithRelationsBy()
 * @method BlogPost create()
 */
class BlogPostTable extends MiniMVC_Table
{

	protected $_table = 'blog_post';
    protected $_model = 'BlogPost';

	protected $_columns = array('id', 'slug', 'title', 'text', 'status', 'created_at');
    protected $_relations = array('Comments' => array('BlogComment', 'id', 'post_id'), 'Tags' => array('BlogTag', 'post_id', 'tag_id', 'blog_post_tag'));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;

    public function getForm($model = null)
    {
        $i18n = $this->registry->helper->i18n->get('Blog');

        if (!$model) {
            $model = $this->create();
            $currentTags = array();
        } else {
            $currentTags = array();
            foreach ($model->loadTags() as $tag) {
                $currentTags[] = $tag->getIdentifier();
            }
        }

        $allTags = array();
        foreach (BlogTagTable::getInstance()->loadAll('title ASC') as $tag) {
                $allTags[$tag->getIdentifier()] = $tag->title;
        }
        
        $form = new MiniMVC_Form(array('name' => 'BlogPostForm', 'model' => $model));
        $form->setElement(new MiniMVC_Form_Element_Text('title',
                        array('label' => 'Post-Titel:', 'info' => '(Pflichtfeld)'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Der Titel muss angegeben werden!'))
                )));

        $form->setElement(new MiniMVC_Form_Element_Textarea('text',
                        array('label' => 'Post-Inhalt:', 'info' => '(Pflichtfeld)'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Der Inhalt muss angegeben werden!'))
                )));
        $form->setElement(new MiniMVC_Form_Element_Select('status',
                        array(
                            'label' => 'Status',
                            'info' => '(Pflichtfeld)',
                            'errorMessage' => 'Bitte wähle einen gültigen Status!',
                            'options' => array('draft' => 'Vorschau', 'published' => 'Öffentlich')
                            ),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Bitte wähle einen gültigen Status!'))
                )));
        $form->setElement(new MiniMVC_Form_Element_SelectMultiple('tags',
                        array(
                            'label' => 'Tags',
                            'info' => '(Mehrfachauswahl mit gedrückter Strg bzw. Command-Taste)',
                            'errorMessage' => 'Die Auswahl ist ungültig!',
                            'options' => $allTags,
                            'defaultValue' => $currentTags
                            )
                    ));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'speichern')));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = 'CREATE TABLE `blog_post` (
					  `id` int(11) NOT NULL auto_increment,
                      `slug` varchar(255) NOT NULL,
                      `title` varchar(255) NOT NULL,
                      `text` text NOT NULL,
                      `status` enum("draft", "published") NOT NULL,
					  PRIMARY KEY  (`id`),
                      UNIQUE (slug),
                      INDEX (status)
					) ENGINE=INNODB DEFAULT CHARSET=utf8';

                $this->_db->query($sql);

                $sql = 'CREATE TABLE `blog_post_tag` (
					  `id` int(11) NOT NULL auto_increment,
                      `post_id` int(11) NOT NULL,
                      `tag_id` int(11) NOT NULL,
					  PRIMARY KEY  (`id`),
                      INDEX (post_id),
                      INDEX (tag_id)
					) ENGINE=INNODB DEFAULT CHARSET=utf8';

                $this->_db->query($sql);
            case 1:
                $sql = "ALTER TABLE blog_post ADD created_at datetime NOT NULL";
                $this->_db->query($sql);
            case 2:
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
            case 2:
                $sql = "ALTER TABLE blog_post DROP created_at";
                $this->_db->query($sql);
            case 1:
                $sql = 'DROP TABLE `blog_post`';
                $this->_db->query($sql);

                $sql = 'DROP TABLE `blog_post_tag`';
                $this->_db->query($sql);
        }
        return true;
    }

    /**
     *
     * @return BlogPostTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new BlogPostTable;
        }
        return self::$_instance;
    }
}
