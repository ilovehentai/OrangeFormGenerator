<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormGenerator\FormGenerator;

use FormGenerator\FormGenerator;
use FormGenerator\FormGeneratorSimpleTemplateEngine\TemplateEngineFactory;
use FormGenerator\Validation\ValidationConfigClass;

/**
 * Description of FormGeneratorHTMLBuilder
 *
 * @author Joana
 */
class FormGeneratorHTMLBuilder {
    
    public static function buildFormHTML(FormGenerator $formInstance)
    {
        /* @var $templateAdapter Patterns\IFormTemplateAdapter */
        $templateAdapter = TemplateEngineFactory::getTemplateInstance();
        
        $templateAdapter->setTemplatePath($formInstance->getFormConfigLoader()->getTemplateDirectoryPath()
                                                        . $formInstance->getTemplateFileName());
        
        $templateAdapter->setFormElements($formInstance->getFormElement(), 
                                            $formInstance->getFormElementList(), 
                                            $formInstance->getFormFieldsetList());
        if($formInstance->isRenderJS() === true) {
            $templateAdapter->addJavaScript(static::buildJavaScript($formInstance));
        }
        
        return $templateAdapter->render();
    }
    
    /**
     * Create Javascript JSON for validation and place it in JS template
     * @return string|null 
     */
    protected static function buildJavaScript(FormGenerator $formInstance)
    {
        $js_validation_data = ValidationConfigClass::getInstance()->get_mConfig_data();
        if(is_file(__DIR__ . $js_validation_data["js_template"]))
        {
            if(!empty($formInstance->getFormValidatorsList()))
            {
                $jsFields = implode(",\n", static::buildFieldOptionsForJS($formInstance));
                
                $content = file_get_contents(__DIR__ . $js_validation_data["js_template"]);
                
                $form_configs = $formInstance->getFormConfigLoader()->getFormElementConfigs();
                return sprintf($content, $jsFields, $js_validation_data["title_msg"], 
                                        $js_validation_data["error_msg"], $form_configs["id"]);
            }else{
                
                return null;
            }
        }
    }
    
    /**
     * Build the JavaScript Field Option Array for the plugin
     * @param \FormGenerator\FormGenerator $formInstance
     * @return string
     */
    protected static function buildFieldOptionsForJS(FormGenerator $formInstance)
    {
        $jsFields = array();
        foreach($formInstance->getFormValidatorsList() as $field_id => $validatores)
        {
            $js_string_option = '"' . $field_id . '": [';
            $js_validatores = array();
            foreach($validatores as $fields) {
                $js_validatores[] = '{"validator" : "' . 
                                            $fields['rule'] . '", "msg" : "' . 
                                            $formInstance->getFormTranslator()->getTranslation($fields['message']) . 
                                    '"}';
            }
            $js_string_option .= implode(",", $js_validatores);
            $js_string_option .= ']';
            $jsFields[] = $js_string_option;
        }
        
        return $jsFields;
    }
}
