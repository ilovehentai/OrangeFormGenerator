<?php

namespace FormGenerator\Validation;

use FormGenerator\FormElements\TranslatableElement;
use FormGenerator\FormGeneratorTranslations\IFormTranslation;

/**
 * Description of TranslatableValidation
 *
 * @author josesantos
 */
abstract class TranslatableValidation extends TranslatableElement{
    
    protected function translate() {
        if($this->_mTranslator instanceof IFormTranslation) {
            $this->_mErrorMessage = $this->_mTranslator->getTranslation($this->_mErrorMessage);
        }
        return false;
    }
}
