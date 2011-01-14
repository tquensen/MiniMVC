<?php
class UserTable extends UserTableBase
{
    public function getRegisterForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('User');

        $model = $this->create();

        $options = array_merge(array('name' => 'UserRegisterForm', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Text('name',
                        array('label' => $i18n->userFormNameLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormNameRequiredError)),
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => $i18n->userFormNameUniqueError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('email',
                        array('label' => $i18n->userFormEmailLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormEmailRequiredError)),
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => $i18n->userFormEmailUniqueError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Password('password',
                        array('label' => $i18n->userFormPasswordLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormPasswordError))
                )));

        $form->setElement(new MiniMVC_Form_Element_Password('passwordAgain',
                        array('label' => $i18n->userFormPasswordAgainLabel),
                        array(
                            new MiniMVC_Form_Validator_Equals(array('value' => $form->password, 'errorMessage' => $i18n->userFormPasswordAgainError))
                )));

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->userFormSubmitCreateLabel)));

        return $form;
    }

    public function getPasswordForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('User');

        $options = array_merge(array('name' => 'UserPasswordForm', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Password('currentPassword',
                        array('label' => $i18n->userFormCurrentPasswordLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormCurrentPasswordRequiredError)),
                            new MiniMVC_Form_Validator_UserPassword(array('errorMessage' => $i18n->userFormCurrentPasswordInvalidError))
                )));

        $form->setElement(new MiniMVC_Form_Element_Password('password',
                        array('label' => $i18n->userFormPasswordLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormPasswordError))
                )));

        $form->setElement(new MiniMVC_Form_Element_Password('passwordAgain',
                        array('label' => $i18n->userFormPasswordAgainLabel),
                        array(
                            new MiniMVC_Form_Validator_Equals(array('value' => $form->password, 'errorMessage' => $i18n->userFormPasswordAgainError))
                )));

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->userFormSubmitPasswordLabel)));

        return $form;
    }

    public function getLoginForm($model = null, $options = array())
    {
        $i18n = $this->registry->helper->i18n->get('User');

        $model = $this->create();
        
        $options = array_merge(array('name' => 'UserLoginForm', 'model' => $model), $options);

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Text('email',
                        array('label' => $i18n->userFormEmailLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormCurrentPasswordRequiredError)),
                )));

        $form->setElement(new MiniMVC_Form_Element_Password('password',
                        array('label' => $i18n->userFormPasswordLabel),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $i18n->userFormPasswordError)),
                            new MiniMVC_Form_Validator_UserPassword(array('errorMessage' => $i18n->userFormLoginInvalidError, 'loginElement' => $form->email))
                )));

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $i18n->userFormSubmitLoginLabel)));

        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0, $targetVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = "CREATE TABLE user (
                      id INT(11) AUTO_INCREMENT,
                      slug VARCHAR(255),
                      name VARCHAR(255),
                      email VARCHAR(255),
                      password VARCHAR(64),
                      salt VARCHAR(64),
                      auth_token VARCHAR(32),
                      role VARCHAR(32),
                      created_at INT(11),
                      updated_at INT(11),
					  PRIMARY KEY (id),
                      UNIQUE (slug),
                      INDEX (auth_token)
					) ENGINE=INNODB DEFAULT CHARSET=utf8";

                $this->_db->query($sql);
            case 1:
                if ($targetVersion && $targetVersion <= 1) break;
            /* //for every new version add your code below (including the lines "case NEW_VERSION:" and "if ($targetVersion && $targetVersion <= NEW_VERSION) break;")

                $sql = "ALTER TABLE user
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
                $sql = "ALTER TABLE user DROP something";
                $this->_db->query($sql);
             */
            case 1:
                if ($targetVersion >= 1) break;
                $sql = "DROP TABLE user";
                $this->_db->query($sql);
        }
        return true;
    }
}
