<?php

namespace FormGenerator\FormElements;

final class SelectElement extends BaseElement{
    
    private $_selected;
    
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<select%s>\n-options-\n</select>";
    }
    
    private function getOptions()
    {
        $options_list = array();
        $options_str = "";
        if(!empty($this->_mElementData['options']))
        {
            foreach($this->_mElementData['options'] as $key => $option)
            {
                if(is_array($option))
                {
                    $op_group = new OptGroupElement($option);
                    $op_group->set_mLabelAttribute($key);
                    $op_group->setTranslator($this->getTranslator());
                    if(!is_null($this->_selected)) {
                        $op_group->fillElement($this->_selected);
                    }
                    $options_str .= $op_group->build();
                }
                else
                {
                    $o_config = array("attributes" => array("value" => $key), "text" => $option);
                    $options_tmp = new OptionElement($o_config);
                    $options_tmp->setTranslator($this->getTranslator());
                    if($key == $this->_selected) {
                        $options_tmp->addAttribute("selected", "selected");
                    }
                    $options_list[] = $options_tmp->build();
                }
            }
        }
        if(!empty($options_list))
        {
            $options_str = implode("\n", $options_list);
        }
        
        return $options_str;
    }
    
    public function build() {
        if(!array_key_exists("id", $this->_mAttributes))
        {
            $this->_mAttributes['id'] = parent::get_mId();
        }
        $select = parent::build();
        $select = str_replace("-options-", $this->getOptions(), $select);
        return $select;
    }
    
    public function fillElement($value) {
        return $this->_selected = $value;
    }
}
