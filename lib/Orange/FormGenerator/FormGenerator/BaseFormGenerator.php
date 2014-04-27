<?php

namespace FormGenerator\FormGenerator;

/**
 * Base Form Generator attributes
 *
 * @author José Santos
 * @abstract
 */
abstract class BaseFormGenerator
{
    
    /**
     * The Form unique FormId name
     * @var string
     */
    protected $formId;
    
    /**
     * Form Arguments Collection
     * @var array 
     */
    protected $args;
    
    /**
     * If the form is read only
     * @var boolean
     */
    protected $readOnly = false;
    
    /**
     * If the form is on debug mode
     * @var boolean
     */
    protected $debug = false;
    
    /**
     * If the form is on render debug text mode
     * @var boolean
     */
    protected $debug_render = false;
    
    /**
     * Activate CSRF Token
     * @var boolean 
     */
    protected $hasCSRFToken = true;
    
    /**
     * Define if render javascript validation
     * @var boolean 
     */
    protected $renderJS = true;
    
    /**
     * Define if the form only outputs html 
     * with no events or actions enabled
     * @var boolean
     */
    protected $outputOnly = false;
    
    /**
     * Create a FormGenerator Object, if the name of a configuration file is passed in the constructor,
     * it will check whether the file exists and set it as the configuration file
     * if not throws an exception
     * @param string $formId
     * @param array $args 
     * @abstract
     */
    public function __construct($formId, array $args = array())
    {   
        $this->formId = $formId;
        $this->args = $args;
    }
    
    /**
     * On serialization save only important data, 
     * form unique id, if readonly and if has CSRF Token
     * @return array 
     */
    public function __sleep() {
        //Save only important info
        return array("formId", "readOnly", "hasCSRFToken");
    }
    
    /** Getters and Setters **/
    
    /**
     * Get the form id
     * @return string
     */
    public function getFormId() {
        return $this->formId;
    }
    
    /**
     * Set de Form Id
     * @param string $formId
     */
    public function setFormId($formId) {
        $this->formId = $formId;
    }
    
    /**
     * Verify if the form is debug mode
     * @return boolean 
     */
    public function isDebug() {
        return $this->debug;
    }

    /**
     * Set the form as debug mode
     * @param boolean $_mDebug 
     */
    public function setDebug($_mDebug) {
        $this->debug = $_mDebug;
    }
    
    /**
     * Verify if the form is readonly
     * @return boolean 
     */
    public function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * Set the form as readonly
     * @param boolean $readOnly 
     */
    public function setReadOnly($readOnly) {
        if(is_bool($readOnly)) {
            $this->readOnly = $readOnly;
        }
    }
    /**
     * Is CSRF Token active in form
     * If the form is in render debug state it will always return false
     * @return boolean
     */
    public function isHasCSRFToken() {
        return ($this->debug_render) ? false : $this->hasCSRFToken;
    }

    /**
     * Set CSRF Token active in form
     * @param type $isHasCSRFToken
     */
    public function setHasCSRFToken($isHasCSRFToken) {
        if(is_bool($isHasCSRFToken)) {
            $this->hasCSRFToken = $isHasCSRFToken;
        }
    }
    
    /**
     * Get if render JS validation script
     * @return boolean
     */
    public function isRenderJS() {
        return $this->renderJS;
    }

    /**
     * Set if render JS validation script
     * @param boolen $renderJs
     */
    public function setRenderJS($renderJs) {
        $this->renderJS = $renderJs;
    }
    
    /**
     * Get if the form only output to html with no events or actions
     * @return boolean
     */
    public function getOutputOnly() {
        return $this->outputOnly;
    }

    /**
     * Set to only output the form with no actions or events
     * @param boolean $outputOnly
     */
    public function setOutputOnly($outputOnly) {
        $this->outputOnly = $outputOnly;
    }
}
