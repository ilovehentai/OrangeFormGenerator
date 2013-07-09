<?php

namespace FormGenerator\Validation;

class EmptyIfNotValidation extends BaseIfValidation{
    
    public function isValid($value) {
                
        if(empty($value))
        {   
            return true;
            
        }else if(!empty($value) && preg_match($this->_mExpression, $value))
        {
            return true;
        }
        
        return false;
        
    }
    
}