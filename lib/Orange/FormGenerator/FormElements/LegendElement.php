<?php
namespace FormGenerator\FormElements;

final class LegendElement extends BaseElement{
    
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<legend%s>-data-</legend>";
    }
    
    public function build() {
        
        $label_html = parent::build();
        return str_replace("-data-", $this->_mElementData['text'], $label_html);
    }
}