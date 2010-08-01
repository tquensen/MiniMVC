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
			$model = $this->getOption('model');
			if (method_exists((object) $model, 'getTable'))
			{
                try
                {
                    return (bool) $model->getTable()->count($element->getName() . ' = "' . mysqli_real_escape_string($value).'"');
			
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