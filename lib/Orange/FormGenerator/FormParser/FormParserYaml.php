<?php
namespace FormGenerator\FormParser;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of FormParserYaml
 *
 * @author josesantos
 */
class FormParserYaml implements IFormParser{
    
    public static function parse($file) {
        return Yaml::parse($file);
    }
}
