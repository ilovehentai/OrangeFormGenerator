<?php
namespace FormGenerator\FormElements;

final class OptionElement extends BaseElement{
    
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<option%s>options</option>";
    }
    
    public function build() {
        $option = parent::build();
        $this->translate();
        return str_replace("options", $this->_mElementData["text"], $option);
    }
}