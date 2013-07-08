<?php

namespace FormGenerator\Validation;

class MatchValidation extends BaseValidation{
    
    protected $_match_value;
    protected $_hasMatch = true;
    
    public function __construct($config) {
        parent::__construct($config);
        list($rule, $this->_mExpression) = explode(":", $config['rule']);
    }
    
    public function isValid($value) {
        /* @var $_mValidation_matcher BaseElement */
        if(!empty($value) && $value === $this->_match_value)
        {   
            return true;
        }
        return false;
        
    }
    
    public function getMatch_Element() {
        return $this->_mExpression;
    }
    
    public function setMatch_value($value) {
        $this->_match_value = $value;
    }
    
}