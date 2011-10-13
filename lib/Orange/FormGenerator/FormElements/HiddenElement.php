<?php

namespace FormGenerator\FormElements;

final class HiddenElement extends InputElement{
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mAttributes['type'] = "hidden";
    }
}
