<?php
namespace FormGenerator\FormElements;

final class OptGroupElement extends BaseElement{
    
    private $_mOption_element_list = array();
    private $_mLabelAttribute;
    
    public function __construct(array $config = array())
    {
        parent::__construct(array());
        if(!empty($config))
        {
            $this->_mOption_element_list = $config;
        }
        $this->_mSkeleton = "<optgroup%s>-data-</optgroup>";
    }
    
    public function build() {
        if(!empty($this->_mLabelAttribute))
        {
            $this->_mAttributes['label'] = $this->translateAttribute($this->_mLabelAttribute);
        }
        $optiongroup = parent::build();
        $options = $this->buildOptionElement();
        return str_replace("-data-", $options, $optiongroup);
    }
    
    public function addOptionElement(OptionElement $op)
    {
        $this->_mOption_element_list[] = $op;
    }
    
    public function buildOptionElement()
    {
        $str_op = "";
        $op_tmp = array();
        if(!empty($this->_mOption_element_list))
        {
            foreach($this->_mOption_element_list as $key => $op)
            {
                $o_config = array("attributes" => array("value" => $key), "text" => $op);
                $options_tmp = new OptionElement($o_config);
                $options_tmp->setTranslator($this->getTranslator());
                $op_tmp[] = $options_tmp->build();
            }
        }
        
        if(!empty($op_tmp))
        {
            $str_op = implode("\n", $op_tmp);
        }
        return $str_op;
    }
    
    public function get_mLabelAttribute() {
        return $this->_mLabelAttribute;
    }

    public function set_mLabelAttribute($_mLabelAttribute) {
        $this->_mLabelAttribute = $_mLabelAttribute;
    }
}