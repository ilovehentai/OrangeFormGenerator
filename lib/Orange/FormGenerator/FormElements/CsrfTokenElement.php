<?php

namespace FormGenerator\FormElements;

/**
 * Description of TokenElement
 *
 * @author josesantos
 */
final class CsrfTokenElement extends InputElement{
    
    public function __construct(array $config = array()) {
        parent::__construct($config);
        $this->_mAttributes['type'] = "hidden";
        $this->_mAttributes['name'] = "CSRFToken";
        $this->_mAttributes['value'] = $this->generateRandomToken();
    }
    
    private function generateRandomToken() {
        return sha1(uniqid(rand(), TRUE));
    }
}
