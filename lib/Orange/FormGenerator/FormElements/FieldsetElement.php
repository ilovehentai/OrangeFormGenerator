<?php
namespace FormGenerator\FormElements;

final class FieldsetElement extends BaseElement{
    
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<fieldset%s>-data-</fieldset>";
    }
    
    public function getOpenAndCloseTag()
    {
        return explode("-data-", $this->_mSkeleton);
    }
}
