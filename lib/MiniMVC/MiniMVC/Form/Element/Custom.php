<?php
class MiniMVC_Form_Element_Custom extends MiniMVC_Form_Element
{
	protected $type = 'custom';

    public function setValue($value)
	{
        if ($this->getOption('setValueCallback') && is_callable($this->getOption('setValueCallback'))) {
            call_user_func($this->getOption('setValueCallback'), $value, $this);
        }
	}

	public function updateModel($model)
	{
        if ($this->getOption('useModel') !== false && $model = $this->getForm()->getModel()) {
            if ($this->getOption('updateModelCallback') && is_callable($this->getOption('updateModelCallback'))) {
                call_user_func($this->getOption('updateModelCallback'), $model, $this);
            }
        }
	}
}