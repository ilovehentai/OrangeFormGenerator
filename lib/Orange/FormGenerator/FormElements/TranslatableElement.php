<?php
namespace FormGenerator\FormElements;
use FormGenerator\FormGeneratorTranslations\IFormTranslation;

/**
 * Description of TranslatableElement
 *
 * @author josesantos
 */
abstract class TranslatableElement{
    
    /**
     * Form Translation Adapter Instance
     * @var IFormTranslation 
     */
    protected $_mTranslator;
    
    public function getTranslator() {
        return $this->_mTranslator;
    }
    
    public function setTranslator(IFormTranslation $translator) {
        $this->_mTranslator = $translator;
    }
    
    protected function translate() {
        if($this->_mTranslator instanceof IFormTranslation) {
            if(array_key_exists("text", $this->_mElementData) && !empty($this->_mElementData['text'])) {
                $this->_mElementData['text'] = $this->_mTranslator->getTranslation($this->_mElementData['text']);
            }
        }
        return false;
    }
    
    protected function translateAttribute($attibute) {
        if($this->_mTranslator instanceof IFormTranslation) {
                return $this->_mTranslator->getTranslation($attibute);
        }
        return false;
    }
    
}