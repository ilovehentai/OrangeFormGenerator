<?php

namespace FormGenerator\Validation;

class BaseValidation implements ValidationInterface{
    
    protected $_mFormID;
    protected $_mTitleMsg;
    protected $_mErrorMsg;
    
    protected $_mExpression;
    protected $_mErrorMessage;

    public function __construct()
    {
        
    }

    public function isValid($value) {
        
        if(!empty($value) && preg_match($this->_mExpression, $value))
        {
            return true;
        }
        else
        {
            return $this->_mErrorMessage;
        }
    }
    
    public function getExpression() {
        return $this->_mExpression;
    }
    
    public function get_mFormID() {
        return $this->_mFormID;
    }

    public function set_mFormID($_mFormID) {
        $this->_mFormID = $_mFormID;
    }
}
