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
    private $_mFormId;

    public function __construct($formId = "") {
        if (!empty($formId)) {
            $this->setFormNameSpace($formId);
        }
    }

    public function getData() {
        return unserialize($_SESSION["ofg"][$this->_mFormId]["object"]);
    }

    public function save(FormGenerator $formObj) {
        $_SESSION["ofg"][$this->_mFormId]["object"] = serialize($formObj);
    }

    public function setFormNameSpace($formId) {
        $this->_mFormId = $formId;
        $this->setSessionNameSpace();
    }

    private function setSessionNameSpace() {
        if (session_id() != "") {
            if (isset($_SESSION["ofg"][$this->_mFormId])) {
                $_SESSION["ofg"][$this->_mFormId] = array();
            } else {
                throw new FormGeneratorException("This Session Namespace is in use!");
            }
        } else {
            throw new FormGeneratorException("Session have not been started!");
        }
    }

    public static function getInstance($formId = "") {
        return (!self::$_instance) ? self::$_instance = new SessionFormSaver($formId) : self::$_instance;
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

}

?>
