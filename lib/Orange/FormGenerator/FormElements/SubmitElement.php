<?php

namespace FormGenerator\FormElements;

final class SubmitElement extends InputElement{
    protected $_mAttributes;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_mAttributes['type'] = "submit";
    }
}
