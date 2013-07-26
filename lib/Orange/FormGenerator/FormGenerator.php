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
use FormGenerator\FormGeneratorTranslations\FromTranslationFactory;
use FormGenerator\FormGeneratorTranslations\IFormTranslation;

class FormGenerator implements FormGeneratorObserver{
    
    /**
     * The Form unique Id name
     * @var string
     */
    private $_mId;
    
    /**
     * Form Arguments
     * @var array 
     */
    private $_mArgs;
    
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
     * Form submit Collection messages error returned by the validators
     * @var Collection
     */
    private $_mFormErrors;
    
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
     * Define if render js validation
     * @var boolean 
     */
    private $_isRenderJs = true;
    
    /**
     * Define if the form only outputs html 
     * with no events or actions enabled
     * @var boolean
     */
    private $_outputOnly = false;
    
    /**
     * Define the locale for translations
     * @var string 
     */
    private $_mLocale;
    
    /**
     * Activate CSRF Token
     * @var boolean 
     */
    private $_mTranslations_path;
    
    /**
     * The translator controller
     * @var IFormTranslation
     */
    private $_mTranslator;
    
    /**
     * Collection of elements id to save after post. 
     * This allow to recover the post values of those items
     * @var Collection 
     */
    private $_mSave_on_submition;
    
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
     * @param string $config_file
     * @param array $args 
     * @return FormGenerator
     */
    public function __construct($idform, array $args = array())
    {   
        $this->_mId = $idform;
        $this->_mArgs = $args;
        $this->_mElements = new Collection();
        $this->_mFieldset = new Collection();
        $this->_mFormErrors = new Collection();
        $this->_mSave_on_submition = new Collection();
        $this->_mDataSaver = FormDataSaverFactory::getFormDataSaverInstance();
        $this->setConfigsPathAndFiles();
    }
        
