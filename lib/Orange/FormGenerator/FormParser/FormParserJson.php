<?php
namespace FormGenerator\FormParser;
use \FormGenerator\FormGeneratorException\FormGeneratorException;

/**
 * Description of FormParserJson
 *
 * @author josesantos
 */
class FormParserJson implements IFormParser{
    
    public static function parse($file) {
        if(is_file($file)){
            return json_decode(file_get_contents($file), true);
        }else{
            throw new FormGeneratorException("No such config file: " . $file);
        }
        
    }
}
