<?php

namespace FormGenerator\Validation;

class NotEmptyValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/(.){1,}/";
    }
    
}
