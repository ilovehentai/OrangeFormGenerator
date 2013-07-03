<?php

namespace FormGenerator\Validation;

class RegExpressionValidation extends BaseValidation{
    
    private $_mRulename;
    
    public function __construct($config) {
        parent::__construct($config);
        list($this->_mRulename, $this->_mExpression) = explode(":", $config['rule']);
    }
    
}