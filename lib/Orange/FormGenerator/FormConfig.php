<?php

namespace FormGenerator;

class FormConfig {
    
    const OFG_VALIDATION_CONFIG_FILE = 'validators.yml';
    const OFG_CONFIG_FILE = 'example.yml';
    const OFG_CONFIG_DIR = 'Configs';
    const OFG_DEFAULT_CACHE_DIR = 'cache';
    const OFG_DEFAULT_TEMPLATE_ENGINE_ADAPTER = 'FormGenerator\\FormGeneratorSimpleTemplateEngine\\SimpleTemplateEngineAdapter';
    const OFG_DEFAULT_PARSER_ADAPTERS_PATH = 'FormGenerator\\FormParser\\';
    
    const OFG_ERROR_NO_SESSION = "Form %s not found in Session";
    const OFG_ERROR_INVALID_FORM_OBJECT = "Invalid Form Object for %s in Session";
    
    public static function getConfigDir(){
        return __DIR__ . DIRECTORY_SEPARATOR . self::OFG_CONFIG_DIR . DIRECTORY_SEPARATOR;
    }
    
    public static function getDefaultConfigFile(){
        return self::getConfigDir().self::OFG_CONFIG_FILE;
    }
    
    public static function getDefaultValidationFile(){
        return self::getConfigDir().self::OFG_VALIDATION_CONFIG_FILE;
    }
    
    public static function getDefaultCacheDir(){
        return __DIR__ . DIRECTORY_SEPARATOR . self::OFG_DEFAULT_CACHE_DIR . DIRECTORY_SEPARATOR;
    }
    
    public static function getTemplateEngine(){
        return self::OFG_DEFAULT_TEMPLATE_ENGINE_ADAPTER;
    }
    
    public static function getParserAdapterPath(){
        return self::OFG_DEFAULT_PARSER_ADAPTERS_PATH;
    }
    
    public static function errorMSGForFormInSession($formId){
        return sprintf(self::OFG_ERROR_NO_SESSION, $formId);
    }
    
    public static function errorMSGForFormObjectInSession($formId){
        return sprintf(self::OFG_ERROR_INVALID_FORM_OBJECT, $formId);
    }
}