    /**
     * On serialization save only important data, form error,
     * form elements, list validators and readonly flag
     * @return array 
     */
    public function __sleep() {
        //Save only important info
        return array("_mId", "_mLocale", "_mFormErrors", "_mElements", "_mListValidators", 
                             "_mReadOnly", "_mDataSaver", "_isCSRFToken", "_mTranslator", "_mSave_on_submition");
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
        if(!$this->_outputOnly) {
            $this->save();
        }
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
     * Add a CSRF Token Element to the form
     * @return CsrfElement
     */
    public function addCsrfToken() {
        
        $csrf = ElementFactory::creatElement(array("type" => "CsrfToken", "id" => $this->_mId . "_csrf_token"));
        $this->addElement($csrf);
        if($this->get_isCSRFToken()) {
            $csrf->saveCSRFToken($this->_mId);
        }
        
        return $csrf;
    }
    
    /**
     * Add an error to the error list
     * @param Collection $errors
     */
    public function addElementErrors(array $errors) {
        $this->_mFormErrors->add($errors);
    }
    
    /**
     * Clear the error list
     */
    public function clearFormErrors() {
        if(!$this->_mFormErrors->isEmpty()) {
            $this->_mFormErrors->clear();
        }
    }
    
    /**
     * Load if exists the config file for the form
     * @throws FormGeneratorException
     */
    public function loadConfigFile() {
        if($this->_mConfigFile !== FormConfig::getDefaultConfigFile()){
            $this->parseConfigFile();
            CheckConfigFile::check($this->_mFormData);
            if(CheckConfigFile::$result != "")
            {
                throw new FormGeneratorException(nl2br(CheckConfigFile::$result));

            }
        }
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
                                                    $this->_mTranslator->getTranslation($fields['message']) . 
                                            '"}';
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
        
        $this->loadConfigFile();
        $this->checkConfigsArguments();
        $this->setTemplateFile($template);
        $this->getFormTranslator();

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
    
    /**
     * Search and get the value from an Element
     * @param string $element_id
     * @return string
     */
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
     * Get the form Translator
     */
    public function getFormTranslator() {
        $this->_mTranslator = FromTranslationFactory::getFormTranslationInstance($this->_mLocale, $this->_mTranslations_path);
        return $this->_mTranslator;
    }
    
    /**
     * Delete all items values saved on submission
     */
    public function clearStoredItemsValues() {
        $this->_mDataSaver->deleteAllItems($this->_mId);
    }
    
    
    /** Private Methods **/
    
    /**
     * check and site formGenerator basic arguments. Non valid arguments are ignored.
     */
    private function checkArguments()
    {
        $this->setRootPath();
        $valid_arguments = array(
                                 "cacheDir" => "defineCacheDirectory",
                                 "templateDir" => "defineTemplateDirectory",
                                 "translationsDir" => "set_mTranslations_path",
                                 "elements_default_values" => "defineElementsDefaultsValues",
                                 "validationFile" => "defineValidationFile",
                                 "readonly" => "set_mReadOnly",
                                 "use_csrf_token" => "set_isCSRFToken",
                                 "renderjs" => "set_isRenderJs",
                                 "renderonly" => "set_outputOnly",
                                 "locale" => "setLocale",
                                 "save_on_submition" => "defineSaveOnSubmitionElements"
                                );
        
        foreach($valid_arguments as $key => $method)
        {
            
            if(!array_key_exists($key, $this->_mFormData["configs"]))
            {
                $this->_mFormData["configs"][$key] = null;
            }
            
            $this->$method($this->_mFormData["configs"][$key]);
        }
        
    }
    
    /**
     * Parse the root path defined in all configurations
     */
    private function setRootPath() {
        
        $root_dir = "";
        if(array_key_exists("rootDir", $this->_mFormData["configs"])) {
            $root_dir = str_replace("%DIR%", __DIR__, $this->_mFormData["configs"]["rootDir"]);
        }
        
        foreach ($this->_mFormData["configs"] as $key => $config) {
            if(is_string($config)) {
                if(strstr($config, "%DIR%")){
                    $this->_mFormData["configs"][$key] = str_replace("%DIR%", __DIR__, $config);
                }
                if(strstr($config, "%ROOT%")){
                    $this->_mFormData["configs"][$key] = str_replace("%ROOT%", $root_dir, $config);
                }
            }
        }
    }
    
    /**
     * Set the configurations files and path. If nothing is defined, the default values are loaded.
     */
    private function setConfigsPathAndFiles() {
        
        $config_dir = (isset($this->_mArgs["configDir"])) ? $this->_mArgs["configDir"] : "";
        $this->defineTheConfigDirectory($config_dir);
        $config_file = (isset($this->_mArgs["configFile"])) ? $this->_mArgs["configFile"] : "";
        $this->defineTheConfigFile($config_file);
        $cache_path = (isset($this->_mArgs["cacheDir"])) ? $this->_mArgs["cacheDir"] : "";
        $this->defineCacheDirectory($cache_path);
        
    }
    
    /**
     * Add configs arguments from a configuration file to the configurations passed to this object.
     * The arguments are then checked and validated.
     */
    private function checkConfigsArguments() {
        
        $extra_args = array();
        
        if(isset($this->_mFormData["configs"]) && is_array($this->_mFormData["configs"])) {
            $extra_args = $this->_mFormData["configs"];
        }
        
        $this->_mFormData["configs"] = array_merge($this->_mArgs, $extra_args);
        $this->checkArguments();
    }
    
    /**
     * Pass an array of elements id to be saved after form submition
     * @param array $items
     */
    private function defineSaveOnSubmitionElements(array $items = null){
        if(!empty($items)) {
            foreach($items as $item) {
                $this->addSave_on_submitionItemId($item);
            }
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
        
        $this->iniForm();
        $this->getFormElements();
        $this->getFormFieldsets();
        $this->transferSavedElementValues();
        
        /* @var $templateAdapter Patterns\IFormTemplateAdapter */
        $templateAdapter = FormGeneratorSimpleTemplateEngine\TemplateEngineFactory::getTemplateInstance();
        $templateAdapter->setTemplatePath($this->_mTemplateDir . $this->_mTemplate);
        $templateAdapter->setFormElements($this->_mformElement, $this->_mElements, $this->_mFieldset);
        if($this->get_isRenderJs() === true) {
            $templateAdapter->addJavaScript($this->buildJavaScript());
        }
        return $templateAdapter->render();
        
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
                    $label->setTranslator($this->_mTranslator);
                    
                }
                
                if($this->_mReadOnly) {
                    $field["attributes"] = array_merge($field["attributes"], 
                                                        array("readonly" => "readonly", "disabled" => "disabled"));
                }
                
                //Check First if a element already exists in the form element list. Cannot have duplicates id on the form
                if(!$this->isElementById($field["id"])) {
                    $element = ElementFactory::creatElement($field);
                    $element->setTranslator($this->_mTranslator);
                    $this->addElement($element, $label);
                }
            }
        }
    }
    
