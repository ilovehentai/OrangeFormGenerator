<?php

namespace FormGenerator\FormElements;
use FormGenerator\FormDataSaver\FormDataSaverFactory;

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
    
    public function saveCSRFToken($formId) {
        $form_data_saver_adapter = FormDataSaverFactory::getFormDataSaverInstance($formId);
        $form_data_saver_adapter::addItem($formId, $this->_mAttributes['name'], $this->_mAttributes['value']);
    }
    
    public function getCSRFToken($formId) {
        $form_data_saver_adapter = FormDataSaverFactory::getFormDataSaverInstance($formId);
        return $form_data_saver_adapter::getItem($formId, $this->_mAttributes['name']);
    }
}
