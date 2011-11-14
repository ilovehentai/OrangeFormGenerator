<?php

namespace FormGenerator\Validation;

class RegExpressionValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = $config['rule'];
    }
    
}