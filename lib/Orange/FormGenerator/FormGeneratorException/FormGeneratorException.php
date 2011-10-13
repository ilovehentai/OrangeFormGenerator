<?php

namespace FormGenerator\FormGeneratorException;

class FormGeneratorException extends \Exception {
    
    protected $file;
    protected $line;
    
    public function __construct($line, $file)
    {
        $this->line = $line;
        $this->file = $file;
    }
    
    public function getSpecificMessage()
    {
        return parent::getMessage() . " in file {$this->file} line {$this->line}";
    }
    
}
