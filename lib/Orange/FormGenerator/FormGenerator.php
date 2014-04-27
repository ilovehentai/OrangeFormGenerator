<?php

namespace FormGenerator;

use FormGenerator\FormGenerator\FormGeneratorObserver;
use FormGenerator\FormGenerator\FormConfig;
use FormGenerator\FormGenerator\FormGeneratorComposer;
use FormGenerator\FormGenerator\FormGeneratorConfigLoader;
use FormGenerator\FormGenerator\FormGeneratorPostHandler;
use FormGenerator\FormGenerator\FormGeneratorHTMLBuilder;
use FormGenerator\FormGeneratorCache\FormGeneratorCache;
use FormGenerator\FormElements\LegendElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FormElement;
use FormGenerator\FormElements\ElementFactory;
use FormGenerator\FormGeneratorException\FormGeneratorException;
use FormGenerator\Validation\ValidationConfigClass;
use FormGenerator\FormDataSaver\FormDataSaverFactory;
use FormGenerator\FormGeneratorTranslations\FromTranslationFactory;
use FormGenerator\FormGeneratorTranslations\IFormTranslation;

class FormGenerator extends FormGeneratorComposer implements FormGeneratorObserver
{
    
    /**
     * Instance of FormGeneratorConfigLoader, 
     * if the form is configured through a config file, this object has
     * all needed configurations
     * @var FormGeneratorConfigLoader 
     */
    protected $formConfigLoader;
    
    /**
     * Html Form template file name
     * @var string 
     */
    protected $templateFileName;
    
    /**
     * Config file Form configurations
     * @var array
     */
    protected $formConfigs;
    
    /**
     * FormElement Object
     * @var FormElement 
     */
    private $formElement;
    
    /**
     * Data Saver Adapter Instance
     * @var IFormDataSaver
     */
    private $dataSaverAdapter;
    
    /**
     * Define the locale for translations
     * @var string 
     */
    private $locale;
    
