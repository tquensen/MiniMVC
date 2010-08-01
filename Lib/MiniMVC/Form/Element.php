<?php
class MiniMVC_Form_Element
{
	protected $name = false;
	protected $options = false;
	protected $validators = false;
	protected $form = false;
	protected $isValid = true;
	protected $type = false;

	public function __construct($name = false, $options = array(), $validators = array())
	{
		$this->name = $name;
		$this->options = (array) $options;
		$this->validators = (is_array($validators)) ? $validators : array($validators);
	}

	public function setForm($form)
	{
		$this->form = $form;
	}

	public function getForm()
	{
		return $this->form;
	}

	public function getType()
	{
		return $this->type;
	}

	public function addValidator($validators)
	{
		if (!is_array($validators))
		{
			$validators = array($validators);
		}
		$this->validators = array_merge($this->validators, $validators);
	}

    /**
     *
     * @param <type> $option
     * @return <type>
     */
	public function getOption($option)
	{
		return (isset($this->options[$option])) ? $this->options[$option] : null;
	}

	public function setOption($option, $value)
	{
		$this->options[$option] = $value;
	}

	public function __get($option)
	{
		return $this->getOption($option);
	}

	public function __set($option, $value)
	{
		$this->setOption($option, $value);
	}

	public function getName()
	{
		return $this->name;
	}

	public function setValue($value)
	{
		if ($this->alwaysUseDefault == true)
		{
			$this->value = $this->defaultValue;
			return;
		}
		if ($this->form->wasSubmitted())
		{
            $this->value = $value;
        }
        else
        {
            $this->value = $this->defaultValue;
        }
	}

	public function validate()
	{
		foreach ($this->validators as $validator)
		{
			if (!$validator->validate($this, $this->value))
			{
				$errorMessage = $validator->errorMessage;
				if ($errorMessage)
				{
					$this->errorMessage = $errorMessage;
				}
				$this->isValid = false;
				break;
			}
		}

		return $this->isValid;
	}

	public function setError($errorMessage, $error = true)
	{
		$this->errorMessage = $errorMessage;
		$this->isValid = !$error;
	}

	public function isValid()
	{
		return $this->isValid;
	}

	public function wasSubmitted()
	{
		return ($this->form) ? $this->form->wasSubmitted() : false;
	}

}