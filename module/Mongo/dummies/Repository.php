<?php
/**
 * @method {name} create() create($data = array()) creates a new {name} object
 * @method {name} findOne() findOne($query) returns the first matching {name} object
 */
class {name}Repository extends Mongo_Repository
{
    protected $collectionName = '{table}';
    protected $className = '{name}';
    protected $autoId = {auto_increment};
    protected $columns = array({columns_list});
    protected $relations = array({relations_list});
    protected $embedded = array({embedded_list});

    /**
     * @param {name} $model a {name} instance (optional)
     * @param array $options options for the form
     * @return MiniMVC_Form returns the created form object
     */
    public function getForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('{module}');

        if (!$model) {
            $model = $this->create();
        }

        $options = array_merge(array('name' => '{name}Form', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        {columns_form}

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $model->isNew() ? $i18n->{namelcfirst}FormSubmitCreateLabel : $i18n->{namelcfirst}FormSubmitUpdateLabel)));

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
     * @return {name}Repository
     */
    public static function get($connection = null)
    {
        return new self(null, null, $connection);
    }
}
