<?php

namespace FormGenerator\Validation;

class SelectedValidation extends BaseValidation{
    protected $_mExpression;
    protected $_mErrorMessage;
    
    public function __construct() {
        parent::__construct();
        $this->_mExpression = "/^[1-9]{1}([0-9])*$/";
    }
    
}
