<?php

namespace FormGenerator\FormElements;

use FormGenerator\FormCollection\Collection;

final class SelectElement extends BaseElement{
    
    private $_OptionsCollection;
    private $_OptionGroupsCollection;
    
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<select%s>\n-options-\n</select>";
        $this->_OptionsCollection = new Collection();
        $this->_OptionGroupsCollection = new Collection();
    }
    
    public function build() {
        if(!array_key_exists("id", $this->_mAttributes))
        {
            $this->_mAttributes['id'] = parent::get_mId();
        }
        $this->setOptions();
        $select = parent::build();
        $select = str_replace("-options-", $this->buildOptions(), $select);
        return $select;
    }
    
    public function addOption(OptionElement $option) {
        $this->_OptionsCollection->add($option);
    }
    
    public function clearOptions() {
        $this->_OptionsCollection->clear();
    }
    
    public function addOptionGroup(OptGroupElement $option) {
        $this->_OptionGroupsCollection->add($option);
    }
    
    public function clearOptionGroups() {
        $this->_OptionGroupsCollection->clear();
    }
    
    private function setOptions()
    {        
        if(!empty($this->_mElementData['options']))
        {
            foreach($this->_mElementData['options'] as $opt_value => $opt_text)
            {
                if(is_array($opt_text))
                {
                    $op_group = new OptGroupElement($opt_text);
                    $op_group->set_mLabelAttribute($opt_value);
                    $op_group->setTranslator($this->getTranslator());
                    $this->addOptionGroup($op_group);
                }
                else
                {
                    $o_config = array("attributes" => array("value" => $opt_value), "text" => $opt_text);
                    $option = new OptionElement($o_config);
                    $option->setTranslator($this->getTranslator());
                    $this->addOption($option);
                }
            }
        }
    }
    
    private function buildOptions()
    {
        if(!$this->_OptionGroupsCollection->isEmpty()) {
            return $this->optionsToString($this->_OptionGroupsCollection);
        } else if(!$this->_OptionsCollection->isEmpty()){
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
