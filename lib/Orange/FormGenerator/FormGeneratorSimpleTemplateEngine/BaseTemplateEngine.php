<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;

use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\FormCollection\Collection;
use FormGenerator\FormGeneratorException\FormGeneratorException;
use FormGenerator\FormElements\FormElement;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BasicTemplateEngine
 *
 * @author josesantos
 */
class BaseTemplateEngine {
    
    private $_template_path = "";
    private $_form_tag;
    private $_elementsCollection;
    private $_fieldsetCollection;
    private $_js = "";
    
    /**
     * Load and return the html template buffer as a string
     * If a template name is passed to the method, it will check if the
     * file exists.
     * If not throws an exception
     * @return string 
     */
    private function getTemplateStream()
    {
        if(is_file($this->_template_path))
        {
            ob_start();
            include($this->_template_path);
            return ob_get_clean();
        }
        else
        {
            throw new FormGeneratorTemplateException("Error no template file");
        }
    }
    
    
    /**
     * Place the fieldset and legends elemets into the html template
     * @param string $stream
     * @param Collection $fieldsetCollection
     * @return string 
     */
    private function placeFieldsetElements($stream, Collection $fieldsetCollection)
    {
        if(!empty($fieldsetCollection))
        {
            foreach($fieldsetCollection as $key => /* @var $element FieldsetElement */ $element)
            {
                $element->build();
                $fieldset_parts = $element->getOpenAndCloseTag();
                
                $stream = str_replace("{%fieldset-" . $element->get_mId() . "%}", $fieldset_parts[0], $stream);
                $stream = str_replace("{%/fieldset-" . $element->get_mId() . "%}", $fieldset_parts[1], $stream);
                
                if(is_a($element->get_mLegend(), "FormGenerator\FormElements\LegendElement"))
                {
                    $tag = "{%legend-" . $element->get_mId() . "%}";
                    $stream = str_replace($tag, $element->get_mLegend()->build(), $stream);
                }
            }
        }
        return $stream;
    }
    
    /**
     * Place the form and labels elemets into the html template
     * @param Collection $elementsCollection
     * @param Collection $fieldsetCollection
     * @return string 
     */
    public function placeFormElements(Collection $elementsCollection, Collection $fieldsetCollection)
    {
        $stream = $this->getTemplateStream();
        $stream = $this->placeFieldsetElements($stream, $fieldsetCollection);
        
        if(!empty($elementsCollection))
        {
            foreach($elementsCollection as $key => /* @var $element BaseElement */ $element)
            {
                $stream = str_replace("{%" . $element->get_mId() . "%}", $element->build(), $stream);
                if(is_a($element->get_mlabel(), "FormGenerator\FormElements\LabelElement"))
                {
                    $tag = "{%label-" . $element->get_mId() . "%}";
                    $stream = str_replace($tag, $element->get_mlabel()->build(), $stream);
                }
            }
        }
        
        return $stream;
    }
    
    public function compile() {
        $html = $this->placeFormElements($this->_elementsCollection, $this->_fieldsetCollection);
        $this->_form_tag->setStream($html);
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