<?php

namespace FormGenerator;

use Symfony\Component\Yaml\Yaml;
use FormGenerator\FormElements\LegendElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\BaseElement;
use FormGenerator\FormElements\FormElement;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\Patterns\ElementFactory;
use FormGenerator\Patterns\FormGeneratorObserver;
use FormGenerator\FormGeneratorException\FormGeneratorException;
use FormGenerator\Validation\ValidationConfigClass;
use FormGenerator\CacheClass;


class FormGenerator implements FormGeneratorObserver{
    
    /**
     * Yaml Form configuration File name
     * @var string 
     */
    private $_mConfigFile;
    
    /**
     * Html Form template
     * @var string 
     */
    private $_mTemplate;
    
    /**
     * Yaml parsed to array form data
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
     * Form submit error message returned by the validators
     * @var string
     */
    private $_mError;
    
    /**
     * List of BaseElement Objects
     * @var array 
     */
    private $_mElements = array();
    
    /**
     * List of FieldsetElement Objects
     * @var array 
     */
    private $_mFieldset = array();
    
    /**
     * List of LegendElement Objects
     * @var array 
     */
    private $_mLegends = array();
    
    /**
     * List of LabelsElement Objects
     * @var array 
     */
    private $_mLabels = array();
    
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
     * @param string $configFile 
     * @param array $elements_values
     * @return FormGenerator
     */
    public function __construct($idform, $configFile = "", array $elements_default_values = array())
    {
        if(!empty($configFile))
        {
            if(is_string($configFile) && is_file(OFG_CONFIG_DIR . $configFile))
            {
                $this->_mConfigFile = OFG_CONFIG_DIR . $configFile;
            }
            else
            {
                throw new \Exception("Config file not found.");
            }
        }
        else
        {
            $this->_mConfigFile = OFG_CONFIG_DIR . OFG_CONFIG_FILE;
        }
        
        if(!empty($elements_default_values) && is_array($elements_default_values))
        {
            self::$_mElementsDefaultValues = $elements_default_values;
        }
        
        ValidationConfigClass::getInstance()->loadValidationConfigFile(OFG_CONFIG_DIR . OFG_VALIDATION_CONFIG_FILE);
        $this->_mId = $idform;
    }
    
    /**
     * On serialization save only important data, form error,
     * form elements, list validators and readonly flag
     * @return array 
     */
    public function __sleep() {
        //Save only important info
        return array("_mError", "_mElements", "_mListValidators", "_mReadOnly");
    }
    
    public function __wakeup() {
        //Se necessário vai fazer o processamento necessário 
        //para unserializar o formulário da sessão
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
        
        array_push($this->_mFieldset, $element);
        if(is_a($legend, "FormGenerator\FormElements\LegendElement"))
        {
            $this->_mLegends[count($this->_mFieldset) - 1] = $legend;
        }
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
        
        array_push($this->_mElements, $element);
        if(is_a($label, "FormGenerator\FormElements\LabelElement"))
        {
            $this->_mLabels[count($this->_mElements) - 1] = $label;
        }
    }
    
    /**
     * Parse the form configuration yaml file to an array
     * @return void
     */
    public function parseConfigFile()
    {
        $this->_mFormData = Yaml::parse($this->_mConfigFile);
    }
    
    /**
     * Create Javascript JSON for validation and place it in JS template
     * @return string 
     */
    public function buildJavaScript()
    {
        $js_validation_data = ValidationConfigClass::getInstance()->get_mConfig_data();
        
        if(is_file(__DIR__ . $js_validation_data["js_template"]))
        {
            if(!empty($this->_mListValidators))
            {
                $jsFields = array();
                foreach($this->_mListValidators as $fields)
                {
                    $jsFields[] = '"' . $fields['id'] . '" : {validator : "' . 
                                    $fields['rule'] . '", msg : "' . 
                                    $fields['message'] . '"}';
                }

                $jsFields = implode(",\n", $jsFields);
            }
            
            $content = file_get_contents(__DIR__ . $js_validation_data["js_template"]);
            return sprintf($content, $jsFields, $js_validation_data["title_msg"], 
                                        $js_validation_data["error_msg"], $this->_mFormData['form']['id']);
        }
    }
    
    /**
     * Add the validatores used in the form into the validatorslist
     * @param BaseElement $sender
     * @param array $args 
     */
    public function update(BaseElement $sender, $args) {
        $args['id'] = $sender->get_mId();
        $this->_mListValidators[] = $args;
    }
    
