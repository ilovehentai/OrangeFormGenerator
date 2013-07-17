<?php

namespace FormGenerator\Validation;

class MinSizeValidation extends BaseValidation{
    
    private $_mRulename;
            
    public function __construct($config) {
        parent::__construct($config);
        
        list($this->_mRulename, $size) = explode(":", $config['rule']);
        $size = (!is_integer($size)) ? 0 : $size;
        $this->_mExpression = "(.){" . $size . ",}";
    }
    
}
