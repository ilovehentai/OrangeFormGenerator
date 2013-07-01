<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;
use FormGenerator\FormCollection\Collection;
use FormGenerator\FormGeneratorSimpleTemplateEngine\SimpleTemplateEngine;
use FormGenerator\FormElements\FormElement;

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

    public function setTemplatePath($path) {
        $this->_mTemplateEngine->set_template_path($path);
    }

    public function render() {
        return $this->_mTemplateEngine->compile();
    }

    public function setFormElements(FormElement $formElement, Collection $elementsCollection, Collection $fieldsetCollection) {
        $this->_mTemplateEngine->set_form_tag($formElement);
        $this->_mTemplateEngine->set_elementsCollection($elementsCollection);
        $this->_mTemplateEngine->set_fieldsetCollection($fieldsetCollection);
    }

    public function addJavaScript($jscript) {
        $this->_mTemplateEngine->addJavaScript($jscript);
    }
}
