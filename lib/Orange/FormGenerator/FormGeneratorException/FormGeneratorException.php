<?php

namespace FormGenerator\FormGeneratorException;

class FormGeneratorException extends \ErrorException {
    
    public function __construct ($message, $code, $severity, $filename, $lineno, $previous)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
    
}
