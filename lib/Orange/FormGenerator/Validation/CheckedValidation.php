<?php

namespace FormGenerator\Validation;

class CheckedValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = null;
    }
    
}
