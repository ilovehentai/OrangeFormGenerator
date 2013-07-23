<?php

namespace FormGenerator\FormDataSaver;

use FormGenerator\FormGenerator;
/**
 * Description of SessionFormSaver
 *
 * @author josesantos
 */
class SessionFormSaverAdapter implements IFormDataSaver {

    private static $_instance;

    public function save(FormGenerator $formObj) {
        $_SESSION["ofg"][$formObj->get_mId()]["object"] = serialize($formObj);
    }
    
    public function delete($formId){
        $_SESSION["ofg"][$formObj->get_mId()] = null;
        unset($_SESSION["ofg"][$formId]);
    }
    
    public function addItemValue($formId, $elementId, $value) {
        $_SESSION["ofg"][$formId]["saved_values"][$elementId] = $value;
    }
    
    public function getItemValue($formId, $elementId) {
        if(isset($_SESSION["ofg"][$formId]["saved_values"][$elementId])) {
            return $_SESSION["ofg"][$formId]["saved_values"][$elementId];
        }
        return false;
    }
    
    public function deleteItem($formId, $elementId) {
        $_SESSION["ofg"][$formId]["saved_values"][$elementId] = null;
        unset($_SESSION["ofg"][$formId]["saved_values"][$elementId]);
    }
    
    public function deleteAllItems($formId) {
        $_SESSION["ofg"][$formId]["saved_values"] = null;
        unset($_SESSION["ofg"][$formId]["saved_values"]);
    }

    public static function getFormData($formId) {
        if(self::isFormNameSpace($formId)) {
            $formData = unserialize($_SESSION["ofg"][$formId]["object"]);
            if (is_a($formData, "FormGenerator\FormGenerator")) {
                return $formData;
            }
        }
        return false;
    }

    public static function isFormNameSpace($formId) {

        $valid = false;

        if (isset($_SESSION["ofg"][$formId]["object"])) {
            $valid = true;
        }

        return $valid;
    }
    
    public static function addItem($formId, $index, $item) {
        $_SESSION["ofg"][$formId][$index] = $item;
    }

    public static function getItem($formId, $index) {
        return $_SESSION["ofg"][$formId][$index];
    }

    public static function getInstance() {
        return (!self::$_instance) ? self::$_instance = new SessionFormSaverAdapter() : self::$_instance;
    }
    
}