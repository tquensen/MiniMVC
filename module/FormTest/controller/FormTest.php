<?php
class FormTest_FormTest_Controller extends MiniMVC_Controller
{
    public function indexAction()
    {
        $this->registry->helper->meta->setTitle($this->view->t->formTestTitle);
        $this->registry->helper->meta->setDescription($this->view->t->formTestMetaDescription);

        $form = $this->getForm();
        
        if ($form->wasSubmitted() && $form->validate()) {
            $this->view->success = true;
            $this->view->message = $this->view->t->formTestSuccessMessage;
        } else {
            $this->view->success = false;
        }

        $this->view->form = $form;
    }

    protected function getForm()
    {
        $options = array('name' => 'TestForm', 'class' => 'fancyForm');

        $form = new MiniMVC_Form($options);

        $form->setElement(new MiniMVC_Form_Element_Fieldset('fs',
                        array('legend' => $this->view->t->testFormFieldsetLegend, 'info' => $this->view->t->testFormFieldsetInfo)
               ));

        $form->setElement(new MiniMVC_Form_Element_Text('title',
                        array('attributes' => array('placeholder' => $this->view->t->testFormTitlePlaceholder), 'label' => $this->view->t->testFormTitleLabel, 'info' => $this->view->t->testFormTitleInfo),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormTitleRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Text('text',
                        array('wrapperClass' => 'small', 'defaultValue' => 'Default value', 'label' => $this->view->t->testFormTextLabel, 'info' => $this->view->t->testFormTextInfo),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormTextRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Password('password',
                        array('wrapperClass' => 'small', 'label' => $this->view->t->testFormPasswordLabel, 'info' => $this->view->t->testFormPasswordInfo),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormPasswordRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Checkbox('checkbox',
                        array('wrapperClass' => 'small', 'label' => $this->view->t->testFormCheckboxLabel, 'info' => $this->view->t->testFormCheckboxInfo),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormCheckboxRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Select('select',
                        array(
                            'wrapperClass' => 'small', 'defaultValue' => '0',
                            'label' => $this->view->t->testFormSelectLabel, 'info' => $this->view->t->testFormSelectInfo,
                            'options' => array(0 => 'select...', 'elem1' => $this->view->t->testFormSelectElem1Label, 'elem2' => $this->view->t->testFormSelectElem2Label, 'elem3' => $this->view->t->testFormSelectElem3Label)
                            ),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormSelectRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_CheckboxGroup('checkboxGroup',
                        array(
                            'wrapperClass' => 'small',
                            'defaultValue' => array('elem2', 'elem3'),
                            'label' => $this->view->t->testFormCheckboxGropupLabel, 'info' => $this->view->t->testFormCheckboxGroupInfo,
                            'elements' => array('elem1' => $this->view->t->testFormCheckboxGroupElem1Label, 'elem2' => $this->view->t->testFormCheckboxGroupElem2Label, 'elem3' => $this->view->t->testFormCheckboxGroupElem3Label)
                            ),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormCheckboxGroupRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_SelectMultiple('selectMultiple',
                        array(
                            'wrapperClass' => 'small',
                            'attributes' => array('size' => 3),
                            'defaultValue' => array('elem1', 'elem3'),
                            'label' => $this->view->t->testFormSelectMultipleLabel, 'info' => $this->view->t->testFormSelectMultipleInfo,
                            'options' => array('elem1' => $this->view->t->testFormSelectMultipleElem1Label, 'elem2' => $this->view->t->testFormSelectMultipleElem2Label, 'elem3' => $this->view->t->testFormSelectMultipleElem3Label)
                            ),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormSelectMultipleRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Textarea('textarea',
                        array('attributes' => array('rows' => 3, 'cols' => 30),'defaultValue' => 'Default value', 'label' => $this->view->t->testFormTextareaLabel, 'info' => $this->view->t->testFormTextareaInfo),
                        array(
                            new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->testFormTexareatRequiredError))
                )));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('wrapperClass' => 'right small', 'label' => $this->view->t->testFormSubmitLabel)));
        $form->setElement(new MiniMVC_Form_Element_Button('button', array('wrapperClass' => 'left small', 'type' => 'submit', 'label' => $this->view->t->testFormButtonLabel)));

        $form->setElement(new MiniMVC_Form_Element_Fieldsetend('fsend'));
        $form->bindValues();

        return $form;
    }
}
