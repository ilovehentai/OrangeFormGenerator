<?php

namespace FormGenerator;

use FormGenerator\FormElements\LegendElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FormElement;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\FormElements\ElementFactory;
use FormGenerator\FormGenerator\FormGeneratorObserver;
use FormGenerator\FormGenerator\FormConfig;
use FormGenerator\FormGenerator\CheckConfigFile;
use FormGenerator\FormParser\ParserFactory;
use FormGenerator\FormGeneratorException\FormGeneratorException;
use FormGenerator\Validation\ValidationConfigClass;
use FormGenerator\FormGeneratorCache\FormGeneratorCache;
use FormGenerator\FormCollection\Collection;
use FormGenerator\FormDataSaver\FormDataSaverFactory;

class FormGenerator implements FormGeneratorObserver{
    
    /**
     * The Form unique Id name
     * @var string
     */
    private $_mId;
    /**
     * array Form configuration File name
     * @var string 
     */
    private $_mConfigFile;
    
    /**
     * array Form configuration path files
     * @var string 
     */
    private $_mConfigDir;
    
    /**
     * Html Form template
     * @var string 
     */
    private $_mTemplate;
    
    /**
     * array parsed to array form data
     * @var array
     */
    private $_mFormData;
    
    /**
     * If the form is read only
     * @var boolean
     */
    private $_mReadOnly = false;
    
    /**
     * If the form is on debug mode
     * @var boolean
     */
    private $_mDebug = false;
    
    /**
     * If the form is on render debug text mode
     * @var boolean
     */
    private $_mRender_debug = false;
    
    /**
     * Form submit error message returned by the validators
     * @var string
     */
    private $_mError;
    
    /**
     * List of BaseElement Objects
     * @var Collection 
     */
    private $_mElements;
    
    /**
     * List of FieldsetElement Objects
     * @var Collection 
     */
    private $_mFieldset;
    
    /**
     * FormElement Object
     * @var FormElement 
     */
    private $_mformElement;
    
    /**
     * List of Validators used by BaseElements in this form
     * @var array 
     */
    private $_mListValidators = array();
    
    /**
     * Error reported in form
     * @var string 
     */
    private $_mErrorsInForm = "";
    
    /**
     * Template form directory path
     * @var string 
     */
    private $_mTemplateDir = "";
    
    /**
     * Data Saver Instance
     * @var IFormDataSaver
     */
    private $_mDataSaver;
    
    /**
     * Activate CSRF Token
     * @var boolean 
     */
    private $_isCSRFToken = true;
    
    /**
     * List of defaults values for elements
     * @var array 
     */
    private static $_mElementsDefaultValues = array();
    
    /** Magic methods **/

    /**
     * Create a FormGenerator Object, if the name of a configuration file is passed in the constructor,
     * it will check whether the file exists and set it as the configuration file
     * if not throws an exception
     * @param string $idform
     * @param array $args 
     * @return FormGenerator
     */
    public function __construct($idform, array $args = array())
    {   
        $this->_mId = $idform;
        $this->checkArguments($args);
        $this->_mElements = new Collection();
        $this->_mFieldset = new Collection();
        $this->_mDataSaver = FormDataSaverFactory::getFormDataSaverInstance($idform);
    }
        
    /**
     * On serialization save only important data, form error,
     * form elements, list validators and readonly flag
     * @return array 
     */
    public function __sleep() {
        //Save only important info
        return array("_mId", "_mErrorsInForm", "_mElements", "_mListValidators", 
                                        "_mReadOnly", "_mDataSaver", "_isCSRFToken");
    }
    
    public function __wakeup() {
        ;
    }
    
    /**
     * Return a string representation of the form
     * @return string
     */
    public function __toString() {
        if($this->_mDebug === true)
        {
            return $this->renderDebug();
        }
        else
        {
            return $this->render();
        }
    }
    
    /**
     * On destruct save the form in the session
     */
    public function __destruct() {
        $this->save();
    }
    
    /** Public methods **/
    
