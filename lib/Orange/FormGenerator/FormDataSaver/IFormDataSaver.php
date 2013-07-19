<?php

namespace FormGenerator\FormDataSaver;
use FormGenerator\FormGenerator;

/**
 *
 * @author josesantos
 */
interface IFormDataSaver {
    public function save(FormGenerator $formObj);
    public static function isFormNameSpace($formId);
    public static function getFormData($formId);
    public static function addItem($formId, $index, $item);
    public static function getItem($formId, $index);
}

?>
