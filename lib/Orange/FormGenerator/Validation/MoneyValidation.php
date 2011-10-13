<?php

namespace FormGenerator\Validation;

class MoneyValidation extends BaseValidation{
    protected $_mExpression;
    protected $_mErrorMessage;
    
    public function __construct() {
        parent::__construct();
        $this->_mExpression = "/^[\d]+((\.|,)[\d]{2}){0,1}$/";
    }
    
}
