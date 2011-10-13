<?php

namespace FormGenerator\FormElements;
use FormGenerator\FormGenerator;

abstract class InputElement extends BaseElement{
    
    protected $_mSkeleton;
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<input%s/>";
    }
    
    protected function setValue()
    {
        
        if(!array_key_exists("value", $this->_mAttributes))
        {
             $this->_mAttributes['value'] = "";
        }
        else if($this->_mAttributes['value'][0] == "\$")
        {
           //Look in the default values list
           $index = substr($this->_mAttributes['value'], 1);
           $this->_mAttributes['value'] = FormGenerator::get_mElementDefaultValues($index);
        }
    }
    
    public function build() {
        
        $this->_mAttributes['id'] = parent::get_mId();
        $this->setValue();
        return parent::build();
    }
}
