<?php

class MiniMVC_Form
{
    protected $elements = array();
    protected $name = null;
    protected $isValid = true;
    protected $errors = array();
    protected $options = array();
    protected $model = null;
    protected $postValidators = array();

    public function __construct($options = array())
    {
        $this->name = (isset($options['name'])) ? $options['name'] : $this->name;
        $this->model = (isset($options['model'])) ? $options['model'] : $this->model;

        $i18n = MiniMVC_Registry::getInstance()->helper->i18n->get('_form');

        $this->options['route'] = MiniMVC_Registry::getInstance()->settings->get('currentRoute');
        $this->options['parameter'] = isset($options['route']) ? array() : MiniMVC_Registry::getInstance()->settings->get('currentRouteParameter');
        $this->options['method'] = 'POST';
        $this->options['showGlobalErrors'] = true;
        $this->options['csrfProtection'] = true;
        $this->options['ajaxValidation'] = true;
        $this->options['csrfErrorMessage'] = $i18n->errorCsrf;
        $this->options['requiredMark'] = $i18n->requiredMark;
        $this->options = array_merge($this->options, (array)$options);

        $this->options['action'] = MiniMVC_Registry::getInstance()->helper->url->get($this->options['route'], !empty($this->options['parameter']) ? $this->options['parameter'] : array());
        

        $this->setElement(new MiniMVC_Form_Element_Hidden('FormCheck', array('defaultValue' => 1, 'alwaysDisplayDefault' => true), array(new MiniMVC_Form_Validator_Required())));


        if ($this->getOption('csrfProtection')) {
            $this->generateCsrfToken();
            

//            $oldCsrfToken = (isset($_SESSION['Form_' . $this->name . '_CsrfToken']))
//                        ? $_SESSION['Form_' . $this->name . '_CsrfToken'] : null;
//            $this->setElement(
//                    new MiniMVC_Form_Element_Hidden(
//                            '_csrf_token',
//                            array('defaultValue' => $csrfToken, 'alwaysDisplayDefault' => true, 'errorMessage' => $t->errorCsrf, 'globalErrors' => true, 'forceName' => '_csrf_token')
//                    )
//            );
//            $_SESSION['Form_' . $this->name . '_CsrfToken'] = $csrfToken; //for this form
             //for the dispatcher csrf check
        }
    }

