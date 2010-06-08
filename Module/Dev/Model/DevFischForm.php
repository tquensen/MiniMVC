<?php
class DevFischForm extends MiniMVC_Form
{
    public function __construct($record = false, $options = array())
    {
        parent::__construct($record, $options);
        $this->setName("DevFischForm");

        //add your elements here

        $this->setValues();
    }
}
