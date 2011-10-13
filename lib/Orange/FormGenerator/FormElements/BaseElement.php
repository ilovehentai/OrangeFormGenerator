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
        
        if(is_array($this->_mElementData) && array_key_exists("name", $this->_mElementData))
        {
            $this->_mAttributes['name'] = $this->_mElementData['name'];
        }
    }
    
    public function setValidations()
    {
        if(array_key_exists("validation", $this->_mElementData) && is_array($this->_mElementData['validation']))
        {
            $this->addValidations($this->_mElementData['validation']);
        }
    }
    
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
                    $_mValidations[] = ValidationFactory::creatElement($vinfo);
                    $this->notify($vinfo);
                }
            }
        }
    }
    
    public function buildAttributes(array $attributes)
    {
        
        $str_attr = "";
        if(!empty($attributes))
        {
            foreach($attributes as $attr => $value)
            {
                $str_attr .= " " . $attr . "=\"" . $value . "\"";
            }
        }
        
        return $str_attr;
    }
    
    public function build(){
        $attr = "";
        if(!is_null($this->_mAttributes))
        {
            $attr = $this->buildAttributes($this->_mAttributes);
        }
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
    
    public function addObserver(FormGenerator $observer) {
        $this->_mObservers[] = $observer;
    }
    
    public function notify(array $info = array()) {
        if(!empty($this->_mObservers)){
            foreach($this->_mObservers  as $observers)
            {
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
    
}
?>
