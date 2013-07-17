<?php
namespace FormGenerator\FormElements;

final class LegendElement extends TranslatableElement{
    
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<legend%s>-data-</legend>";
        $this->_mCheckName = false;
    }
    
    public function build() {
        
        $label_html = parent::build();
        $this->translate();
        return str_replace("-data-", $this->_mElementData['text'], $label_html);
    }
}