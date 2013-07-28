<?php

namespace FormGenerator\FormGeneratorSimpleTemplateEngine;

use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\FormCollection\Collection;
use FormGenerator\FormElements\LegendElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\FormElement;

/**
 * Description of TemplateEngine
 *
 * @author josesantos
 */
class SimpleTemplateEngine extends BaseTemplateEngine{
    
    
    /**
     * Load and set the html template buffer as a string
     * If a template name is passed to the method, it will check if the
     * file exists.
     * If not return a empty stream
     * @return string 
     */
    protected function getTemplateStream()
    {
        if(is_file($this->_template_path))
        {
            ob_start();
            include($this->_template_path);
            $this->_template_stream = ob_get_clean();
        }
        else
        {
            $this->_template_stream = parent::getTemplateStream();
        }
    }
    
    /**
     * Place the fieldset and legends elemets into the html template
     * @param Collection $fieldsetCollection
     */
    protected function placeFieldsetElements()
    {
        if(!$this->_fieldsetCollection->isEmpty())
        {
            foreach($this->_fieldsetCollection as /* @var $element FieldsetElement */ $element)
            {
                $element->build();
                $fieldset_parts = $element->getOpenAndCloseTag();
                
                $this->_template_stream = str_replace("{%fieldset-" . $element->get_mId() . "%}", $fieldset_parts[0], $this->_template_stream);
                $this->_template_stream = str_replace("{%/fieldset-" . $element->get_mId() . "%}", $fieldset_parts[1], $this->_template_stream);

                if($element->get_mLegend() instanceof LegendElement)
                {
                    $tag = "{%legend-" . $element->get_mId() . "%}";
                    $this->_template_stream = str_replace($tag, $element->get_mLegend()->build(), $this->_template_stream);
                }
            }
        }
    }
    
    /**
     * Place the form and labels elemets into the html template
     * @param Collection $elementsCollection
     * @param Collection $fieldsetCollection
     */
    public function placeFormElements()
    {
        if(!$this->_elementsCollection->isEmpty())
        {
            foreach($this->_elementsCollection as /* @var $element BaseElement */ $element)
            {
                $this->_template_stream = str_replace("{%" . $element->get_mId() . "%}", $element->build(), $this->_template_stream);
                if($element->get_mlabel() instanceof LabelElement)
                {
                    $tag = "{%label-" . $element->get_mId() . "%}";
                    $this->_template_stream = str_replace($tag, $element->get_mlabel()->build(), $this->_template_stream);
                }
            }
        }
    }
}