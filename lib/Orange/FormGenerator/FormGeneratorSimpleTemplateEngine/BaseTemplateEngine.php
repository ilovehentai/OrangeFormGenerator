<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;

use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FieldsetElement;
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
            throw new FormGeneratorException("Error no template file");
        }
    }
    
    
    /**
     * Place the fieldset and legends elemets into the html template
     * @param Collector $elements
     * @param Collector $fieldset
     * @return string 
     */
    private function placeFieldsetElements($stream, $fieldset = array())
    {
        if(!empty($fieldset))
        {
            foreach($fieldset as $key => /* @var $element FieldsetElement */ $element)
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
     * @param Collector $elements
     * @param Collector $fieldset
     * @return string 
     */
    public function placeFormElements($elements, $fieldset)
    {
        $stream = $this->getTemplateStream();
        $stream = $this->placeFieldsetElements($stream, $fieldset);
        
        if(!empty($elements))
        {
            foreach($elements as $key => /* @var $element BaseElement */ $element)
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
    
    public function get_template_path() {
        return $this->_template_path;
    }

    public function set_template_path($_template_path) {
        $this->_template_path = $_template_path;
    }


}