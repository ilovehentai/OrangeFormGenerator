<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateEngineFactory
 *
 * @author josesantos
 */
class TemplateEngineFactory {
    //put your code here
    public static function getTemplateInstance()
    {
        $class_path = \FormGenerator\FormGenerator\FormConfig::getTemplateEngine();
        return new $class_path();
    }
}
