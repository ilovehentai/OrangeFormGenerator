<?php

namespace FormGenerator\Validation;

class OneOrOtherValidation extends BaseIfValidation{
    
    public function isValid($value) {
        
        if(!empty($value)){
            return true;
        }else if(preg_match($this->_mExpression, $value)){
            return true;
        }
        
        return false;
        
    }
    
}