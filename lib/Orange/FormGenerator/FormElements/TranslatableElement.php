<?php
namespace FormGenerator\FormElements;
use FormGenerator\FormGenerator;

/**
 * Description of TranslatableElement
 *
 * @author josesantos
 */
abstract class TranslatableElement extends BaseElement{
    
    /**
     * Form Translation Adapter Instance
     * @var IFormTranslation 
     */
    protected $_mTranslator;
    protected $_mLocale;
    
    protected function getTranslator() {
        $this->_mTranslator = FormGenerator::getFormTranslator($this->_mLocale);
    }
    
    protected function translate() {
        $this->getTranslator();
        if(array_key_exists("text", $this->_mElementData) && !empty($this->_mElementData['text'])) {
            $this->_mElementData['text'] = $this->_mTranslator->getTranslation($this->_mElementData['text']);
        }
        return false;
    }
    
    public function setLocale($locale) {
        $this->_mLocale = $locale;
    }
    
    public function getLocale() {
        return $this->_mLocale;
    }
    
}