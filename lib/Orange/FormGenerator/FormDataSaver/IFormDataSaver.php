<?php

namespace FormGenerator\FormDataSaver;
use FormGenerator\FormGenerator;

/**
 *
 * @author josesantos
 */
interface IFormDataSaver {
    public function setFormNameSpace($formId);
    public function save(FormGenerator $formObj);
    public function getData();    
    public static function isFormNameSpace($formId);
    public static function getFormData($formId);
}

?>
