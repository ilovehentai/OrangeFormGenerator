<?php

namespace FormGenerator\Validation;

class ZipCodeValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/([1-9]{1}[0-9]{3}-[0-9]{3}|[1-9]{1}[0-9]{3})/";
    }
    
}
