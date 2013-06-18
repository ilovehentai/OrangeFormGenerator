<?php
namespace FormGenerator\FormElements;

final class FieldsetElement extends BaseElement{
    
    protected $_mAttributes;
    protected $_mLegend;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mSkeleton = "<fieldset%s>-data-</fieldset>";
        $this->_mCheckName = false;
    }
    
    public function getOpenAndCloseTag()
    {
        return explode("-data-", $this->_mSkeleton);
    }

    public function get_mLegend() {
        return $this->_mLegend;
    }

    public function set_mLegend($_mLegend) {
        $this->_mLegend = $_mLegend;
    }


}