    /**
     * Add a FieldsetElement Object to the FieldsetElement list
     * if a LegendElement Object is passed to the method it will also be added
     * to the LegendElement list
     * 
     * @param FieldsetElement $element
     * @param LegendElement $legend 
     * @return void
     */
    public function addFieldset(FieldsetElement $element, LegendElement $legend = null)
    {
        
        if(is_a($legend, "FormGenerator\FormElements\LegendElement"))
        {
            /** @var FieldsetElement $element **/
            $element->set_mLegend($legend);
        }
        
        $this->_mFieldset->add($element);
    }
    
    /**
     * Add a BaseElement Object to the BaseElement list, the element will add
     * the FormGenerator to is observers list and set is validations if they exists
     * if a LabelElement Object is passed to the method it will also be added
     * to the LabelElement list
     * 
     * @param BaseElement $element
     * @param LabelElement $label
     * @return void 
     */
    public function addElement(BaseElement $element, LabelElement $label = null)
    {
        $element->addObserver($this);
        $element->setValidations();
        
        if(is_a($label, "FormGenerator\FormElements\LabelElement"))
        {
            $element->set_mlabel($label);
        }
        
        $this->_mElements->add($element);
    }
    
    /**
     * Parse the form configuration file to an array
     * @return void
     */
    public function parseConfigFile()
    {
        $file_type = pathinfo($this->_mConfigFile, PATHINFO_EXTENSION);
        $parser = ParserFactory::getParserInstance($file_type);
        /* @var $parser FormGenerator\FormParser\IFormParser */
        $this->_mFormData = $parser::parse($this->_mConfigFile);
    }
    
    /**
     * Create Javascript JSON for validation and place it in JS template
     * @return string|null 
     */
    public function buildJavaScript()
    {
        $js_validation_data = ValidationConfigClass::getInstance()->get_mConfig_data();
        if(is_file(__DIR__ . $js_validation_data["js_template"]))
        {
            if(!empty($this->_mListValidators))
            {
                $jsFields = array();
                foreach($this->_mListValidators as $field_id => $validatores)
                {
                    $js_string_option = '"' . $field_id . '": [';
                    $js_validatores = array();
                    foreach($validatores as $fields) {
                        $js_validatores[] = '{"validator" : "' . 
                                                    $fields['rule'] . '", "msg" : "' . 
                                                    $fields['message'] . '"}';
                    }
                    $js_string_option .= implode(",", $js_validatores);
                    $js_string_option .= ']';
                    $jsFields[] = $js_string_option;
                }
                
                $jsFields = implode(",\n", $jsFields);
                
                $content = file_get_contents(__DIR__ . $js_validation_data["js_template"]);
                
                return sprintf($content, $jsFields, $js_validation_data["title_msg"], 
                                        $js_validation_data["error_msg"], $this->_mFormData['form']['id']);
            }else{
                
                return null;
            }
        }
    }
    
    /**
     * Add the validatores used in the form into the validatorslist
     * @param BaseElement $sender
     * @param array $args 
     */
    public function update(BaseElement $sender, $args) {
        $args['id'] = $sender->get_mId();
        if(!array_key_exists($args["id"], $this->_mListValidators)) {
            $this->_mListValidators[$args["id"]][] = $args;
        } else {
            array_push($this->_mListValidators[$args["id"]], $args);
        }
    }
    
    /**
     * Render The form object as html and js script
     * @param string $template
     * @return string 
     */
    public function render($template = "")
    {
        if(is_file($this->_mConfigFile))
        {
                
            $this->parseConfigFile();
            $this->checkConfigsArguments();
            $this->setTemplateFile($template);

            FormGeneratorCache::iniCache();
            $cache_name = FormGeneratorCache::expectedCacheName($this->_mId, $this->_mConfigFile,
                                                            $this->_mTemplateDir . DIRECTORY_SEPARATOR . $this->_mTemplate);
            $cache_exist = FormGeneratorCache::checkCacheFile($cache_name);
            $html = "";

            if($cache_exist === false || $this->_mDebug === true)
            {

                $html = $this->generateForm();

                FormGeneratorCache::clearFileCache($this->_mId);
                FormGeneratorCache::saveDataFile($cache_name, $html);

            }
            else
            {
                include FormGeneratorCache::$_cache_path . DIRECTORY_SEPARATOR . $cache_name;
            }

            return $html;
        }
        else
        {
            throw new FormGeneratorException("No configuration data no such file: " . $this->_mConfigFile);
        }
        
    }
    
