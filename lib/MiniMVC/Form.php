<?php
class MiniMVC_Form
{
	protected $elements = array();
	protected $name = null;
	protected $isValid = true;
	protected $options = array();
    protected $model = null;

	public function __construct($options = array())
	{
		$this->name = (isset($options['name'])) ? $options['name'] : $this->name;
        $this->model = (isset($options['model'])) ? $options['model'] : $this->model;

		$this->options['action'] = $_SERVER['REQUEST_URI'];
		$this->options['method'] = 'POST';
        $this->options['csrfProtection'] = true;
		$this->options = array_merge($this->options, (array) $options);

		$this->setElement(new MiniMVC_Form_Element_Hidden('FormCheck', array('defaultValue' => 1, 'alwaysDisplayDefault' => true), array(new MiniMVC_Form_Validator_Required())));

        $t = MiniMVC_Registry::getInstance()->helper->i18n->get('_form');
        
        if ($this->getOption('csrfProtection')) {
            $oldCsrfToken = (isset($_SESSION['Form_'.$this->name.'_CsrfToken'])) ? $_SESSION['Form_'.$this->name.'_CsrfToken'] : null;
            $csrfToken = md5($this->name.time().rand(1000,9999));
            $this->setElement(new MiniMVC_Form_Element_Hidden('CsrfToken', array('defaultValue' => $csrfToken, 'alwaysDisplayDefault' => true, 'errorMessage' => $t->errorCsrf), array(new MiniMVC_Form_Validator_Required(), new MiniMVC_Form_Validator_Equals(array('value' => $oldCsrfToken)))));
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

    public function getModel()
	{
		return $this->model;
	}

    public function setModel($model)
	{
		$this->model = $model;
	}

    public function updateModel()
    {
        if (!is_object($this->model)) {
            return false;
        }
        foreach ($this->elements as $element)
		{
			$element->updateModel($this->model);
		}
        return $this->model;
    }

	public function __get($element)
	{
		return $this->getElement($element);
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

	public function bindValues()
	{
        $values = (isset($_POST[$this->name]) && is_array($_POST[$this->name])) ? $_POST[$this->name] : array();
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
        $this->bindValues();
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
		return (isset($_POST[$this->name]) && is_array($_POST[$this->name]));
	}

}