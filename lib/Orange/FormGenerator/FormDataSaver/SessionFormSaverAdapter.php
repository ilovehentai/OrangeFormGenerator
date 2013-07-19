<?php

namespace FormGenerator\FormDataSaver;

use FormGenerator\FormGenerator;
use FormGenerator\FormGeneratorException\FormGeneratorException;
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

    public static function getInstance($formId = "") {
        return (!self::$_instance) ? self::$_instance = new SessionFormSaverAdapter($formId) : self::$_instance;
    }
    
}

?>
