<?php
namespace FormGenerator\FormParser;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of FormGeneratorYaml
 *
 * @author josesantos
 */
class FormGeneratorYaml implements IFormParser{
    
    public static function parse($file) {
        return Yaml::parse($file);
    }
}
