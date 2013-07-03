<?php

namespace FormGenerator\Validation;

class DateValidation extends BaseValidation{
    
    private $_mYear;
    private $_mMonth;
    private $_mDay;
    
    public function __construct($config) {
        parent::__construct($config);
    }
    
    public function isValid($value) {
        list($this->_mYear, $this->_mMonth, $this->_mDay) = explode("-", $value);
        if(checkdate($this->_mMonth, $this->_mDay, $this->_mYear))
        {   
            return true;
        }
        return false;
    }
    
}