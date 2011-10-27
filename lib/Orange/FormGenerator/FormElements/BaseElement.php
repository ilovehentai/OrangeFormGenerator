<?php

namespace FormGenerator\FormElements;

use FormGenerator\FormGenerator;
use FormGenerator\Patterns\ValidationFactory;
use FormGenerator\Patterns\ElementObservable;
use FormGenerator\Validation\ValidationConfigClass;

abstract class BaseElement implements InterfaceElement, ElementObservable{
    
    protected $_mAttributes = null;
    protected $_mSkeleton;
    protected $_mId;
    protected $_mElementData;
    protected $_mValidations;
    protected $_mObservers = array();
    protected $_mName;
    protected $_mErrors = array();
    protected $_mCheckName = true;
    
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
                    $this->_mValidations[] = ValidationFactory::creatElement($vinfo);
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
    
    protected function setValue()
    {
        if(!array_key_exists("text", $this->_mElementData))
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
    
    public function isValid($value)
    {
        if(!empty($this->_mValidations))
        {
            foreach($this->_mValidations as /* @var $validation BaseValidation */ $validation)
            {
                if(!$validation->isValid($value))
                {
                    $this->_mErrors[] = $validation->get_mErrorMessage();
                }
            }
        }
        return (!empty($this->_mErrors)) ? false : true;
    }
    
    public function addObserver(FormGenerator $observer) {
        $this->_mObservers[] = $observer;
    }
    
    public function notify(array $info = array()) {
        if(!empty($this->_mObservers)){
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
    
    public function get_mErrors() {
        return $this->_mErrors;
    }
    
}
?>
