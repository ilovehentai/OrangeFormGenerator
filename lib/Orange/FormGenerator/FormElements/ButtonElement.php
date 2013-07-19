<?php

namespace FormGenerator\FormElements;

final class ButtonElement extends InputElement{
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mAttributes['type'] = "button";
    }
    
    public function build() {
        
        if(isset($this->_mAttributes['value'])) {
            $this->_mAttributes['value'] = $this->translateAttribute($this->_mAttributes['value']);
        }
        
        return parent::build();
    }
}
