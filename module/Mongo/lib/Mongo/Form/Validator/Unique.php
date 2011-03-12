<?php

class Mongo_Form_Validator_Unique extends MiniMVC_Form_Validator
{

    public function validate($value)
    {
        if ($this->getOption('model') && $this->getOption('property')) {
            $model = $this->getOption('model');
            if (is_string($model) && class_exists($model)) {
                $model = new $model();
            }
            $property = $this->getOption('property');		
        } elseif ($element = $this->getElement()) {
            $model = $element->getForm()->getModel();
            $property = $element->getOption('modelProperty') ? $element->getOption('modelProperty') : $element->getName();
        }
        
        if (!empty($model) && !empty($property) && $model instanceof Mongo_Model)
        {
            try {
                $entry = $model->getRepository()->findOne(array($property => $value));
                if (!$entry || $entry->_id == $model->_id) {
                    return true;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        
        return false;
    }

}

