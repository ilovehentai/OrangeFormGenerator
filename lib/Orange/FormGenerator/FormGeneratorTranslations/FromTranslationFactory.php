<?php

namespace FormGenerator\FormGeneratorTranslations;

/**
 * Description of FromTranslationFactory
 *
 * @author josesantos
 */
class FromTranslationFactory {
    
    public static function getFormTranslationInstance($locale = "", $translations_path = "")
    {
        $class_path = \FormGenerator\FormGenerator\FormConfig::getFormTranslationAdapter();
        return new $class_path($locale, $translations_path);
    }
}