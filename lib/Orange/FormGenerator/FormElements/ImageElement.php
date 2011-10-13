<?php

namespace FormGenerator\FormElements;

final class ImageElement extends InputElement{
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mAttributes['type'] = "image";
    }
    
    public function build() {
        if(array_key_exists("src", $this->_mElementData))
        {
            $this->_mAttributes['src'] = $this->_mElementData['src'];
        }
        return parent::build();
    }
}
