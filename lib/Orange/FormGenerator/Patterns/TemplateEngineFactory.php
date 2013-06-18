<?php
namespace FormGenerator\Patterns;
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
        $class_path = \FormGenerator\FormConfig::getDefaultTemplateEngine();
        if(isset($config['template_engine'])){
            $class_path = $config['template_engine'];
        }
        
        return new $class_path();
    }
}
