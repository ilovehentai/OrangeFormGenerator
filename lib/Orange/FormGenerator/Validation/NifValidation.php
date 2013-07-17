<?php

namespace FormGenerator\Validation;

class NifValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/[0-9]{9}/";
    }
    
}
