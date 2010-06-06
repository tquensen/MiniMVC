<?php

class UserForm extends MiniMVC_Form
{

    public function __construct($record = false, $options = array())
    {
        parent::__construct($record, $options);
        if (!isset($options['type']) || ! method_exists($this, 'get' . ucfirst($options['type']))) {
            throw new Exception('No valid type for UserForm specified!');
        }

        $this->{'get' . ucfirst($options['type'])} ();
    }

    protected function getLogin()
    {
        $this->setName("UserLoginForm");

        $this->addElement(new MiniMVC_Form_Element_Text('email',
                        array('defaultValue' => $this->record['email'], 'label' => 'E-Mail Adresse:'),
                        array(
                            new MiniMVC_Form_Validator_Exists(),
                            new MiniMVC_Form_Validator_Required()
                        )
                )
        );

        $this->addElement(new MiniMVC_Form_Element_Password('password',
                        array('label' => 'Passwort:'),
                        array(
                            new MiniMVC_Form_Validator_Required()
                        )
                )
        );


        $this->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'einloggen')));

        $this->setValues();
    }

    protected function getLogout()
    {
        $this->setName("UserLogoutForm");

        $this->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'ausloggen')));

        $this->setValues();
    }

    protected function getRegister()
    {
        $this->setName("UserRegisterForm");

        $this->addElement(new MiniMVC_Form_Element_Text('name',
                        array('defaultValue' => $this->record['name'], 'label' => 'Username:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Dieser username existiert bereits!')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Kein Username angegeben!'))
                )));
        $this->addElement(new MiniMVC_Form_Element_Text('email',
                        array('defaultValue' => $this->record['email'], 'label' => 'E-Mail Adresse:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Diese E-Mail existiert bereits!')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'keine E-Mail angegeben'))
                )));
        $this->addElement(new MiniMVC_Form_Element_Password('password', array('label' => 'Passwort:'), new MiniMVC_Form_Validator_Required(array('errorMessage' => 'kein PW angegeben'))));

        $this->addElement(new MiniMVC_Form_Element_Password('passwordAgain', array('label' => 'Passwort wiederholen:'), array(new MiniMVC_Form_Validator_Required(), new MiniMVC_Form_Validator_Equals(array('value' => $this->password, 'errorMessage' => 'Passwörter stimmen nicht überein')))));

        $this->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'registrieren')));

        $this->setValues();
    }

    protected function getEdit()
    {
        $this->setName("UserEditForm");

        $this->addElement(new MiniMVC_Form_Element_Text('name',
                        array('defaultValue' => $this->record['name'], 'label' => 'Username:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'Username existiert bereits')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'Username ist ein Pflichtfeld'))
                        )
                )
        );

        $this->addElement(new MiniMVC_Form_Element_Text('email',
                        array('defaultValue' => $this->record['email'], 'label' => 'E-Mail Adresse:'),
                        array(
                            new MiniMVC_Form_Validator_Unique(array('errorMessage' => 'E-Mail existiert bereits')),
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'E-Mail ist ein Pflichtfeld'))
                        )
                )
        );

        $this->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'speichern')));

        $this->setValues();
    }

    protected function getEditPassword()
    {
        $this->setName("UserEditPasswordForm");

        $this->addElement(new MiniMVC_Form_Element_Text('oldPassword',
                        array('label' => 'Aktuelles Passwort:'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'oldPassword ist ein Pflichtfeld')),
                            new MiniMVC_Form_Validator_UserPassword(array('errorMessage' => 'oldPassword ist falsch'))
                        )
                )
        );

        $this->addElement(new MiniMVC_Form_Element_Text('password',
                        array('label' => 'Neues Passwort:'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'newPassword ist ein Pflichtfeld'))                           
                        )
                )
        );

        $this->addElement(new MiniMVC_Form_Element_Text('newPassword2',
                        array('label' => 'Neues Passwort wiederholen:'),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => 'newPassword2 ist ein Pflichtfeld'))
                        )
                )
        );

        $this->password->addValidator(new MiniMVC_Form_Validator_Equals(array('value' => $this->newPassword2, 'errorMessage' => 'Passwörter stimmen nicht überein')));
        $this->newPassword2->addValidator(new MiniMVC_Form_Validator_Equals(array('value' => $this->password, 'errorMessage' => 'Passwörter stimmen nicht überein')));

        $this->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'speichern')));

        $this->setValues();
    }

}
