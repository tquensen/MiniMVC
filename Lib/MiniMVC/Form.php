<?php
class MiniMVC_Form
{
	protected $elements = array();
	protected $name = null;
	protected $isValid = true;
	protected $options = array();

	public function __construct($options = array())
	{
		$this->name = (isset($options['name'])) ? $options['name'] : $this->name;

		$this->options['action'] = $_SERVER['REQUEST_URI'];
		$this->options['method'] = 'post';
        $this->options['csrfProtection'] = true;
		$this->options = array_merge($this->options, (array) $options);

		$this->addElement(new MiniMVC_Form_Element_Hidden('FormCheck', array('defaultValue' => 1, 'alwaysDisplayDefault' => true), array(new MiniMVC_Form_Validator_Required())));

        if ($this->getOption('csrfProtection')) {
            $oldCsrfToken = (isset($_SESSION['Form_'.$this->name.'_CsrfToken'])) ? $_SESSION['Form_'.$this->name.'_CsrfToken'] : null;
            $csrfToken = md5($this->name.time().rand(1000,9999));
            $this->addElement(new MiniMVC_Form_Element_Hidden('CsrfToken', array('defaultValue' => $csrfToken, 'alwaysDisplayDefault' => true, 'errorMessage' => $t->errorCsrf), array(new MiniMVC_Form_Validator_Required(), new MiniMVC_Form_Validator_Equals(array('value' => $oldCsrfToken)))));
            $_SESSION['Form_'.$this->name.'_CsrfToken'] = $csrfToken;
        }
	}

	public function getName()
	{
		return $this->name;
	}

    public function setName($name)
	{
		$this->name = $name;
	}

	public function __get($element)
	{
		return $this->getElement($element);
	}

    public function __set($element)
	{
		$this->setElement($element);
	}

	public function setOption($option, $value)
	{
		$this->options[$option] = $value;
		return true;
	}
	
	public function getOption($option)
	{
		return (isset($this->options[$option])) ? $this->options[$option] : null;
	}

	public function setElement($element)
	{
		if (!is_object($element))
		{
			return false;
		}
		$this->elements[$element->getName()] = $element;
		$element->setForm($this);
		return $this;
	}

	public function getElement($name)
	{
		return (isset($this->elements[$name])) ? $this->elements[$name] : null;
	}

	public function getElements()
	{
		return $this->elements;
	}

	public function bindValues($values)
	{
		foreach ($this->elements as $element)
		{
			$element->setValue(isset($values[$element->getName()]) ? $values[$element->getName()] : null);
		}
		return true;
	}

    public function setError($error = true)
	{
		$this->isValid = !$error;
	}

	public function validate()
	{
		if (!$this->wasSubmitted())
		{
			return false;
		}
		foreach ($this->elements as $element)
		{
			if (!$element->validate())
			{
				$this->isValid = false;
			}
		}
		return $this->isValid;
	}

	public function wasSubmitted()
	{
		return (bool) $this->FormCheck->value;
	}

}