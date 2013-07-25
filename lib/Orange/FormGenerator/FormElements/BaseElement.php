<?php

namespace FormGenerator\FormElements;

use FormGenerator\FormGenerator;
use FormGenerator\Validation\ValidationFactory;
use FormGenerator\Validation\ValidationConfigClass;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormCollection\Collection;

abstract class BaseElement extends TranslatableElement implements InterfaceElement, ElementObservable{
    
    protected $_mAttributes = null;
    protected $_mSkeleton;
    protected $_mId;
    protected $_mElementData;
    protected $_mValidations;
    protected $_mObservers;
    protected $_mName;
    protected $_mErrors;
    protected $_mCheckName = true;
    protected $_mlabel;
    protected $_mValue;
    
    /**
     * Create a BaseElemet Object, recieve a configuration array containing 
     * an id, somes attributes
     * @param array $config 
     * @return BaseElement
     */
    public function __construct(array $config = array())
    {
        if(array_key_exists("attributes", $config) && is_array($config['attributes']))
        {
            $this->_mAttributes = $config['attributes'];
            if(isset($this->_mAttributes['id']))
            {
                $this->_mId = $this->_mAttributes['id'];
            }
        }
        
        $this->_mElementData = $config;
        $this->_mErrors = new Collection();
        $this->_mValidations = new Collection();
        $this->_mObservers = new Collection();
    }
    
    /**
     * Add validations to the validation list from the configuration data passed to the constructor
     * A base Element can recieve multiple validations objects
     * @return void
     */
    public function setValidations()
    {
        if(array_key_exists("validation", $this->_mElementData) && is_array($this->_mElementData['validation']))
        {
            $this->addValidations($this->_mElementData['validation']);
        }
    }
    
    /**
     * Create and add a validation object to validation list
     * @param array $validation_info 
     * @return
     */
    public function addValidations(array $validation_info)
    {
        if(!empty($validation_info))
        {
            foreach($validation_info as $vinfo)
            {
                $class_info = ValidationConfigClass::getInstance()->getValidationClass($vinfo['rule']);
                if(is_array($class_info) && array_key_exists("class", $class_info))
                {
                    $vinfo = array_merge($vinfo, $class_info);
                    $validator = ValidationFactory::creatElement($vinfo);
                    /* @var $validator BaseValidation */
                    $validator->setTranslator($this->getTranslator());
                    $this->_mValidations->add($validator);
                    $this->notify($vinfo);
                }
            }
        }
    }
    
    public function checkAttributeName()
    {
        
        if(!array_key_exists("name", $this->_mAttributes) && 
                is_array($this->_mElementData) && array_key_exists("name", $this->_mElementData))
        {
            $this->_mAttributes['name'] = $this->_mElementData['name'];
        }
        else if(!array_key_exists("name", $this->_mAttributes))
        {
            $this->_mAttributes['name'] = $this->_mId;
        }
        
        if(array_key_exists("name", $this->_mAttributes))
        {
            $this->set_mName($this->_mAttributes['name']);
        }
        
    }
    
    public function buildAttributes()
    {
        
        $str_attr = "";
        if($this->_mCheckName === true)
        {
            $this->checkAttributeName();
        }
        
        if(!empty($this->_mAttributes))
        {
            foreach($this->_mAttributes as $attr => $value)
            {
                $str_attr .= " " . $attr . "=\"" . $value . "\"";
            }
        }
        
        return $str_attr;
    }
    
    public function build(){
        $attr = "";
        
        if(is_null($this->_mAttributes))
        {
            $this->_mAttributes = array();
        }
        
        $attr = $this->buildAttributes();
        $this->_mSkeleton = sprintf($this->_mSkeleton, $attr);
        return $this->_mSkeleton;
    }
    
    public function addAttribute($attribute, $value)
    {
        if(!empty($attribute) && !empty($value))
        {
            $this->_mAttributes[$attribute] = $value;
        }
    }
    
    public function getAttribute($attribute)
    {
        if(isset($this->_mAttributes[$attribute]))
        {
            return $this->_mAttributes[$attribute];
        }
        return false;
    }
    
    protected function setValue()
    {
        if(!array_key_exists("text", $this->_mElementData) || empty($this->_mElementData['text']))
        {
             $this->_mElementData['text'] = "";
        }
        else if($this->_mElementData['text'][0] == "\$")
        {
           //Look in the default values list
           $index = substr($this->_mElementData['text'], 1);
           $this->_mElementData['text'] = FormGenerator::get_mElementDefaultValues($index);
        }
    }
    
    public function fillElement($value){
        $this->addAttribute("value", $value);
    }
    
    public function isValid(FormGenerator $form = null)
    {
        if(!$this->_mValidations->isEmpty())
        {
            foreach($this->_mValidations as /* @var $validation BaseValidation */ $validation)
            {
                if($validation->hasMatch()) {
                    $matcher = $validation->getMatch_Element();
                    $value = $form->getElementValue($matcher);
                    $validation->setMatch_value($value);
                }
                
                if(!$validation->isValid($this->_mValue))
                {
                    $this->_mErrors->add($validation->get_mErrorMessage());
                }
            }
        }
        
        return (!$this->_mErrors->isEmpty()) ? false : true;
    }
    
    public function addObserver(FormGenerator $observer) {
        $this->_mObservers->add($observer);
    }
    
    public function notify(array $info = array()) {
        if(!$this->_mObservers->isEmpty()){
            foreach($this->_mObservers  as $observers)
            {
                /* @var $observers FormGenerator */
                $observers->update($this, $info);
            }
        }
    }
    
    public function get_mId() {
        return $this->_mId;
    }

    public function set_mId($_mId) {
        $this->_mId = $_mId;
    }
    
    public function get_mName() {
        return $this->_mName;
    }

    public function set_mName($_mName) {
        $this->_mName = $_mName;
    }
    
    /**
     * Return a Collection of errors found after validation of the input
     * data or empty in case of successful validation.
     * @return Collection
     */
    public function get_mErrors() {
        return $this->_mErrors;
    }
    
    public function get_mlabel() {
        return $this->_mlabel;
    }

    public function set_mlabel(LabelElement $_mlabel) {
        $this->_mlabel = $_mlabel;
    }
    
    public function get_mValue() {
        return $this->_mValue;
    }

    public function set_mValue($_mValue) {
        $this->_mValue = $_mValue;
    }
    
}