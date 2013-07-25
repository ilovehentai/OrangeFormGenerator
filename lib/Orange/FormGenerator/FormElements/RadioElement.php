<?php

namespace FormGenerator\FormElements;

final class RadioElement extends InputElement{
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mAttributes['type'] = "radio";
    }
    
    public function fillElement($value) {
        if($value == $this->getAttribute("value")){
            $this->addAttribute("checked", "checked");
        }
    }
}