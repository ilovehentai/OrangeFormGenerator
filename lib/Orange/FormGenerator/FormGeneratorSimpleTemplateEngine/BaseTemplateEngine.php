<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;

use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\FormCollection\Collection;
use FormGenerator\FormElements\LegendElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\FormElement;

/**
 * Description of BasicTemplateEngine
 *
 * @author josesantos
 */
class BaseTemplateEngine {
    
    protected $_template_path = "";
    protected $_form_tag;
    protected $_elementsCollection;
    protected $_fieldsetCollection;
    protected $_js = "";
    protected $_template_stream;
    
    /**
     * set a empty stream
     */
    protected function getTemplateStream()
    {
        $this->_template_stream = "";
    }
    
    
    /**
     * Place the fieldset and legends elemets into the html template
     */
    protected function placeFieldsetElements()
    {
        if(!$this->_fieldsetCollection->isEmpty())
        {
            foreach($this->_fieldsetCollection as /* @var $element FieldsetElement */ $element)
            {
                $element->build();
                $fieldset_parts = $element->getOpenAndCloseTag();
                
                $this->_template_stream .= $fieldset_parts[0];
                
                if($element->get_mLegend() instanceof LegendElement)
                {
                    $this->_template_stream .= $element->get_mLegend()->build();
                }
                
                $this->_template_stream .= $fieldset_parts[1];
            }
        }
    }
    
    /**
     * Place the form and labels elemets into the html template
     */
    public function placeFormElements()
    {
        if(!$this->_elementsCollection->isEmpty())
        {
            foreach($this->_elementsCollection as /* @var $element BaseElement */ $element)
            {
                if($element->get_mlabel() instanceof LabelElement)
                {
                    $this->_template_stream .= $element->get_mlabel()->build();
                }
                $this->_template_stream .= $element->build();
            }
        }
    }
    
    public function compile() {
        $this->getTemplateStream();
        $this->placeFieldsetElements();
        $this->placeFormElements();
        $this->_form_tag->setStream($this->_template_stream);
        $html = $this->_form_tag->build();
        $html .= $this->_js;
        return $html;
    }
    
    public function addJavaScript($jscript) {
        $this->_js = $jscript;
    }
    
    public function get_template_path() {
        return $this->_template_path;
    }

    public function set_template_path($_template_path) {
        $this->_template_path = $_template_path;
    }

    public function get_form_tag() {
        return $this->_form_tag;
    }

    public function set_form_tag(FormElement $_form_tag) {
        $this->_form_tag = $_form_tag;
    }

    public function get_elementsCollection() {
        return $this->_elementsCollection;
    }

    public function set_elementsCollection(Collection $elementsCollection) {
        $this->_elementsCollection = $elementsCollection;
    }

    public function get_fieldsetCollection() {
        return $this->_fieldsetCollection;
    }

    public function set_fieldsetCollection(Collection $fieldsetCollection) {
        $this->_fieldsetCollection = $fieldsetCollection;
    }
}