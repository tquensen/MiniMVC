<?php
/**
 * @property {name} $model
 */
class {name}Form extends MiniMVC_Form
{
    /**
     * @var MiniMVC_Translation
     */
    protected $i18n = null;
    protected $name = '{name}Form';
    
    public function __construct($options, $model = null)
    {
        $this->i18n = MiniMVC_Registry::getInstance()->helper->i18n->get('{module}');
        
        if (!$model) {
            $model = new {name}();
        }
        $this->model = $model;
        
        $options = array_merge(array(
            'class' => 'fancyForm'
        ),(array)$options);
        
        parent::__construct($options);
        
        $this->setup();
    }
        
    public function setup()
    {
        {columns_form}

        $this->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $this->model->isNew() ? $this->i18n->{namelcfirst}FormSubmitCreateLabel : $this->i18n->{namelcfirst}FormSubmitUpdateLabel)));
    }

}