    /**
     * Render The form object as html and js script
     * @param string $template
     * @return string 
     */
    public function render($template = "")
    {
        
        try{
            
            $cache_name = $this->_mId . "_" . md5_file($this->_mConfigFile) . ".php";
            CacheClass::iniCache();
            $stream = CacheClass::checkCacheFile($cache_name);
            
            if($stream === false || $this->_mDebug === true)
            {
                
                $stream = $this->generateHtmlForm($template);

                $this->_mformElement->setStream($stream);
                $stream = $this->_mformElement->build();

                $stream .= $this->buildJavaScript();

                CacheClass::clearFileCache($this->_mId);
                CacheClass::saveDataFile($cache_name, $stream);
                
            }
            else
            {
                $stream = "";
                include CacheClass::$_cache_path . DIRECTORY_SEPARATOR . $cache_name;
            }
            
            return $stream;
            
        }  
        catch (Exception $e)
        {
            echo $e->getTraceAsString();
        }
        
    }
    
    /**
     * Render The form object as textarea with the html and js script
     * @param string $template
     * @return string 
     */
    public function renderDebug($template = "")
    {
        $stream = $this->render($template);
        $html = str_replace(array("<", ">"), array("&lt;", "&gt;"), $stream);
        $text_area = new FormElements\TextareaElement(
                            array("text" => $html, 
                                  "attributes" => array("style" => "width:100%;height:800px"))
                    );
        return $text_area->build();
    }
    
    /** Private Methods **/
    
    /**
     * Load a return the html template buffer as a string
     * If a template name is passed to the method, it will check if the
     * file exists.
     * If not throws an exception
     * @param string $template
     * @return string 
     */
    private function loadTemplate($template = "")
    {
        if(!empty($template))
        {
            $this->_mTemplate = $template;
        }
        else
        {
            $this->_mTemplate = $this->_mFormData["template"];
        }
        
        if(is_file(__DIR__ . $this->_mTemplate))
        {
            ob_start();
            include(__DIR__ . $this->_mTemplate);
            return ob_get_clean();
        }
        else
        {
            throw new FormGeneratorException("Error no template file");
        }
    }
    
    /**
     * Parse the html part of the form
     * @param string $template
     * @return string 
     */
    private function generateHtmlForm($template = "")
    {
        if(is_file($this->_mConfigFile))
        {
            $this->parseConfigFile();
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
                $stream = $this->loadTemplate($template);

                return $this->placeFormElements($stream);
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
                
                $this->addElement(ElementFactory::creatElement($field), $label);
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
        if(session_id() != ""){
            $_SESSION["ofg"][$this->_mId]["object"] = serialize($this);
        }
    }
    
    /**
     * Place the fieldset and legends elemets into the html template
     * @param string $stream
     * @return string 
     */
    private function placeFieldsetElements($stream)
    {
        if(!empty($this->_mFieldset))
        {
            foreach($this->_mFieldset as $key => $element)
            {
                $element->build();
                $fieldset_parts = $element->getOpenAndCloseTag();
                
                $stream = str_replace("{%fieldset-" . $element->get_mId() . "%}", $fieldset_parts[0], $stream);
                $stream = str_replace("{%/fieldset-" . $element->get_mId() . "%}", $fieldset_parts[1], $stream);
                
                if(isset($this->_mLegends[$key]))
                {
                    $tag = "{%legend-" . $element->get_mId() . "%}";
                    $stream = str_replace($tag, $this->_mLegends[$key]->build(), $stream);
                }
            }
        }
        return $stream;
    }
    
    /**
     * Place the form and labels elemets into the html template
     * @param string $stream
     * @return string 
     */
    private function placeFormElements($stream)
    {
        $stream = $this->placeFieldsetElements($stream);
        
        if(!empty($this->_mElements))
        {
            foreach($this->_mElements as $key => $element)
            {
                $stream = str_replace("{%" . $element->get_mId() . "%}", $element->build(), $stream);
                if(isset($this->_mLabels[$key]))
                {
                    $tag = "{%label-" . $element->get_mId() . "%}";
                    $stream = str_replace($tag, $this->_mLabels[$key]->build(), $stream);
                }
            }
        }
        
        return $stream;
    }
    
    /**
     * Initialize a FormElement object
     */
    private function iniForm()
    {
        if(isset($this->_mFormData['form']) && is_array($this->_mFormData['form']))
        {
            $this->_mformElement = new FormElement($this->_mFormData['form']);
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
        $this->_mReadOnly = $_mReadOnly;
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
        
    /** Static methods **/
    
    /**
     * Check if the form data is valid
     */
    public static function isValid()
    {
        
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
    
}