    /**
     * Render The form object as textarea with the html and js script
     * @param string $template
     * @return string 
     */
    public function renderDebug($template = "")
    {
        $this->_mRender_debug = true;
        $stream = $this->render($template);
        $html = str_replace(array("<", ">"), array("&lt;", "&gt;"), $stream);
        $text_area = new FormElements\TextareaElement(
                            array("text" => $html, 
                                  "attributes" => array("style" => "width:100%;height:800px"))
                    );
        
        $this->_mRender_debug = false;
        return $text_area->build();
    }
    
    
    /**
     * Define the configuration files directory
     * @param string $configDir 
     */
    public function defineTheConfigDirectory($configDir="")
    {
        if(!empty($configDir))
        {
            $this->_mConfigDir = $configDir;
        }
        else
        {
            $this->_mConfigDir = FormConfig::getConfigDir();
        }
    }
    
    /**
     * Define the configuration file, let the class know where to look for external informations
     * about the form
     * @param string $configFile 
     */
    public function defineTheConfigFile($configFile="")
    {
        if(!empty($configFile))
        {
            if(is_string($configFile) && is_file($this->_mConfigDir . $configFile))
            {
                $this->_mConfigFile = $this->_mConfigDir . $configFile;
            }
            else
            {
                throw new FormGeneratorException("Config file not found. No such file : " . $this->_mConfigDir . $configFile);
            }
        }
        else if ($configFile === "")
        {
            $this->_mConfigFile = FormConfig::getDefaultConfigFile();
        }
    }
    
    /**
     * Define the directory where to store the cache
     * @param string $cacheDir 
     */
    public function defineCacheDirectory($cacheDir = "")
    {
        FormGeneratorCache::setCachePath($cacheDir);
    }
    
    /**
     * Define the elements defaults values to output
     * @param array $elements_default_values 
     */
    public function defineElementsDefaultsValues(array $elements_default_values = null)
    {
        if(!empty($elements_default_values) && is_array($elements_default_values))
        {
            self::$_mElementsDefaultValues = $elements_default_values;
        }
    }
    
    /**
     * define the validation file
     * @param string $validationFile 
     */
    public function defineValidationFile($validationFile = "")
    {
        if(empty($validationFile))
        {
            $validationFile = FormConfig::getDefaultValidationFile();
        }
        
        ValidationConfigClass::getInstance()->loadValidationConfigFile($validationFile);
    }
    
    /**
     * Define the template directory
     * @param type $templateDirPath 
     */
    public function defineTemplateDirectory($templateDirPath = "")
    {
        if(!empty($templateDirPath))
        {
            $this->_mTemplateDir = $templateDirPath;
        }
        else
        {
            $this->_mTemplateDir = __DIR__;
        }
    }
    
    
    /** Private Methods **/
    
    /**
     * check and site formGenerator basic arguments. Non valid arguments are ignored.
     * @param array $args 
     */
    private function checkArguments(array $args = array())
    {
        $valid_arguments = array(
                                 "configDir" => "defineTheConfigDirectory", 
                                 "configFile" => "defineTheConfigFile", 
                                 "cacheDir" => "defineCacheDirectory",
                                 "templateDir" => "defineTemplateDirectory",
                                 "elements_default_values" => "defineElementsDefaultsValues",
                                 "validationFile" => "defineValidationFile",
                                 "readonly" => "set_mReadOnly",
                                 "use_csrf_token" => "set_isCSRFToken"
                                );
        
        foreach($valid_arguments as $key => $method)
        {
            
            if(!array_key_exists($key, $args))
            {
                $args[$key] = null;
            }
            
            $this->$method($args[$key]);
        }
        
    }
    
    private function checkConfigsArguments() {
        
        if(isset($this->_mFormData["configs"]) && is_array($this->_mFormData["configs"])) {
            
            $root_dir = "";
            
            foreach ($this->_mFormData["configs"] as $key => $config) {
                if(is_string($config)) {
                    if(strstr($config, "%DIR%")){
                        $this->_mFormData["configs"][$key] = str_replace("%DIR%", __DIR__, $config);
                    }
                    if(strstr($config, "%ROOT%")){
                        $this->_mFormData["configs"][$key] = str_replace("%ROOT%", $root_dir, $config);
                    }
                    if($key == "rootDir") {
                        $root_dir = $this->_mFormData["configs"][$key];
                    }
                }
            }
            
            $this->checkArguments($this->_mFormData["configs"]);
        }
    }
    