    public function generateCsrfToken()
    {
        $this->csrfToken = MiniMVC_Registry::getInstance()->guard->generateCsrfToken($this->options['route']); 
    }

    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    public function checkCsrfToken()
    {
        if (!MiniMVC_Registry::getInstance()->guard->checkCsrfProtection(false)) {
            $this->setError($this->getOption('csrfErrorMessage'));
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
        foreach ($this->elements as $element) {
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
        if (!is_object($element)) {
            return false;
        }
        $this->elements[$element->getName()] = $element;
        $element->setForm($this);
        if ($this->getOption('forceGlobalErrors')) {
            $element->globalErrors = true;
        }
        return $this;
    }

    /**
     *
     * @param string $name the name of a form element
     * @return MiniMVC_Form_Element
     */
    public function getElement($name)
    {
        return (isset($this->elements[$name])) ? $this->elements[$name] : null;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function setPostValidator($validator)
    {
        if (!is_object($validator)) {
            return false;
        }
        $this->postValidators[] = $validator;
        $validator->setForm($this);
        return $this;
    }

    public function getPostValidators()
    {
        return $this->postValidators;
    }

    public function bindValues()
    {
        if (isset($_SESSION['form_' . $this->name . '__errorData'])) {
            $values = $_SESSION['form_' . $this->name . '__errorData'];
            
            if (!empty($values['_form'])) {
                $this->errors = $values['_form'];
                $this->isValid = false;
            }

            unset($_SESSION['form_' . $this->name . '__errorData']);
            foreach ($this->elements as $element) {
                $_POST[$this->name][$element->getName()] = isset($values[$element->getName()]['value']) ? $values[$element->getName()]['value']
                                    : null;
                if (!empty($values[$element->getName()]['hasError'])) {
                    $this->isValid = false;
                    $element->setError($values[$element->getName()]['errorMessage']);
                }
            }
        }
        $values = (isset($_POST[$this->name]) && is_array($_POST[$this->name])) ? $_POST[$this->name]
                    : array();
        foreach ($this->elements as $element) {
            $element->setValue(isset($values[$element->getName()]) ? $values[$element->getName()]
                                : null);
        }
        return true;
    }

    public function setError($errorMessage = null)
    {
        $this->isValid = false;
        if ($errorMessage) {
            $this->errors[] = $errorMessage;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return (bool) count($this->errors);
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function validate()
    {
//        $this->handleAjaxValidation();

        $this->bindValues();
        if (!$this->isValid || !$this->wasSubmitted()) {
            return false;
        }

        if ($this->getOption('csrfProtection')) {
            $this->checkCsrfToken();
        }
        
        foreach ($this->elements as $element) {
            if (!$element->validate()) {
                $this->isValid = false;
            }
        }

        foreach ($this->postValidators as $validator)
		{
			if (!$validator->validate($this->isValid))
			{
                $errorMessage = $validator->errorMessage;
				if ($errorMessage)
				{
					$this->setError($errorMessage);
				}
				$this->isValid = false;
			}
		}

//        if (!$this->isValid && $this->getOption('redirectOnError')) {
//            $this->errorRedirect();
//        }
        return $this->isValid;
    }

    public function errorRedirect($route, $parameter = array())
    {
        $url = MiniMVC_Registry::getInstance()->helper->url->get($route, $parameter);

        $sessionData = array();
        foreach ($this->elements as $element) {
            $sessionData[$element->getName()] = array(
                'hasError' => !$element->isValid(),
                'value' => $element->value,
                'errorMessage' => $element->errorMessage
            );
        }

        if ($this->getOption('ajaxValidation') && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'redirect' => $url, 'formErrors' => $this->errors, 'elements' => $sessionData));
            exit;
        }

        $sessionData['_form'] = $this->errors;
        
        $_SESSION['form_' . $this->name . '__errorData'] = $sessionData;

        $url = MiniMVC_Registry::getInstance()->helper->url->get($route, $parameter);

        header('Location: ' . $url);
        exit;
    }

    public function successRedirect($route, $parameter = array())
    {
        $url = MiniMVC_Registry::getInstance()->helper->url->get($route, $parameter);

        if ($this->getOption('ajaxValidation') && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(array('status' => true, 'redirect' => $url));
            exit;
        }

        header('Location: ' . $url);
        exit;
    }

    public function wasSubmitted()
    {
        return (isset($_POST[$this->name]) && is_array($_POST[$this->name]));
    }

//    public function handleAjaxValidation()
//    {
//        if (empty($_POST['_validateForm']) || $_POST['_validateForm'] != $this->name) {
//            return;
//        }
//
//        if (empty($_POST['_validateField']) || !isset($_POST['_validateValue'])) {
//            $t = MiniMVC_Registry::getInstance()->helper->i18n->get('_form');
//            $response = array('status' => false, 'message' => $t->errorAjaxMissingField);
//            exit;
//        } elseif (!$field = $this->getElement($_POST['_validateField'])) {
//            $t = MiniMVC_Registry::getInstance()->helper->i18n->get('_form');
//            $response = array('status' => false, 'message' => $t->errorAjaxInvalidField('field=' . htmlspecialchars($_POST['_validateField'])));
//        } else {
//            $field->setValue($_POST['_validateValue']);
//            $response = $field->validate() ? array('status' => true) : array('status' => false, 'message' => $field->errorMessage);
//        }
//
//        header('Content-Type: application/json');
//        echo json_encode($response);
//        exit;
//    }

}