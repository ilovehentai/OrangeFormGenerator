<?php

namespace FormGenerator\FormGeneratorTranslations;

/**
 * Description of FromTranslationFactory
 *
 * @author josesantos
 */
class FromTranslationFactory {
    
    public static function getFormTranslationInstance($locale = "")
    {
        $class_path = \FormGenerator\FormGenerator\FormConfig::getFormTranslationAdapter();
        return $class_path::getInstance($locale);
    }
}