    /**
     * Set the template name
     * @param string $template
     * @return void 
     */
    private function setTemplateFile($template = "")
    {
        if(empty($template))
        {
            $template = $this->_mFormData["template"];
        } 
        
        $this->set_mTemplate($template);
    }
    
    /**
     * Parse the html part of the form
     * @param string $template
     * @return string 
     */
    private function generateForm()
    {
        if(!empty($this->_mFormData))
        {
            $this->iniForm();
            
            CheckConfigFile::check($this->_mFormData);
            if(CheckConfigFile::$result != "")
            {
                return nl2br(CheckConfigFile::$result);
            }
            else
            {
                $this->getFormElements();
                $this->getFormFieldsets();
                /* @var $templateAdapter Patterns\IFormTemplateAdapter */
                $templateAdapter = FormGeneratorSimpleTemplateEngine\TemplateEngineFactory::getTemplateInstance();
                $templateAdapter->setTemplatePath($this->_mTemplateDir . $this->_mTemplate);
                $templateAdapter->setFormElements($this->_mformElement, $this->_mElements, $this->_mFieldset);
                //$templateAdapter->addJavaScript($this->buildJavaScript());
                return $templateAdapter->render();
            }
        }
    }
    
    /**
     * Fill the FormElement List with BaseElement Objects declared on the form config file
     * If the BaseElement has a label, the LabelElement will also be added.
     * @return void
     */
    private function getFormElements()
    {
        if(!empty($this->_mFormData['fields']))
        {
            foreach($this->_mFormData['fields'] as $field)
            {
                $label = null;
                if(array_key_exists("label", $field))
                {
                    $label_text = "";
                    $label_attributes = array();
                    if(is_string($field['label']))
                    {
                        $label_text = $field['label'];
                    }
                    else if(is_array($field['label']))
                    {
                        if(array_key_exists("text", $field['label']))
                        {
                            $label_text = $field['label']['text'];
                        }
                        if(array_key_exists("attributes", $field['label']))
                        {
                            $label_attributes = $field['label']['attributes'];
                        }
                        
                    }
                    $label_attributes = array_merge($label_attributes, array("for" => $field['id']));
                    $label = new LabelElement(array("text" => $label_text,
                                                "attributes" => $label_attributes)
                                        );
                }
                
                if($this->_mReadOnly) {
                    $field["attributes"] = array_merge($field["attributes"], 
                                                        array("readonly" => "readonly", "disabled" => "disabled"));
                }
                
                //Check First if a element already exists in the form element list. Cannot have duplicates id on the form
                if(!$this->isElementById($field["id"])) {
                    $this->addElement(ElementFactory::creatElement($field), $label);
                }
            }
        }
    }
    
    private function isElementById($id) {
        $found = false;
        if($this->_mElements->getIterator()->valid()) {
            foreach($this->_mElements as $key => $element){
                /* @var $element BaseElement */
                if($element->get_mId() === $id) {
                    $found = $key;
                    break;
                }
            }
        }
        return $found;
    }
    
    private function getElementById($id) {
        $pos = $this->isElementById($id);
        if($pos !== false) {
            return $this->_mElements->get($pos);
        }
        return false;
    }
    
    public function getElementValue($element_id) {
        $value = false;
        if(!$this->_mElements->isEmpty()) {
            foreach($this->_mElements as /* @var $element BaseElement */ $element) {
                if($element->get_mId() === $element_id) {
                    $value = $element->get_mValue();
                    break;
                }
            }
        }
        
        return $value;
    }
    
