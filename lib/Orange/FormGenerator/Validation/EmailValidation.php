<?php

namespace FormGenerator\Validation;

class EmailValidation extends BaseValidation{
    protected $_mExpression;
    protected $_mErrorMessage;
    
    public function __construct() {
        parent::__construct();
        $this->_mExpression = "/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4}\.)?[a-zA-Z]{2,4}$/";
    }
    
}
