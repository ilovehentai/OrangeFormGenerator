<?php
namespace FormGenerator\FormGeneratorException;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormGeneratorTemplateException
 *
 * @author jose
 */
class FormGeneratorTemplateException extends \ErrorException{
    
    public function __construct ($message, $code, $severity, $filename, $lineno, $previous)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
    
}