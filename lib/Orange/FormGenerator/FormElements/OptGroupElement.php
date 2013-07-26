<?php
namespace FormGenerator\FormElements;

use FormGenerator\FormCollection\Collection;

final class OptGroupElement extends BaseElement{
    
    private $_mOption_element_list = array();
    private $_OptionsCollection;
    private $_mLabelAttribute;
    private $_selected;
    
    public function __construct(array $config = array())
    {
        parent::__construct(array());
        if(!empty($config))
        {
            $this->_mOption_element_list = $config;
        }
        $this->_mSkeleton = "<optgroup%s>-data-</optgroup>";
        $this->_OptionsCollection = new Collection();
    }
    
    public function build() {
        if(!empty($this->_mLabelAttribute))
        {
            $this->_mAttributes['label'] = $this->translateAttribute($this->_mLabelAttribute);
        }
        $optiongroup = parent::build();
        $this->setOptions();
        return str_replace("-data-", $this->buildOptions(), $optiongroup);
    }
    
    public function setOptions()
    {
        
        if(!empty($this->_mOption_element_list))
        {
            foreach($this->_mOption_element_list as $opt_value => $opt_text)
            {
                $o_config = array("attributes" => array("value" => $opt_value), "text" => $opt_text);
                $option = new OptionElement($o_config);
                $option->setTranslator($this->getTranslator());
                if($opt_value == $this->_selected) {
                    $option->addAttribute("selected", "selected");
                }
                $this->addOption($option);
            }
        }
    }
    
    public function get_mLabelAttribute() {
        return $this->_mLabelAttribute;
    }

    public function set_mLabelAttribute($_mLabelAttribute) {
        $this->_mLabelAttribute = $_mLabelAttribute;
    }
    
    public function addOption(OptionElement $option) {
        $this->_OptionsCollection->add($option);
    }
    
    public function clearOptions() {
        $this->_OptionsCollection->clear();
    }
    
    public function fillElement($value) {
        $this->_selected = $value;
    }
    
    private function buildOptions()
    {
        if(!$this->_OptionsCollection->isEmpty()){
            return $this->optionsToString($this->_OptionsCollection);
        }
        
        return "";
    }
    
    private function optionsToString(Collection $optCollection) {
        $opt_string = "";
        foreach($optCollection as $opt_group) {
            $opt_string .= $opt_group->build();
        }
        return $opt_string;
    }
}