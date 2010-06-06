<?php
class MiniMVC_Form_Validator_Exists extends MiniMVC_Form_Validator
{
	public function validate($element, $value)
	{
		if ($this->getOption('values'))
		{
			return (in_array($value, $this->getOption('values')));
		}
		else
		{
			$record = &$element->getForm()->getRecord();
			if (method_exists((object) $record, 'getTable'))
			{
                try
                {
                    return (bool) $record->getTable()->findOneBy($element->getName(), $value);
			
                }
                catch (Exception $e)
                {
                    return false;
                }
            }
		}
		return false;
	}
}