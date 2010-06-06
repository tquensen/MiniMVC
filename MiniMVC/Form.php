<?php
class MiniMVC_Form
{
	protected $elements = array();
	protected $name = false;
	protected $record = false;
	protected $isValid = true;
	protected $options = array();
	protected $module = false;

	public function __construct($record = false, $options = array())
	{
        $t = MiniMVC_Registry::getInstance()->helper->I18n->get('_form');
		$this->record = $record;
		$this->name = (isset($options['name'])) ? $options['name'] : null;

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
		return (isset($this->elements[$element])) ? $this->elements[$element] : false;
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

	public function &getRecord()
	{
		return $this->record;
	}

	public function updateRecord()
	{
		if ($this->record)
		{
			foreach ($this->elements as $element)
			{
				$element->updateRecord();
			}
			//$this->record->save();
            return $this->record;
		}
        return false;
	}

	public function addElement($element)
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

	public function setValues()
	{
		foreach ($this->elements as $element)
		{
			$element->setValue(isset($_POST[$this->name.'_'.$element->getName()]) ? $_POST[$this->name.'_'.$element->getName()] : null);
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
			if (!$element->validate($_POST[$this->name.'_'.$element->getName()]))
			{
				$this->isValid = false;
			}
		}
		return $this->isValid;
	}

	public function wasSubmitted()
	{
		return isset($_POST[$this->name.'_FormCheck']);
	}

}