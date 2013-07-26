<?php

namespace FormGenerator\FormElements;

/**
 * Description of RadioGroupElement
 *
 * @author josesantos
 */
final class RadiogroupElement extends BaseElement {

    private $_checked;
    
    public function build() {
        parent::build();
        $str_element = "";
        if (array_key_exists("group", $this->_mElementData) && is_array($this->_mElementData['group'])) {
            $list_tmp_radio = array();

            foreach ($this->_mElementData['group'] as $key => $group) {
                $attributes['attributes'] = $this->_mAttributes;
                $attributes['attributes']['value'] = $group['value'];
                $attributes['attributes']['id'] = $this->_mElementData['id'] . $key;

                $label = "";
                if (array_key_exists("label", $group)) {
                    $lattributes = array("for" => $attributes['attributes']['id']);
                    if (array_key_exists("attributes", $group)) {
                        $lattributes = array_merge($lattributes, $group['attributes']);
                    }
                    $label_tmp = new LabelElement(array("text" => $group['label'],
                        "attributes" => $lattributes));
                    $label_tmp->setTranslator($this->getTranslator());
                    $label = $label_tmp->build();
                }
                
                $radio_tmp = new RadioElement($attributes);
                if((!is_null($this->_checked) && $this->_checked == $radio_tmp->getAttribute("value"))
                        || (is_null($this->_checked) && array_key_exists("checked", $group))){
                    $radio_tmp->addAttribute("checked", "checked");
                }
                
                $list_tmp_radio[] = $label . $radio_tmp->build();
            }

            if (!empty($list_tmp_radio)) {
                $str_element = implode("\n", $list_tmp_radio);
            }
        }
        return $str_element;
    }
    
    public function fillElement($value) {
        $this->_checked = $value;
    }

}