    /**
     * look if a element exist. If true the key position is given else false.
     * @param string $id
     * @return type
     */
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
    
    /**
     * Get an element by is Id, false if the element wasn't found
     * @param string $id
     * @return boolean
     */
    private function getElementById($id) {
        $pos = $this->isElementById($id);
        if($pos !== false) {
            return $this->_mElements->get($pos);
        }
        return false;
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
                    $legend->setTranslator($this->_mTranslator);
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
        
        $csrf = $this->addCsrfToken();
        
        if($this->_mformElement instanceof FormElement) {
            $this->_mformElement->set_csrfToken($csrf);
        } else if(isset($this->_mFormData['form']) && is_array($this->_mFormData['form'])) {
            $this->_mformElement = new FormElement($this->_mFormData['form'], $csrf);
        } else {
            throw new FormGeneratorException("Error: No Form Element declared!");
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
                $formObj->addElementErrors(array("Invalid Csrf Token"));
                $formObj->save();
                return false;
            }
        }
        return true;
    }
    
    /**
     * Transfer the saved values after post to the from elements
     */
    private function transferSavedElementValues() {
        if(!$this->_mSave_on_submition->isEmpty()) {
            foreach($this->_mSave_on_submition as $item) {
                $value = $this->_mDataSaver->getItemValue($this->_mId, $item);
                if($value !== false) {
                    if(($element = $this->getElementById($item)) !== false){
                        $element->fillElement($value);
                    }
                }
            }
        }
    }
    
    /** Getters and Setters **/
    
    /**
     * Get the form id
     * @return string
     */
    public function get_mId() {
        return $this->_mId;
    }

    /**
     * Set de Form Id
     * @param string $_mId
     */
    public function set_mId($_mId) {
        $this->_mId = $_mId;
    }

    
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
     * Get the form error message
     * @return type 
     */
    public function get_mErrorsInForm() {
        $this->errorsToString();
        return $this->_mErrorsInForm;
    }
    
    /**
     * Set error message
     * @param string $_mErrorsInForm 
     */
    public function set_mErrorsInForm($_mErrorsInForm) {
        $this->_mErrorsInForm = $_mErrorsInForm;
    }
    
    /**
     * Get the translations path
     * @return string
     */
    public function get_mTranslations_path() {
        return $this->_mTranslations_path;
    }

    /**
     * Set the translations path
     * @param string $_mTranslations_path
     */
    public function set_mTranslations_path($_mTranslations_path) {
        $this->_mTranslations_path = $_mTranslations_path;
    }
    
    /**
     * Get the form element
     * @return FormElement
     */
    public function get_mformElement() {
        return $this->_mformElement;
    }

    /**
     * Set the form element
     * @param FormElement $_mformElement
     */
    public function set_mformElement(FormElement $_mformElement) {
        $this->_mformElement = $_mformElement;
    }

        
    /**
     * Get all the form errors into a string
     * @param string $errors
     */
    public function errorsToString()
    {
        if(!$this->_mFormErrors->isEmpty()) {
            foreach ($this->_mFormErrors as $errors) {
                $this->_mErrorsInForm .= implode("<br/>", $errors) . "<br/>";
            }
        }
    }
    
    /**
     * Is CSRF Token active in form
     * If the form is in render debug state it will always return false
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
     * Get if render JS validation script
     * @return boolean
     */
    public function get_isRenderJs() {
        return $this->_isRenderJs;
    }

    /**
     * Set if render JS validation script
     * @param boolen $_isRenderJs
     */
    public function set_isRenderJs($_isRenderJs) {
        $this->_isRenderJs = $_isRenderJs;
    }

        
    /**
     * Set the locale for translations
     * @param type $locale
     */
    public function setLocale($locale) {
        $this->_mLocale = $locale;
    }

    /**
     * Get the defined Locale for translations
     * @return string
     */
    public function getLocale() {
        return $this->_mLocale;
    }
    
    /**
     * Get if the form only output to html with no events or actions
     * @return boolean
     */
    public function get_outputOnly() {
        return $this->_outputOnly;
    }

    /**
     * Set to only output the form with no actions or events
     * @param boolean $_outputOnly
     */
    public function set_outputOnly($_outputOnly) {
        $this->_outputOnly = $_outputOnly;
    }
    
    /**
     * Return the collection of items id to be saved after post
     * @return Collection
     */
    public function get_mSave_on_submition() {
        return $this->_mSave_on_submition;
    }

    /**
     * Set a collection of items id to be save after post
     * @param Collection $_mSave_on_post
     */
    public function set_mSave_on_submition(Collection $_mSave_on_submition) {
        $this->_mSave_on_submition = $_mSave_on_submition;
    }
    
    /**
     * Add an item id to the collection of items to be saved after post
     * @param string $item
     */
    public function addSave_on_submitionItemId($item){
        $this->_mSave_on_submition->add($item);
    }
    
    /**
     * Verify if element is to be saved
     * @param string $elementId
     * @return boolean
     */
    public function isElementValueToBeSaved($elementId) {
        if(!$this->_mSave_on_submition->isEmpty()) {
            foreach($this->_mSave_on_submition as $item) {
                if($item == $elementId) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Save a element value in the data saver to recover after submission
     * @param string $elementId
     * @param string $value
     */
    public function saveElementValue($elementId, $value) {
        /** @var $this->_mDataSaver \FormGenerator\FormDataSaver\IFormDataSaver **/
        $this->_mDataSaver->addItemValue($this->_mId, $elementId, $value);
    }
    
    /**
     * Delete all saved element values
     */
    public function deleteElementsValue(){
        $this->_mDataSaver->deleteAllItems($this->_mId);
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
                        
                        $formObj->deleteElementsValue();
                        
                        if(!$formObj->isValidCsrfToken($formObj, $formId, $submited_data)) {
                            return false;
                        }
                        $check_form = true;
                        foreach($formObj->_mElements as $element)
                        {
                            /* @var $element BaseElement */
                            if($element instanceof FormGenerator\FormElements\FileElement)
                            {
                                $submited_data[$element->get_mName()] = $_FILES[$element->get_mName()]["name"];
                            }
                            
                            $element_value = (array_key_exists($element->get_mName(), $submited_data)) ? 
                                                                    $submited_data[$element->get_mName()] : null;
                            $element->set_mValue($element_value);
                            $element->get_mErrors()->clear();
                            if(!$element->isValid($formObj)) {
                                $formObj->addElementErrors($element->get_mErrors()->toArray());
                                $check_form = false;
                            }
                            if($formObj->isElementValueToBeSaved($element->get_mId())) {
                                $formObj->saveElementValue($element->get_mId(), $element->get_mValue());
                            }
                        }
                        
                    }
                }
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
            $form->clearFormErrors();
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
    
    public static function prettyvd($a) {
        echo "<pre>";
        var_dump($a);
        echo "</pre>";
    }
}