    /**
     * Fill the FieldsetElement List with FieldsetElement Objects declared on the form config file
     * If the FieldsetElement has a legend, the LegendElement will also be added.
     * @return void 
     */
    private function getFormFieldsets()
    {
        if(!empty($this->_mFormData['fieldset']))
        {
            foreach($this->_mFormData['fieldset'] as $fieldset)
            {
                
                $legend = null;
                $fieldset['type'] = "Fieldset";
                if(array_key_exists("legend", $fieldset) && is_array($fieldset['legend']))
                {
                    if(!array_key_exists("text", $fieldset['legend'])){
                        $fieldset['legend']['text'] = "";
                    }
                    if(!array_key_exists("attributes", $fieldset['legend'])){
                        $fieldset['legend']['attributes'] = array();
                    }
                    $legend = new LegendElement(array("text" => $fieldset['legend']['text'],
                                                "attributes" => $fieldset['legend']['attributes'])
                                        );
                }
                
                $this->addFieldset(ElementFactory::creatElement($fieldset), $legend);
            }
        }
    }
    
    /**
     * Serialize the FormGenerator object and save it to the session
     * @return void 
     */
    private function save()
    {
        /* @var $_mDataSaver IFormDataSaver */
        $this->_mDataSaver->save($this);
    }
    
    /**
     * Initialize a FormElement object
     */
    private function iniForm()
    {
        if(isset($this->_mFormData['form']) && is_array($this->_mFormData['form']))
        {
            $csrf = ElementFactory::creatElement(array("type" => "CsrfToken", "id" => $this->_mId . "_csrf_token"));
            $this->addElement($csrf);
            if($this->get_isCSRFToken()) {
                $csrf->saveCSRFToken($this->_mId);
            }
            
            $this->_mformElement = new FormElement($this->_mFormData['form'], $csrf);
        }
        else
        {
            throw new FormGeneratorException("Error no form config");
        }
    }
    
    /** Getters and Setters **/

    /**
     * Get the config file name
     * @return string 
     */
    public function get_mConfigFile() {
        return $this->_mConfigFile;
    }

    /**
     * Set the config file name
     * @return string 
     */
    public function set_mConfigFile($_mConfigFile) {
        $this->_mConfigFile = $_mConfigFile;
    }

    /**
     * Verify if the form is readonly
     * @return boolean 
     */
    public function is_mReadOnly() {
        return $this->_mReadOnly;
    }

    /**
     * Set the form as readonly
     * @param boolean $_mReadOnly 
     */
    public function set_mReadOnly($_mReadOnly) {
        if(is_bool($_mReadOnly)) {
            $this->_mReadOnly = $_mReadOnly;
        }
    }

    /**
     * Get the html template file name
     * @return string
     */
    public function get_mTemplate() {
        return $this->_mTemplate;
    }

    /**
     * Set the html template file name
     * @return string
     */
    public function set_mTemplate($_mTemplate) {
        $this->_mTemplate = $_mTemplate;
    }
    
    /**
     * Verify if the form is debug mode
     * @return boolean 
     */
    public function is_mDebug() {
        return $this->_mDebug;
    }

    /**
     * Set the form as debug mode
     * @param boolean $_mDebug 
     */
    public function set_mDebug($_mDebug) {
        $this->_mDebug = $_mDebug;
    }
    
    /**
     * Get List of Elements
     * @return BaseElement[]
     */
    public function get_mElements() {
        return $this->_mElements;
    }
    
    /**
     * Get List of Validators
     * @return type BaseValidation[]
     */
    public function get_mListValidators() {
        return $this->_mListValidators;
    }
    
    /**
     * Set error message
     * @param string $_mErrorsInForm 
     */
    public function set_mErrorsInForm($_mErrorsInForm) {
        $this->_mErrorsInForm = $_mErrorsInForm;
    }
    
    /**
     * Set the form error message
     * @param string $errors
     */
    private function setErrors(array $errors)
    {
        $this->_mErrorsInForm .= implode("<br/>", $errors) . "<br/>";
    }
    
    /**
     * Get the form error message
     * @return type 
     */
    public function get_mErrorsInForm() {
        return $this->_mErrorsInForm;
    }
    
    /**
     * Is CSRF Token active in form
     * @return boolean
     */
    public function get_isCSRFToken() {
        return ($this->_mRender_debug) ? false : $this->_isCSRFToken;
    }

