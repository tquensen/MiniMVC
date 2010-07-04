<?php
class TestExampleModelForm extends MiniMVC_Form
{
    public function __construct($record = false, $options = array())
    {
        $this->setName("TestExampleModelForm");
        parent::__construct($record, $options);
        

        //add your elements here

        foreach (MiniMVC_Registry::getInstance()->settings->config['enabledLanguages'] as $language) {
            $this->addElement(new MiniMVC_Form_Element_Text('name_'.$language, array('label' => 'Name ('.$language.')')));
            $this->addElement(new MiniMVC_Form_Element_Textarea('description_'.$language, array('label' => 'Text ('.$language.')')));
        }

        $this->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'GO')));

        $this->setValues();
    }

    public function updateRecord() {
        if ($this->record)
		{
            foreach (MiniMVC_Registry::getInstance()->settings->config['enabledLanguages'] as $language) {
                $name = 'name_'.$language;
                $description = 'description_'.$language;
                if ($this->$name && $this->$description && $this->$name->value)
                {
                    $this->record->Translation[$language]->name = $this->$name->value;
                    $this->record->Translation[$language]->description = $this->$description->value;
                }

            }
			//$this->record->save();
            return $this->record;
		}
        return false;
    }
}
