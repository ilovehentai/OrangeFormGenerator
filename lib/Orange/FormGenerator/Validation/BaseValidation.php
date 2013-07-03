<?php

namespace FormGenerator\Validation;

class BaseValidation implements ValidationInterface{
    
    protected $_mFormID;
    protected $_mTitleMsg;
    protected $_mErrorMsg;
    
    protected $_mExpression;
    protected $_mErrorMessage;

    
    public function __construct($config)
    {
        $this->_mErrorMessage = $config['message'];
        $this->_mErrorMsg = $config['message'];
    }

    public function isValid($value) {
        if(!empty($value) && preg_match($this->_mExpression, $value))
        {   
            return true;
        }
        return false;
        
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
    
    public function get_mErrorMessage() {
        return $this->_mErrorMessage;
    }
}
