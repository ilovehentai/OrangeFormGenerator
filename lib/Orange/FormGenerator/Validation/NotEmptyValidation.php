<?php

namespace FormGenerator\Validation;

class NotEmptyValidation extends BaseValidation{
    protected $_mExpression;
    protected $_mErrorMessage;
    
    public function __construct() {
        parent::__construct();
        $this->_mExpression = "/(.){1,}/";
    }
    
}
