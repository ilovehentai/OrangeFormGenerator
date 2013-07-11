<?php

namespace FormGenerator\FormGeneratorException;

class FormGeneratorException extends \ErrorException {
    
    public function __construct ($message, $code=0, $severity=0, $filename="", $lineno=0, $previous = NULL)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
    
}
