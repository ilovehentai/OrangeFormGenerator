<?php

namespace FormGenerator\Validation;

/**
 * Description of BaseIfValidation
 *
 * @author josesantos
 */
class BaseIfValidation extends BaseValidation{
    
    private $_mCompare_validation_object;
    
    public function __construct($config) {
        parent::__construct($config);
        list($rule, $this->_mExpression) = explode(":", $config['rule']);
        
        $validation_config = ValidationConfigClass::getInstance();
        $validation_confing_data = $validation_config->get_mConfig_data();
        if(!empty($validation_confing_data) && array_key_exists("rules", $validation_confing_data)) {
            foreach($validation_confing_data["rules"] as $rule => $validation) {
                if($this->_mExpression == $rule) {
                    $validation["message"] = $this->get_mErrorMessage();
                    $this->_mCompare_validation_object = ValidationFactory::creatElement($validation);
                    $this->_mExpression = $this->_mCompare_validation_object->getExpression();
                    break;
                }
            }
        }
        
    }
    
}

?>
