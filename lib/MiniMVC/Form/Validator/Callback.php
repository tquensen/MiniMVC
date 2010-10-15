<?php
class MiniMVC_Form_Validator_Callback extends MiniMVC_Form_Validator
{
	public function validate($value)
	{
        $callback = $this->getOption('callback');
        if (is_callable($callback))
        {
            return call_user_func($callback, $this, $value);
        }

		return false;
	}
}