    /**
     * Set CSRF Token active in form
     * @param type $_isCSRFToken
     */
    public function set_isCSRFToken($_isCSRFToken) {
        if(is_bool($_isCSRFToken)) {
            $this->_isCSRFToken = $_isCSRFToken;
        }
    }
        
    
    /**
     * Internal function to chek is the submited CSRF Token is valid
     * @param \FormGenerator\FormGenerator $formObj
     * @param string $formId
     * @param array $submited_data
     * @return boolean
     */
    private function isValidCsrfToken(FormGenerator $formObj, $formId, array $submited_data) {
        if($this->get_isCSRFToken()) {
            $csrf_token = $formObj->getElementById($formId . "_csrf_token");
            $csrf_tokern_value = (array_key_exists($csrf_token->get_mName(), $submited_data)) ? 
                                                        $submited_data[$csrf_token->get_mName()] : null;
            $csrf_token->set_mValue($csrf_tokern_value);
            
            if($csrf_token->getCSRFToken($formId) != $csrf_token->get_mValue()) {
                $formObj->setErrors(array("Invalid Csrf Token"));
                $formObj->save();
                return false;
            }
        }
        return true;
    }
        
    /** Static methods **/
    
    /**
     * Check if the form data is valid
     */
    public static function isValid($formId)
    {
        $check_form = false;
        
        if(!empty($formId))
        {
            // cleans previous errors
            self::clearErrors($formId);
            $formObj = self::getFormData($formId);
            
            if(is_a($formObj, "FormGenerator\FormGenerator"))
            {
                if(count($formObj->get_mListValidators()) > 0 && count($formObj->get_mElements()) > 0)
                {
                    $submited_data = $_SERVER["REQUEST_METHOD"] == "POST" ? $_POST : $_GET;
                    
                    if($submited_data){
                        
                        if(!$formObj->isValidCsrfToken($formObj, $formId, $submited_data)) {
                            return false;
                        }
                        
                        $check_form = true;
                        foreach($formObj->_mElements as $element)
                        {
                            /* @var $element BaseElement */
                            if(is_a($element,"FormGenerator\FormElements\FileElement"))
                            {
                                $submited_data[$element->get_mName()] = $_FILES[$element->get_mName()]["name"];
                            }
                            
                            $element_value = (array_key_exists($element->get_mName(), $submited_data)) ? 
                                                                    $submited_data[$element->get_mName()] : null;
                            $element->set_mValue($element_value);
                            if(!$element->isValid($formObj)) {
                                $formObj->setErrors($element->get_mErrors());
                                $check_form = false;
                            }	
                        }
                        
                    }
                }
                $formObj->save();
            } else {
                throw new FormGeneratorException("Invalid Form Data for Form ID: $formId");
            }
        } else {
            throw new FormGeneratorException("Invalid Form ID: empty");
        }
        
        return $check_form;
    }
    
    /**
     * Retrieve FormGenerator object from session
     * @param string $formId
     * @return FormGenerator 
     */
    private static function getFormData($formId)
    {
        /* @var $form_data_saver_adapter IFormDataSaver */
        $form_data_saver_adapter = FormDataSaverFactory::getFormDataSaverInstance($formId);
        return $form_data_saver_adapter::getFormData($formId);
    }
    
    /**
     * Get the Elements default values
     * @return array 
     */
    public static function get_mElementsDefaultValues() {
        return self::$_mElementsDefaultValues;
    }

    /**
     * Set the Elements defaults values
     * @param array $_mElementsDefaultValues 
     */
    public static function set_mElementsDefaultValues(array $_mElementsDefaultValues) {
        self::$_mElementsDefaultValues = $_mElementsDefaultValues;
    }
    
    /**
     * Get the Elements default values
     * @return mixed<string, boolean> 
     */
    public static function get_mElementDefaultValues($index) {
        if(array_key_exists($index, self::$_mElementsDefaultValues))
        {
            return self::$_mElementsDefaultValues[$index];
        }
        return false;
    }
    
    
    /**
     * Clear erros in session for formId
     * @param string $formId 
     */
    public static function clearErrors($formId)
    {
        $form = self::getFormData($formId);
        if(is_a($form, "FormGenerator\FormGenerator")) {
            $form->set_mErrorsInForm("");
            $form->save();
        }
    }
    
    /**
     * Get erros in session for formId
     * @param string $formId 
     */
    public static function getFormErrors($formId)
    {
        $form = self::getFormData($formId);
        if(is_a($form, "FormGenerator\FormGenerator")) {
            return $form->get_mErrorsInForm();
        }
        return false;
    }

}
