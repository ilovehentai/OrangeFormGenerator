<?php

namespace FormGenerator\FormElements;

final class PushbuttonElement extends BaseElement{
    
     public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<button%s>-val-</button>";
    }
    
    public function build() {
        
        if(!array_key_exists("text", $this->_mElementData))
        {
            $this->_mElementData['text'] = $this->_mAttributes['value'];
        }
        
        $push_button = parent::build();
        
        parent::setValue();
        $push_button = str_replace("-val-", $this->_mElementData['text'], $push_button);
        
        return $push_button;
    }
}