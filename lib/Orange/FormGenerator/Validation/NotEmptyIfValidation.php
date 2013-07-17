<?php

namespace FormGenerator\Validation;

class NotEmptyIfValidation extends BaseIfValidation{
    
    public function isValid($value) {
        
        if(empty($value) || preg_match($this->_mExpression, $value))
        {   
            return true;
        }
        return false;
        
    }
    
}