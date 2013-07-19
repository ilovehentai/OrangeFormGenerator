<?php

namespace FormGenerator\FormElements;

final class SubmitElement extends InputElement{
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mAttributes['type'] = "submit";
    }
    
    public function build() {
        
        if(isset($this->_mAttributes['value'])) {
            $this->_mAttributes['value'] = $this->translateAttribute($this->_mAttributes['value']);
        }
        
        return parent::build();
    }
}
