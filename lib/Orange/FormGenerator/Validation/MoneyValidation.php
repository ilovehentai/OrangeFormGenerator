<?php

namespace FormGenerator\Validation;

class MoneyValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/^[\d]+((\.|,)[\d]{2}){0,1}$/";
    }
    
}
