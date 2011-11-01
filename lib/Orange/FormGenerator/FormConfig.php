<?php

namespace FormGenerator;

class FormConfig {
    
    const OFG_VALIDATION_CONFIG_FILE = 'validators.yml';
    const OFG_CONFIG_FILE = 'defaultform.yml';
    const OFG_CONFIG_DIR = 'Configs';
    const OFG_DEFAULT_CACHE_DIR = 'cache';
    
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
}