    /**
     * The translator controller
     * @var IFormTranslation
     */
    private $translator;
    
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
        parent::__construct($idform, $args);
        $this->configureForm($args);
        $this->dataSaverAdapter = FormDataSaverFactory::getFormDataSaverInstance();
    }
        
    /**
     * On serialization save only important data, form error,
     * form elements, list validators and readonly flag
     * @return array 
     */
    public function __sleep() {
        return array_merge(
                            array("locale", "dataSaverAdapter", "translator")
                            , parent::__sleep()
                            );
    }
    
    public function __wakeup() {}
    
    /**
     * Return the form id
     * @return string
     */
    public function __toString() {
        return $this->formId;
    }
    
    /**
     * On destruct save the form in the session
     */
    public function __destruct() {
        if(!$this->outputOnly) {
            $this->save();
        }
    }
    
    /** Public methods **/
    
    /**
     * Render The form object as html and js script
     * @param string $template
     * @return string 
     */
    public function render($template = "")
    {
        $this->setTemplateFile($template);
        FormGeneratorCache::iniCache();
        $cache_name = FormGeneratorCache::expectedCacheName($this->formId, 
                                                            $this->formConfigLoader->getConfigFile(),
                                                            $this->formConfigLoader->getTemplateDirectoryPath()
                                                            . DIRECTORY_SEPARATOR . 
                                                            $this->templateFileName);
        $cache_exist = FormGeneratorCache::checkCacheFile($cache_name);
        $html = "";

        if($cache_exist === false || $this->debug === true)
        {

            $html = $this->generateForm();

            FormGeneratorCache::clearFileCache($this->formId);
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
        $this->debug_render = true;
        $stream = $this->render($template);
        $html = str_replace(array("<", ">"), array("&lt;", "&gt;"), $stream);
        $text_area = new FormElements\TextareaElement(
                            array("text" => $html, 
                                  "attributes" => array("style" => "width:100%;height:800px"))
                    );
        
        $this->debug_render = false;
        return $text_area->build();
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
     * Search and get the value from an Element
     * @param string $element_id
     * @return string
     */
    public function getElementValue($element_id) {
        $value = false;
        if(!$this->formElementList->isEmpty()) {
            foreach($this->formElementList as /* @var $element BaseElement */ $element) {
                if($element->get_mId() === $element_id) {
                    $value = $element->get_mValue();
                    break;
                }
            }
        }
        
        return $value;
    }
    
    /**
     * look if a element exist. If true the key position is given else false.
     * @param string $id
     * @return type
     */
    public function isElementById($id) {
        $found = false;
        if($this->formElementList->getIterator()->valid()) {
            foreach($this->formElementList as $key => $element){
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
    public function getElementById($id) {
        $pos = $this->isElementById($id);
        if($pos !== false) {
            return $this->formElementList->get($pos);
        }
        return false;
    }
    
    
    /**
     * Get the form Translator
     */
    public function getFormTranslator() {
        $this->translator = FromTranslationFactory::getFormTranslationInstance(
                                                            $this->locale, 
                                                            $this->formConfigLoader->getTranslationDirectoryPath()
                                                        );
        return $this->translator;
    }
    
    /**
     * Delete all items values saved on submission
     */
    public function clearStoredItemsValues() {
        $this->dataSaverAdapter->deleteAllItems($this->formId);
    }
    
    /**
     * Serialize the FormGenerator object and save it to the data saver Adapter
     * @return void 
     */
    public function save()
    {
        /* @var $_mDataSaver IFormDataSaver */
        $this->dataSaverAdapter->save($this);
    }
    
    /** Private Methods **/
    
    private function configureForm(array $args = array())
    {
        $this->formConfigLoader = new FormGeneratorConfigLoader($args);
        $this->formConfigLoader->configure();
        $this->formConfigs = $this->formConfigLoader->getLoadedConfigs();
        
        $this->setArgumentsValues();
        $this->setTemplateFile();
        $this->getFormTranslator();
    }
    
    /**
     * check and set formGenerator basic arguments. Non valid arguments are ignored.
     */
    private function setArgumentsValues()
    {
        $valid_arguments = array(
                                 "elements_default_values" => "defineElementsDefaultsValues",
                                 "validationFile" => "defineValidationFile",
                                 "readonly" => "setReadOnly",
                                 "use_csrf_token" => "setHasCSRFToken",
                                 "renderjs" => "setRenderJs",
                                 "renderonly" => "setOutputOnly",
                                 "locale" => "setLocale",
                                 "save_on_submition" => "defineSaveOnSubmitionElements"
                                );
        
        foreach($valid_arguments as $key => $method)
        { 
            if(!array_key_exists($key, $this->formConfigs["configs"]))
            {
                $this->formConfigs["configs"][$key] = null;
            }
            $this->$method($this->formConfigs["configs"][$key]);
        }
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
            $template = isset($this->formConfigs["template"]) ? $this->formConfigs["template"] : "";
        }
        $this->setTemplateFileName($template);
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
        
        return FormGeneratorHTMLBuilder::buildFormHTML($this);
    }
    
    /**
     * Fill the FormElement List with BaseElement Objects declared on the form config file
     * If the BaseElement has a label, the LabelElement will also be added.
     * @return void
     */
    private function getFormElements()
    {
        if(!empty($this->formConfigs['fields']))
        {
            foreach($this->formConfigs['fields'] as $field)
            {
                $label = null;
                if(array_key_exists("label", $field))
                {
                    $label = $this->prepareLabel($field);
                }
                
                $field = $this->setElementAsReadonly($field);
                $this->addElementToForm($field, $label);
            }
        }
    }
    
    /**
     * Fill the FieldsetElement List with FieldsetElement Objects declared on the form config file
     * If the FieldsetElement has a legend, the LegendElement will also be added.
     * @return void 
     */
    private function getFormFieldsets()
    {
        if(!empty($this->formConfigs['fieldset']))
        {
            foreach($this->formConfigs['fieldset'] as $fieldset)
            {
                
                $legend = null;
                $fieldset['type'] = "Fieldset";
                if(array_key_exists("legend", $fieldset) && is_array($fieldset['legend']))
                {
                    $legend = $this->prepareLegend($fieldset);
                }
                
                $this->addFieldset(ElementFactory::creatElement($fieldset), $legend);
            }
        }
    }
    
    /**
     * Prepare and create the Label Element for a Form Element
     * @param array $element
     * @return \FormGenerator\FormElements\LabelElement
     */
    private function prepareLabel(array $element)
    {
        $label_text = "";
        $label_attributes = array("for" => $element['id']);
        
        if(is_string($element['label']))
        {
            $label_text = $element['label'];
        }
        else if(is_array($element['label']))
        {
            if(array_key_exists("text", $element['label']))
            {
                $label_text = $element['label']['text'];
            }
            if(array_key_exists("attributes", $element['label']))
            {
                $label_attributes = array_merge($label_attributes, $element['label']['attributes']);
            }

        }
        
        $label = new LabelElement(array("text" => $label_text, "attributes" => $label_attributes));
        $label->setTranslator($this->translator);
        return $label;
    }
    
    /**
     * Prepare and create the Legend Element for a Fieldset Element 
     * @param array $fieldset
     * @return \FormGenerator\FormElements\LegendElement
     */
    private function prepareLegend(array $fieldset)
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
        $legend->setTranslator($this->translator);
        
        return $legend;
    }
    
    /**
     * Check if this element Id already exists in form
     * and creates element to add it to form
     * @param array $element
     */
    private function addElementToForm(array $element, LabelElement $label = null)
    {
        if(!$this->isElementById($element["id"])) {
            $formElement = ElementFactory::creatElement($element);
            $formElement->setTranslator($this->translator);
            $this->addElement($formElement, $label);
        }
    }
    
    /**
     * Add to element the readonly and disabled attributes
     * @param array $element
     * @return array
     */
    private function setElementAsReadonly(array $element)
    {
        if($this->readOnly) {
            $element["attributes"] = array_merge($element["attributes"], 
                                                    array("readonly" => "readonly", "disabled" => "disabled"));
        }
        return $element;
    }
    
    /**
     * Initialize a FormElement object
     */
    private function iniForm()
    {
        
        $csrf = $this->addCsrfToken();
        
        if($this->formElement instanceof FormElement) {
            $this->formElement->set_csrfToken($csrf);
        } else if(isset($this->formConfigs['form']) && is_array($this->formConfigs['form'])) {
            $this->formElement = new FormElement($this->formConfigs['form'], $csrf);
        } else {
            throw new FormGeneratorException("Error: No Form Element declared!");
        }
        
    }
    
    /**
     * Transfer the saved values after post to the from elements
     */
    private function transferSavedElementValues() {
        if(!$this->getIdListToSave()->isEmpty()) {
            foreach($this->getIdListToSave() as $item) {
                $value = $this->dataSaverAdapter->getItemValue($this->formId, $item);
                if($value !== false && ($element = $this->getElementById($item)) !== false) {
                    $element->fillElement($value);
                }
            }
        }
    }

    /**
     * Get the html template file name
     * @return string
     */
    public function getTemplateFileName() {
        return $this->templateFileName;
    }

    /**
     * Set the html template file name
     * @param string $templateFileName
     */
    public function setTemplateFileName($templateFileName) {
        $this->templateFileName = $templateFileName;
    }
    
    /**
     * Get the form error message
     * @return string 
     */
    public function getErrorsInForm() {
        return $this->errorsToString();
    }
    
    /**
     * Get the form element
     * @return FormElement
     */
    public function getFormElement() {
        return $this->formElement;
    }

    /**
     * Set the form element
     * @param FormElement $formElement
     */
    public function setFormElement(FormElement $formElement) {
        $this->formElement = $formElement;
    }

    /**
     * Set the locale for translations
     * @param type $locale
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Get the defined Locale for translations
     * @return string
     */
    public function getLocale() {
        return $this->locale;
    }
    
    /**
     * Returns the Form Config Loader instance
     * @return FormGeneratorConfigLoader
     */
    public function getFormConfigLoader() {
        return $this->formConfigLoader;
    }
        
    /**
     * Add an item id to the collection of items to be saved after post
     * @param string $item
     */
    public function addSave_on_submitionItemId($item){
        $this->getIdListToSave()->add($item);
    }
    
    /**
     * Verify if element is to be saved
     * @param string $elementId
     * @return boolean
     */
    public function isElementValueToBeSaved($elementId) {
        if(!$this->getIdListToSave()->isEmpty()) {
            foreach($this->getIdListToSave() as $item) {
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
        $this->dataSaverAdapter->addItemValue($this->formId, $elementId, $value);
    }
    
    /**
     * Delete all saved element values
     */
    public function deleteElementsValue(){
        $this->dataSaverAdapter->deleteAllItems($this->formId);
    }
    
    /**
     * Add the validatores used in the form into the validatorslist
     * @param BaseElement $sender
     * @param array $args 
     */
    public function update(BaseElement $sender, $args) {
        $args['id'] = $sender->get_mId();
        if(!array_key_exists($args["id"], $this->formValidatorsList)) {
            $this->formValidatorsList[$args["id"]][] = $args;
        } else {
            array_push($this->formValidatorsList[$args["id"]], $args);
        }
    }
                
    /** Static methods **/
    
    /**
     * Check if the form data is valid
     */
    public static function isValid($formId)
    {
        return FormGeneratorPostHandler::isValid($formId);
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
        FormGeneratorPostHandler::clearErrors($formId);
    }
    
    /**
     * Get erros in session for formId
     * @param string $formId 
     */
    public static function getFormErrors($formId)
    {
        return FormGeneratorPostHandler::getFormErrors($formId);
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
}
