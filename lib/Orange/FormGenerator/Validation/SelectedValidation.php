<?php

namespace FormGenerator\Validation;

class SelectedValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/^[1-9]{1}([0-9])*$/";
    }
    
}
