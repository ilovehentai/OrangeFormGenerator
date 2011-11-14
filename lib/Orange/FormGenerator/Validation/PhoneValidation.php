<?php

namespace FormGenerator\Validation;

class PhoneValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "/^([(][0-9-+ ]+[)]|[0-9-+ ]*)?[0-9 ]{9,}([(][a-zA-Z0-9. ]+[)]|[a-zA-Z0-9. ]+)?$/";
    }
    
}
