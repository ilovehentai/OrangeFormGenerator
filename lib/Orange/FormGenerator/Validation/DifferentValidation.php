<?php

namespace FormGenerator\Validation;

class DifferentValidation extends MatchValidation{
   
    public function isValid($value) {
        /* @var $_mValidation_matcher BaseElement */
        if(!empty($value) && $value !== $this->_match_value)
        {   
            return true;
        }
        return false;
        
    }
    
}