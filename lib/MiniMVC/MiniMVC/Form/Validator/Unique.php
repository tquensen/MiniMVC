<?php

class MiniMVC_Form_Validator_Unique extends MiniMVC_Form_Validator
{

    public function validate($value)
    {
        if ($this->getOption('values')) {
            return (!in_array($value, $this->getOption('values')));
        }

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
        
        if (!empty($model) && !empty($property) && method_exists((object) $model, 'getTable'))
        {
            try {
                $entry = $model->getTable()->loadOneBy($property . ' = ?', $value);
                if (!$entry || $entry->getIdentifier() == $model->getIdentifier()) {
                    return true;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        
        return false;
    }

}

