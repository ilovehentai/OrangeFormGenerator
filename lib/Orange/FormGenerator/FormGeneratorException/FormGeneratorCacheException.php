<?php

namespace FormGenerator\FormGeneratorException;

class FormGeneratorCacheException extends \ErrorException {
    
    public function __construct ($message, $code, $severity, $filename, $lineno, $previous)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
    
}
