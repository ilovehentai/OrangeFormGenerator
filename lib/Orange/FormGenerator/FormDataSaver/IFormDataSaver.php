<?php

namespace FormGenerator\FormDataSaver;
use FormGenerator\FormGenerator;

/**
 *
 * @author josesantos
 */
interface IFormDataSaver {
    public function save(FormGenerator $formObj);
    public function delete($formId);
    public function addItemValue($formId, $elementId, $value);
    public function getItemValue($formId, $elementId);
    public function deleteItem($formId, $elementId);
    public function deleteAllItems($formId);
    public static function isFormNameSpace($formId);
    public static function getFormData($formId);
    public static function addItem($formId, $index, $item);
    public static function getItem($formId, $index);
    public static function getInstance();
}