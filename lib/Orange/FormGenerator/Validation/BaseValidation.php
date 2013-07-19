<?php

namespace FormGenerator\Validation;

class BaseValidation extends TranslatableValidation implements ValidationInterface{
    
    protected $_mFormID;
    protected $_mTitleMsg;
    protected $_hasMatch = false;
    
    protected $_mExpression;
    protected $_mErrorMessage;

    
    public function __construct($config)
    {
        $this->_mErrorMessage = $config['message'];
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
        $this->translate();
        return $this->_mErrorMessage;
    }
    
    public function hasMatch() {
        return $this->_hasMatch;
    }

    public function set_hasMatch($_hasMatch) {
        $this->_hasMatch = $_hasMatch;
    }
}
