<?php

namespace FormGenerator\Validation;

class PhoneValidation extends BaseValidation{
    protected $_mExpression;
    protected $_mErrorMessage;
    
    public function __construct() {
        parent::__construct();
        $this->_mExpression = "/^([(][0-9-+ ]+[)]|[0-9-+ ]*)?[0-9 ]{9,}([(][a-zA-Z0-9. ]+[)]|[a-zA-Z0-9. ]+)?$/";
    }
    
}
