<?php
/**
 * @method MongoExampleAuthor create() create($data = array()) creates a new MongoExampleAuthor object
 * @method MongoExampleAuthor findOne() findOne($query) returns the first matching MongoExampleAuthor object
 */
class MongoExampleAuthorRepository extends Mongo_Repository
{
    protected $collectionName = 'mongo_example_author';
    protected $autoId = true;
    protected $columns = array('_id', 'name', 'created_at', 'updated_at');

    /**
     * @param MongoExampleAuthor $model a MongoExampleAuthor instance (optional)
     * @param array $options options for the form
     * @return MiniMVC_Form returns the created form object
     */
    public function getForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('Mongo');

        if (!$model) {
            $model = $this->create();
        }

        $options = array_merge(array('name' => 'MongoExampleAuthorForm', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Text('name',
                        array('label' => $i18n->mongoExampleAuthorFormNameLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleAuthorFormNameError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('created_at',
                        array('label' => $i18n->mongoExampleAuthorFormCreatedAtLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleAuthorFormCreatedAtError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('updated_at',
                        array('label' => $i18n->mongoExampleAuthorFormUpdatedAtLabel),
                        array(
                            //new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->mongoExampleAuthorFormUpdatedAtError))
                )));
        

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $model->isNew() ? $i18n->mongoExampleAuthorFormSubmitCreateLabel : $i18n->mongoExampleAuthorFormSubmitUpdateLabel)));

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
     * @return MongoExampleAuthorRepository
     */
    public static function get($connection = null)
    {
        return new self($connection);
    }
}
