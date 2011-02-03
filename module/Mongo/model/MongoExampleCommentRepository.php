<?php
/**
 * @method MongoExampleComment create() create($data = array()) creates a new MongoExampleComment object
 * @method MongoExampleComment findOne() findOne($query) returns the first matching MongoExampleComment object
 */
class MongoExampleCommentRepository extends Mongo_Repository
{
    protected $collectionName = 'mongo_example_comment';
    protected $autoId = true;
    protected $columns = array('_id', 'article_id', 'author_id', 'content', 'created_at', 'updated_at');

    /**
     * @param MongoExampleComment $model a MongoExampleComment instance (optional)
     * @param array $options options for the form
     * @return MiniMVC_Form returns the created form object
     */
    public function getForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('Mongo');

        if (!$model) {
            $model = $this->create();
        }

        $options = array_merge(array('name' => 'MongoExampleCommentForm', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Text('article_id',
                        array('label' => $i18n->mongoExampleCommentFormArticleIdLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleCommentFormArticleIdError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('author_id',
                        array('label' => $i18n->mongoExampleCommentFormAuthorIdLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleCommentFormAuthorIdError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('content',
                        array('label' => $i18n->mongoExampleCommentFormContentLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleCommentFormContentError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('created_at',
                        array('label' => $i18n->mongoExampleCommentFormCreatedAtLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleCommentFormCreatedAtError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('updated_at',
                        array('label' => $i18n->mongoExampleCommentFormUpdatedAtLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleCommentFormUpdatedAtError))
                )));
        

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $model->isNew() ? $i18n->mongoExampleCommentFormSubmitCreateLabel : $i18n->mongoExampleCommentFormSubmitUpdateLabel)));

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
                $this->getCollection()->ensureIndex(array('article_id' => 1), array('save' => true));
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
     * @return MongoExampleCommentRepository
     */
    public static function get($connection = null)
    {
        return new self($connection);
    }
}
