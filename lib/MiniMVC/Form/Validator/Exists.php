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
			$model = $element->getForm()->getModel();
			if ($model && method_exists((object) $model, 'getTable'))
			{
                $property = $element->getOption('modelProperty') ? $element->getOption('modelProperty') : $element->getName();
                try
                {
                    return $model->getTable()->exist($property . ' = ?', $value);
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