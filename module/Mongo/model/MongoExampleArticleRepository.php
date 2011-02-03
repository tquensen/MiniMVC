<?php
/**
 * @method MongoExampleArticle create() create($data = array()) creates a new MongoExampleArticle object
 * @method MongoExampleArticle findOne() findOne($query) returns the first matching MongoExampleArticle object
 */
class MongoExampleArticleRepository extends Mongo_Repository
{
    protected $collectionName = 'mongo_example_article';
    protected $autoId = true;
    protected $columns = array('_id', 'slug', 'title', 'content', 'author_id', 'created_at', 'updated_at');

    /**
     * @param MongoExampleArticle $model a MongoExampleArticle instance (optional)
     * @param array $options options for the form
     * @return MiniMVC_Form returns the created form object
     */
    public function getForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('Mongo');

        if (!$model) {
            $model = $this->create();
        }

        $options = array_merge(array('name' => 'MongoExampleArticleForm', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Text('slug',
                        array('label' => $i18n->mongoExampleArticleFormSlugLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleArticleFormSlugError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('title',
                        array('label' => $i18n->mongoExampleArticleFormTitleLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleArticleFormTitleError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('content',
                        array('label' => $i18n->mongoExampleArticleFormContentLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleArticleFormContentError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('author_id',
                        array('label' => $i18n->mongoExampleArticleFormAuthorIdLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleArticleFormAuthorIdError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('created_at',
                        array('label' => $i18n->mongoExampleArticleFormCreatedAtLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleArticleFormCreatedAtError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('updated_at',
                        array('label' => $i18n->mongoExampleArticleFormUpdatedAtLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleArticleFormUpdatedAtError))
                )));
        

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $model->isNew() ? $i18n->mongoExampleArticleFormSubmitCreateLabel : $i18n->mongoExampleArticleFormSubmitUpdateLabel)));

        $form->bindValues();

        return $form;
    }

    /**
     * initiate the collection for this model
     */
    public function install($installedVersion = 0, $targetVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $this->getCollection()->ensureIndex(array('slug' => 1), array('save' => true, 'unique' => true));
                $this->getCollection()->ensureIndex(array('author_id' => 1), array('save' => true));
            case 1:
                if ($targetVersion && $targetVersion <= 1) break;
            /* //for every new version add your code below (including the lines "case NEW_VERSION:" and "if ($targetVersion && $targetVersion <= NEW_VERSION) break;")

                $this->getCollection()->ensureIndex(array('name' => 1), array('save' => true));

            case 2:
                if ($targetVersion && $targetVersion <= 2) break;
             */
        }
        return true;
    }

    /**
     * remove the collection for this model
     */
    public function uninstall($installedVersion = 0, $targetVersion = 0)
    {

        SWITCH ($installedVersion) {
            case 0:
            /* //for every new version add your code directly below "case 0:", beginning with "case NEW_VERSION:" and "if ($targetVersion >= NEW_VERSION) break;"
            case 2:
                if ($targetVersion >= 2) break;
                $c->deleteIndex("name");
             */
            case 1:
                if ($targetVersion >= 1) break;
                $this->getCollection()->drop();
        }
        return true;
    }

    /**
     *
     * @param string $connection the database connection to use (null for the default connection)
     * @return MongoExampleArticleRepository
     */
    public static function get($connection = null)
    {
        return new self($connection);
    }
}
