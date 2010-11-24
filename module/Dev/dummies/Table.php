<?php
class {name}Table extends {name}TableBase
{
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
					  PRIMARY KEY ({identifier})
					) ENGINE=INNODB DEFAULT CHARSET=utf8";

                $this->_db->query($sql);
            case 1:
                if ($targetVersion && $targetVersion <= 1) break;
            /* //for every new version add your code below (including the lines "case NEW_VERSION:" and "if ($targetVersion && $targetVersion <= NEW_VERSION) break;")

                $sql = "ALTER TABLE {table}
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
}
