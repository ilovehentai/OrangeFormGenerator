<?php
namespace FormGenerator\FormElements;

final class OptionElement extends BaseElement{
    
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<option%s>options</option>";
    }
    
    public function build($value = "") {
        $option = parent::build();
        return str_replace("options", $value, $option);
    }
}