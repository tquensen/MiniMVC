<?php

class MiniMVC_Form_Validator_Unique extends MiniMVC_Form_Validator
{

    public function validate($element, $value)
    {
        if ($this->getOption('values')) {
            return (!in_array($value, $this->getOption('values')));
        } else {
            $record = &$element->getForm()->getRecord();
            if (method_exists((object)$record, 'getTable')) {
                try {
                    $entry = $record->getTable()->findOneBy($element->getName(), $value);
                    if (!$entry || !$entry->exists() || $entry->identifier() == $record->identifier()) {
                        return true;
                    }
                } catch (Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }

}

