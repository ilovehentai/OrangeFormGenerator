<?php

namespace FormGenerator\FormGenerator;

use FormGenerator\FormCollection\Collection;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\FormElements\LegendElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\ElementFactory;

/**
 * FormGeneratorComposer Composite Form Generator Class with all colectable elements
 *
 * @author José Santos
 */
class FormGeneratorComposer extends BaseFormGenerator {

    /**
     * List of BaseElement Objects
     * @var Collection 
     */
    protected $formElementList;

    /**
     * List of FieldsetElement Objects
     * @var Collection 
     */
    protected $formFieldsetList;

    /**
     * List of Validators used by BaseElements in this form
     * @var array 
     */
    protected $formValidatorsList = array();

    /**
     * Form submit Collection messages error returned by the validators
     * @var Collection
     */
    protected $formErrorList;

    /**
     * Collection of elements id to save after post. 
     * This allow to recover the post values of those items
     * @var Collection 
     */
    protected $idListToSave;

    /**
     * Error reported in form
     * @var string 
     */
    protected $errorMessage = "";

    /**
     * Create a FormGenerator Object, if the name of a configuration file is passed in the constructor,
     * it will check whether the file exists and set it as the configuration file
     * if not throws an exception
     * @param string $idform
     * @param array $args 
     * @return FormGenerator
     */
    public function __construct($idform, array $args = array()) {
        parent::__construct($idform, $args);
        $this->formElementList = new Collection();
        $this->formFieldsetList = new Collection();
        $this->formErrorList = new Collection();
        $this->idListToSave = new Collection();
    }

    /**
     * On serialization save only important data, form error,
     * form elements, list validators and readonly flag
     * @return array 
     */
    public function __sleep() {
        return array_merge(
                            array("formErrorList", "formElementList", "formValidatorsList",
                                    "idListToSave")
                            , parent::__sleep()
                            );
    }
    
    /**
     * Add a BaseElement Object to the BaseElement list, the element will add
     * the FormGenerator to is observers list and set is validations if they exists
     * if a LabelElement Object is passed to the method it will also be added
     * to the LabelElement list
     * 
     * @param BaseElement $element
     * @param LabelElement $label
     * @return void 
     */
    public function addElement(BaseElement $element, LabelElement $label = null)
    {
        $element->addObserver($this);
        $element->setValidations();
        
        if(is_a($label, "FormGenerator\FormElements\LabelElement"))
        {
            $element->set_mlabel($label);
        }
        
        $this->formElementList->add($element);
    }
    
    /**
     * Add a FieldsetElement Object to the FieldsetElement list
     * if a LegendElement Object is passed to the method it will also be added
     * to the LegendElement list
     * 
     * @param FieldsetElement $element
     * @param LegendElement $legend 
     * @return void
     */
    public function addFieldset(FieldsetElement $element, LegendElement $legend = null)
    {
        
        if(is_a($legend, "FormGenerator\FormElements\LegendElement"))
        {
            /** @var FieldsetElement $element **/
            $element->set_mLegend($legend);
        }
        
        $this->formFieldsetList->add($element);
    }
    
    /**
     * Add an error to the error list
     * @param Collection $errors
     */
    public function addElementErrors(array $errors) {
        $this->formErrorList->add($errors);
    }
    
    /**
     * Add a CSRF Token Element to the form
     * @return CsrfElement
     */
    public function addCsrfToken() {
        
        $csrf = ElementFactory::creatElement(array("type" => "CsrfToken", "id" => $this->formId . "_csrf_token"));
        $this->addElement($csrf);
        if($this->isHasCSRFToken()) {
            $csrf->saveCSRFToken($this->formId);
        }
        
        return $csrf;
    }
    
    /**
     * Clear the error list
     */
    public function clearFormErrors() {
        if(!$this->formErrorList->isEmpty()) {
            $this->formErrorList->clear();
        }
    }
    
    /**
     * Get all the form errors into a string
     * @param string $errors
     */
    public function errorsToString() {
        $this->errorMessage = "";
        if (!$this->formErrorList->isEmpty()) {
            foreach ($this->formErrorList as $errors) {
                $this->errorMessage .= implode("<br/>", $errors) . "<br/>";
            }
        }
        return $this->errorMessage;
    }

    /**
     * Get List of Elements
     * @return BaseElement[]
     */
    public function getFormElementList() {
        return $this->formElementList;
    }
    
    /**
     * Get List of Fieldset Elements
     * @return BaseElement[]
     */
    public function getFormFieldsetList() {
        return $this->formFieldsetList;
    }
    
    /**
     * Return the collection of items id to be saved after post
     * @return Collection
     */
    public function getIdListToSave() {
        return $this->idListToSave;
    }

    /**
     * Get List of Validators
     * @return type BaseValidation[]
     */
    public function getFormValidatorsList() {
        return $this->formValidatorsList;
    }

    /**
     * Set a collection of items id to be save after post
     * @param Collection $idListToSave
     */
    public function setIdListToSave(Collection $idListToSave) {
        $this->idListToSave = $idListToSave;
    }
    
    /**
     * Get The Form errors message after submit
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    
    /**
     * Set The Form errors message after submit
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
    }


}
