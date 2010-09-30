<?php

/**
 * @method User getOne()
 * @method User getOneBy()
 * @method User loadOne()
 * @method User loadOneBy()
 * @method User create()
 */
class UserTable extends MiniMVC_Table
{
    protected $_table = 'user';
    protected $_model = 'User';
    protected $_columns = array('id', 'slug', 'name', 'password', 'salt', 'email', 'role');
    protected $_relations = array();
    protected $_identifier = 'id';
    protected $_isAutoIncrement = true;
    protected static $_instance = null;

    public function getRegisterForm()
    {
        $user = $this->create();
        $form = new MiniMVC_Form(array('name' => 'UserRegisterForm', 'model' => $user));
        $form->setElement(new MiniMVC_Form_Element_Text('name',
                        array('label' => 'Username:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Dieser Username existiert bereits!')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Kein Username angegeben!'))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('email',
                        array('label' => 'E-Mail Adresse:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Diese E-Mail existiert bereits!')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'keine E-Mail angegeben'))
                )));

        $form->setElement(new MiniMVC_Form_Element_Fieldset('pwFieldset', array('legend' => 'Passwörter')));
        $form->setElement(new MiniMVC_Form_Element_Password('password', array('label' => 'Passwort:'), new MiniMVC_Form_Validator_Required(array('errorMessage' => 'kein PW angegeben'))));

        $form->setElement(new MiniMVC_Form_Element_Password('passwordAgain', array('label' => 'Passwort wiederholen:'), array(new MiniMVC_Form_Validator_Equals(array('value' => $form->password, 'errorMessage' => 'Passwörter stimmen nicht überein')), new MiniMVC_Form_Validator_Required())));
        $form->setElement(new MiniMVC_Form_Element_Fieldsetend('pwFieldsetEnd'));

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'registrieren')));

        return $form;
    }

    public function getEditForm($user)
    {
        $form = new MiniMVC_Form(array('name' => 'UserEditForm', 'model' => $user));
        $form->setElement(new MiniMVC_Form_Element_Text('name',
                        array('label' => 'Username:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Dieser Username existiert bereits!')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Kein Username angegeben!'))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('email',
                        array('label' => 'E-Mail Adresse:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Diese E-Mail existiert bereits!')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'keine E-Mail angegeben'))
                )));

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'speichern')));

        return $form;
    }

    public function getEditPasswordForm($user)
    {
        $form = new MiniMVC_Form(array('name' => 'UserEditForm', 'model' => $user));
        $form->setElement(new MiniMVC_Form_Element_Password('currentPassword', array('label' => 'Aktuelles Passwort:', 'defaultValue' => ''), array(new MiniMVC_Form_Validator_Required(array('errorMessage' => 'kein aktuelles PW angegeben')), new MiniMVC_Form_Validator_UserPassword(array('errorMessage' => 'Aktuelles PW falsch')))));

        $form->setElement(new MiniMVC_Form_Element_Password('password', array('label' => 'Neues Passwort:', 'defaultValue' => ''), new MiniMVC_Form_Validator_Required(array('errorMessage' => 'kein PW angegeben'))));

        $form->setElement(new MiniMVC_Form_Element_Password('passwordAgain', array('label' => 'Neues Passwort wiederholen:', 'defaultValue' => ''), array(new MiniMVC_Form_Validator_Equals(array('value' => $form->password, 'errorMessage' => 'Passwörter stimmen nicht überein')), new MiniMVC_Form_Validator_Required())));

        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'speichern')));

        return $form;
    }

    public function getLoginForm($widget = false)
    {
        $user = $this->create();
        if ($widget) {
            $form = new MiniMVC_Form(array('name' => 'UserLoginForm', 'model' => $user, 'action' => $this->registry->helper->url->get('user.login')));
        } else {
            $form = new MiniMVC_Form(array('name' => 'UserLoginForm', 'model' => $user));
        }
        $form->setElement(new MiniMVC_Form_Element_Text('email',
                        array('label' => 'E-Mail Adresse:'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Kein Username angegeben!'))
                        )
                )
        );

        $form->setElement(new MiniMVC_Form_Element_Password('password',
                        array('label' => 'Passwort:'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Kein Passwort angegeben!'))
                        )
                )
        );


        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'einloggen')));

        return $form;
    }

    public function getLogoutForm($widget = false)
    {
        if ($widget) {
            $form = new MiniMVC_Form(array('name' => 'UserLogoutForm', 'action' => $this->registry->helper->url->get('user.logout')));
        } else {
            $form = new MiniMVC_Form(array('name' => 'UserLogoutForm'));
        }
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'ausloggen')));
        return $form;
    }

    /**
     * Created the table for this model
     */
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = "CREATE TABLE user (
					  id int NOT NULL auto_increment,
					  slug varchar(255) NOT NULL,
					  name varchar(255) NOT NULL,
					  password varchar(255) NOT NULL,
					  salt varchar(255) NOT NULL,
					  email varchar(255) NOT NULL,
					  role varchar(255) NOT NULL,
					  PRIMARY KEY  (id)
					) ENGINE=INNODB DEFAULT CHARSET=utf8";

                $this->_db->query($sql);
            case 1:
        }
        return true;
    }

    public function install($installedVersion = 0, $targetVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                if (!$targetVersion) break;
                $sql = "CREATE TABLE user (
					  id int NOT NULL auto_increment,
					  slug varchar(255) NOT NULL,
					  name varchar(255) NOT NULL,
					  password varchar(255) NOT NULL,
					  salt varchar(255) NOT NULL,
					  email varchar(255) NOT NULL,
					  role varchar(255) NOT NULL,
					  PRIMARY KEY  (id)
					) ENGINE=INNODB DEFAULT CHARSET=utf8";

                $this->_db->query($sql);
            case 1:
                if ($targetVersion && $targetVersion <= 1) break;
            /* //for every new version add your code below (including the lines "case NEW_VERSION:" and "if ($targetVersion && $targetVersion <= NEW_VERSION) break;")

                $sql = "ALTER TABLE {table} (
					  ADD something varchar(255)";

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
                $sql = "DROP TABLE user";
                $this->_db->query($sql);
        }
        return true;
    }

    /**
     *
     * @return UserTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new UserTable;
        }
        return self::$_instance;
    }

}
