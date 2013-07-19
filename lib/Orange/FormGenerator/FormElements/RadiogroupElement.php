<?php

namespace FormGenerator\FormElements;

/**
 * Description of RadioGroupElement
 *
 * @author josesantos
 */
final class RadiogroupElement extends BaseElement {

    public function build() {
        $str_element = "";
        if (array_key_exists("group", $this->_mElementData) && is_array($this->_mElementData['group'])) {
            $list_tmp_radio = array();
            if (substr($this->_mAttributes['name'], -2) != "[]") {
                $this->_mAttributes['name'] .= "[]";
            }

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

                if (array_key_exists("checked", $group)) {
                    $attributes['attributes']['checked'] = $group["checked"];
                }

                $radio_tmp = new RadioElement($attributes);
                $list_tmp_radio[] = $label . $radio_tmp->build();
            }

            if (!empty($list_tmp_radio)) {
                $str_element = implode("\n", $list_tmp_radio);
            }
        }
        return $str_element;
    }

}
