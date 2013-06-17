<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;
use \FormGenerator\Patterns\IFormTemplateAdapter;
use FormGenerator\FormGeneratorSimpleTemplateEngine\SimpleTemplateEngine;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BasicTemplateEngineAdapter
 *
 * @author josesantos
 */
class SimpleTemplateEngineAdapter implements IFormTemplateAdapter{
    
    private $_mTemplateEngine;
    
    public function __construct() {
        $this->_mTemplateEngine = new SimpleTemplateEngine();
    }

    public function placeFormElements($elements, $fieldset) {
        $this->_mTemplateEngine->placeFormElements($stream, $elements, $labels, $fieldset, $legends);
    }

    public function setTemplatePath($path) {
        $this->_mTemplateEngine->set_template_path($path);
    }
}
