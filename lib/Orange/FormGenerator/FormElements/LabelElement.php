<?php
namespace FormGenerator\FormElements;

final class LabelElement extends BaseElement{
    
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<label%s>-data-</label>";
    }
    
    public function build() {
        if(!array_key_exists("for", $this->_mAttributes))
        {
            $this->_mAttributes['for'];
        }
        $label_html = parent::build();
        
        return str_replace("-data-", $this->_mElementData['text'], $label_html);
    }
}
