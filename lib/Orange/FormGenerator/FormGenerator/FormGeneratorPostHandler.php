<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormGenerator\FormGenerator;

use FormGenerator\FormGenerator;
use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormDataSaver\FormDataSaverFactory;

/**
 * Description of FormGeneratorBuilder
 *
 * @author Joana
 */
class FormGeneratorPostHandler {
    
    /**
     * List of defaults values for elements
     * @var array 
     */
    private static $elementsDefaultValues = array();
    
    /**
     * The submited data
     * @var array 
     */
    private static $submitedData = false;
    
    /**
     * Check if the form data is valid
     */
    public static function isValid($formId)
    {
        $check_form = false;
        
        if(!empty($formId))
        {
            /* @var $formObj FormGenerator */
            $formObj = self::loadForm($formId);
            
            if($formObj instanceof FormGenerator)
            {
                $check_form = static::validateForm($formObj);
                $formObj->save();
                
                if($check_form) {
                    $formObj->clearStoredItemsValues();
                }
                
            } else {
                throw new FormGeneratorException("Invalid Form Data for Form ID: $formId");
            }
        } else {
            throw new FormGeneratorException("Invalid Form ID: empty");
        }
        
        return $check_form;
    }
    
    /**
     * Internal function to chek is the submited CSRF Token is valid
     * @param FormGenerator $formObj
     * @param array $submited_data
     * @return boolean
     */
    public static function isValidCsrfToken(FormGenerator $formObj, array $submited_data) {
        if($formObj->isHasCSRFToken()) {
            $csrf_token = $formObj->getElementById($formObj->getFormId() . "_csrf_token");
            $csrf_tokern_value = (array_key_exists($csrf_token->get_mName(), $submited_data)) ? 
                                                        $submited_data[$csrf_token->get_mName()] : null;
            $csrf_token->set_mValue($csrf_tokern_value);
            
            if($csrf_token->getCSRFToken($formObj->getFormId()) != $csrf_token->get_mValue()) {
                $formObj->addElementErrors(array("Invalid Csrf Token"));
                $formObj->save();
                return false;
            }
        }
        return true;
    }
    
    /**
     * Clear previous errors and load form
     * @param string $formId
     * @return FormGenerator
     */
    private static function loadForm($formId)
    {
        // cleans previous errors
        self::clearErrors($formId);
        return self::getFormData($formId);
    }
    
    /**
     * Check every Validators for errors from every Element 
     * returns false if errors occure or true if form is valid
     * @param \FormGenerator\FormGenerator $formObj
     * @return boolean
     */
    private static function validateForm(FormGenerator $formObj)
    {
        $form_valid = false;
        if(count($formObj->getFormValidatorsList()) > 0 && count($formObj->getFormElementList()) > 0)
        {
            static::$submitedData = $_SERVER["REQUEST_METHOD"] == "POST" ? $_POST : $_GET;

            if(static::$submitedData){

                $formObj->deleteElementsValue();

                if(!static::isValidCsrfToken($formObj, static::$submitedData)) {
                    return false;
                }
                $form_valid = true;
                foreach($formObj->getFormElementList() as $element)
                {
                    $elem_valid = static::validateElement($formObj, $element);
                    $form_valid = (!$elem_valid && $form_valid) ? false : $form_valid;
                }
            }
        }
        return $form_valid;
    }
    
    /**
     * Validate Form Element submited data
     * @param \FormGenerator\FormGenerator $formObj
     * @param \FormGenerator\FormGenerator\BaseElement $element
     */
    private static function validateElement(FormGenerator $formObj, BaseElement $element)
    {
        $element_valid = true;
        /* @var $element BaseElement */
        if($element instanceof FormGenerator\FormElements\FileElement)
        {
            static::$submitedData[$element->get_mName()] = $_FILES[$element->get_mName()]["name"];
        }

        $element_value = (array_key_exists($element->get_mName(), static::$submitedData)) ? 
                                                static::$submitedData[$element->get_mName()] : null;
        $element->set_mValue($element_value);
        $element->get_mErrors()->clear();
        if(!$element->isValid($formObj)) {
            $formObj->addElementErrors($element->get_mErrors()->toArray());
            $element_valid = false;
        }
        if($formObj->isElementValueToBeSaved($element->get_mId())) {
            $formObj->saveElementValue($element->get_mId(), $element->get_mValue());
        }
        
        return $element_valid;
    }
    
    /**
     * Retrieve FormGenerator object from session
     * @param string $formId
     * @return FormGenerator 
     */
    private static function getFormData($formId)
    {
        /* @var $form_data_saver_adapter IFormDataSaver */
        $form_data_saver_adapter = FormDataSaverFactory::getFormDataSaverInstance();
        return $form_data_saver_adapter::getFormData($formId);
    }
    
    /**
     * Get the Elements default values
     * @return array 
     */
    public static function get_mElementsDefaultValues() {
        return self::$elementsDefaultValues;
    }

    /**
     * Set the Elements defaults values
     * @param array $_mElementsDefaultValues 
     */
    public static function set_mElementsDefaultValues(array $_mElementsDefaultValues) {
        self::$elementsDefaultValues = $_mElementsDefaultValues;
    }
    
    /**
     * Get the Elements default values
     * @return mixed<string, boolean> 
     */
    public static function get_mElementDefaultValues($index) {
        if(array_key_exists($index, self::$elementsDefaultValues))
        {
            return self::$elementsDefaultValues[$index];
        }
        return false;
    }
    
    
    /**
     * Clear erros in session for formId
     * @param string $formId 
     */
    public static function clearErrors($formId)
    {
        /* @var $form FormGenerator */
        $form = self::getFormData($formId);
        if($form instanceof FormGenerator) {
            $form->clearFormErrors();
            $form->setErrorMessage("");
            $form->save();
        }
    }
    
    /**
     * Get erros in session for formId
     * @param string $formId 
     */
    public static function getFormErrors($formId)
    {
        /* @var $form FormGenerator */
        $form = self::getFormData($formId);
        if($form instanceof FormGenerator) {
            return $form->getErrorsInForm();
        }
        return false;
    }
}
