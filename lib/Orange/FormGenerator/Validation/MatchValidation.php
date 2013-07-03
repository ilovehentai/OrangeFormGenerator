<?php

namespace FormGenerator\Validation;

class MatchValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        list($rule, $this->_mExpression) = explode(":", $config['rule']);
    }
    
    public function isValid($value) {
        if(!empty($value) && preg_match($this->_mExpression, $value))
        {   
            return true;
        }
        return false;
        
    }
    
}