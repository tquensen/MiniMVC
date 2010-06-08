<?php
class DevFischArtForm extends MiniMVC_Form
{
    public function __construct($record = false, $options = array())
    {
        parent::__construct($record, $options);
        $this->setName("DevFischArtForm");

        //add your elements here

        $this->setValues();
    }
}
