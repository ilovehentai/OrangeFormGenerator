<?php

namespace FormGenerator\Validation;

class RegExpressionValidation extends BaseValidation{
    protected $_mExpression;
    protected $_mErrorMessage;
    
    public function __construct($expression) {
        parent::__construct();
        $this->_mExpression = $expression;
    }
    
}