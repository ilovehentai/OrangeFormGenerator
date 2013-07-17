<?php

namespace FormGenerator\FormDataSaver;

/**
 * Description of FormDataSaverFactory
 *
 * @author josesantos
 */
class FormDataSaverFactory {
    
    public static function getFormDataSaverInstance($idform = "")
    {
        $adapter = \FormGenerator\FormGenerator\FormConfig::getFormDataSaverAdapter();
        return $adapter::getInstance($idform);
    }
    
}
