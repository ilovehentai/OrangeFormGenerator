<?php

namespace FormGenerator\Validation;

class EmailValidation extends BaseValidation{    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4}\.)?[a-zA-Z]{2,4}$/";
    }
    
}
