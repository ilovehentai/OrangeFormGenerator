<?php

namespace FormGenerator\Validation;

use FormGenerator\FormParser\ParserFactory;
use \FormGenerator\FormGeneratorException\FormGeneratorException;

class ValidationConfigClass{
    
    private $_mConfig_data;
    public static $_instance;
    private $_mValidationConfigFile;

    public function __construct($file = "")
    {
        if(!empty($file))
        {
            $this->loadValidationConfigFile($file);
        }
    }
    
    public function loadValidationConfigFile($file)
    {
        if(is_file($file))
        {
            $file_type = pathinfo($file, PATHINFO_EXTENSION);
            $parser = ParserFactory::getParserInstance($file_type);
            /* @var $parser FormGenerator\FormParser\IFormParser */
            $this->_mConfig_data = $parser::parse($file);
            $this->_mValidationConfigFile = $file;
        }
        else
        {
            throw new FormGeneratorException("Validation file not found: " . $file);
        }
    }
    
    public function getValidationClass($rule)
    {
        if(strstr($rule, ":"))
        {
            $rule = substr($rule, 0, strlen($rule) - (strlen($rule) - strpos($rule, ":")));
        }
        
        if(array_key_exists($rule, $this->_mConfig_data['rules']))
        {
            return $this->_mConfig_data['rules'][$rule];
        }
        else
        {
            return null;
        }
        
    }
    
    public function get_mConfig_data() {
        return $this->_mConfig_data;
    }

    public function set_mConfig_data($_mConfig_data) {
        $this->_mConfig_data = $_mConfig_data;
    }
    
    public function get_mValidationConfigFile() {
        return $this->_mValidationConfigFile;
    }

    public function set_mValidationConfigFile($_mValidationConfigFile) {
        $this->_mValidationConfigFile = $_mValidationConfigFile;
    }
        
    /**
     *
     * @return ValidationConfigClass 
     */
    public static function getInstance()
    {
        return (!isset(self::$_instance)) ? self::$_instance = new ValidationConfigClass(): self::$_instance ;
    }
}
