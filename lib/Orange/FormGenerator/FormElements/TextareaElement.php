<?php

namespace FormGenerator\FormElements;

final class TextareaElement extends BaseElement{
    
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<textarea%s>-val-</textarea>";
    }
    
    public function build() {
        
        if(!array_key_exists("id", $this->_mAttributes))
        {
            $this->_mAttributes['id'] = parent::get_mId();
        }
        
        $textarea = parent::build();
        
        parent::setValue();
        $textarea = str_replace("-val-", $this->_mElementData['text'], $textarea);
        
        return $textarea;
    }
    
    public function fillElement($value) {
        $this->_mElementData['text'] = $value;
    }
}