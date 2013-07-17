<?php
namespace FormGenerator\FormParser;

/**
 * Description of ParserFactory
 *
 * @author josesantos
 */
class ParserFactory {
    //put your code here
    public static function getParserInstance($file_type)
    {
        $class_path = \FormGenerator\FormGenerator\FormConfig::getParserAdapterPath() . "FormParser" . ucfirst($file_type);
        return new $class_path();
    }
}