<?php

namespace FormGenerator\FormGenerator;

use FormGenerator\FormGenerator\CheckConfigFile;
use FormGenerator\FormParser\ParserFactory;
use FormGenerator\FormGeneratorCache\FormGeneratorCache;

/**
 * Description of FormGeneratorConfigLoader
 *
 * @author Joana
 */
class FormGeneratorConfigLoader {
    
    /**
     * Array of form configurations
     * @var array 
     */
    protected $configs;
    
    /**
     * array Form configuration File name
     * @var string 
     */
    protected $configFile;
    
    /**
     * The loaded configurations from the config file
     * @var array 
     */
    protected $loadedConfigs;
    
    /**
     * Form configuration directory path
     * @var string 
     */
    protected $configDirectoryPath;
    
    /**
     * Template form directory path
     * @var string 
     */
    protected $templateDirectoryPath = "";
    
    /**
     * Path for translations
     * @var boolean 
     */
    protected $translationDirectoryPath;
    
    public function __construct(array $configs = array()) {
        $this->configs["configs"] = $configs;
    }
    
    /**
     * Process and configure form configurations
     */
    public function configure()
    {
        $this->setConfigsPathAndFiles();
        $this->loadConfigFile();
        $this->mergeConfigsArguments();
        $this->parseRootPath();
        $this->setArgumentsConfig();
    }
    
    /**
     * Load if exists the config file for the form
     * @throws FormGeneratorException
     */
    protected function loadConfigFile() {
        if($this->configFile !== FormConfig::getDefaultConfigFile()){
            $this->parseConfigFile();
            CheckConfigFile::check($this->loadedConfigs);
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
    protected function parseConfigFile()
    {
        $file_type = pathinfo($this->configFile, PATHINFO_EXTENSION);
        $parser = ParserFactory::getParserInstance($file_type);
        /* @var $parser FormGenerator\FormParser\IFormParser */
        $this->loadedConfigs = $parser::parse($this->configFile);
    }
    
    /**
     * Set the configurations files and path. If nothing is defined, the default values are loaded.
     * @param array $args Array of arguments
     */
    protected function setConfigsPathAndFiles() 
    {
        $config_dir = (isset($this->configs["configs"]["configDir"])) ? $this->configs["configs"]["configDir"] : "";
        $this->defineTheConfigDirectory($config_dir);
        $config_file = (isset($this->configs["configs"]["configFile"])) ? $this->configs["configs"]["configFile"] : "";
        $this->defineTheConfigFile($config_file);
        
    }
    
    protected function setArgumentsConfig()
    {
        $config_pointer = $this->loadedConfigs["configs"];
        $cache_path = (isset($config_pointer["cacheDir"])) ? $config_pointer["cacheDir"] : "";
        $this->defineCacheDirectory($cache_path);
        $templateDir = (isset($config_pointer["templateDir"])) ? $config_pointer["templateDir"] : "";
        $this->defineTemplateDirectory($templateDir);
        $translationsDir = (isset($config_pointer["translationsDir"])) ? $config_pointer["translationsDir"] : "";
        $this->setTranslationDirectoryPath($translationsDir);
        unset($config_pointer);
    }
    
    /**
     * Add configs arguments from a configuration file to the configurations passed to this object.
     * The arguments are then checked and validated.
     */
    protected function mergeConfigsArguments() {
        
        $extra_args = array();
        
        if(isset($this->loadedConfigs["configs"]) && is_array($this->loadedConfigs["configs"])) {
            $extra_args = $this->loadedConfigs["configs"];
        }
        
        $this->loadedConfigs["configs"] = array_merge($this->configs["configs"], $extra_args);
    }
    
    /**
     * Define the configuration files directory
     * @param string $configDirectoryPath 
     */
    protected function defineTheConfigDirectory($configDirectoryPath="")
    {
        if(!empty($configDirectoryPath))
        {
            $this->configDirectoryPath = $configDirectoryPath;
        }
        else
        {
            $this->configDirectoryPath = FormConfig::getConfigDir();
        }
    }
    
    /**
     * Define the configuration file, let the class know where to look for external informations
     * about the form
     * @param string $configFile 
     */
    protected function defineTheConfigFile($configFile="")
    {
        if(!empty($configFile))
        {
            if(is_string($configFile) && is_file($this->configDirectoryPath . $configFile))
            {
                $this->configFile = $this->configDirectoryPath . $configFile;
            }
            else
            {
                throw new FormGeneratorException("Config file not found. No such file : " . $this->configDirectoryPath . $configFile);
            }
        }
        else if ($configFile === "")
        {
            $this->configFile = FormConfig::getDefaultConfigFile();
        }
    }
    
    /**
     * Define the directory where to store the cache
     * @param string $cacheDir 
     */
    protected function defineCacheDirectory($cacheDir = "")
    {
        FormGeneratorCache::setCachePath($cacheDir);
    }
    
    /**
     * Define the template directory
     * @param type $templateDirPath 
     */
    protected function defineTemplateDirectory($templateDirPath = "")
    {
        if(!empty($templateDirPath))
        {
            $this->templateDirectoryPath = $templateDirPath;
        }
        else
        {
            $this->templateDirectoryPath = __DIR__;
        }
    }
    
    
    
    /**
     * Parse the root path defined in all configurations
     */
    protected function parseRootPath() {
        
        $root_dir = "";
        if(array_key_exists("rootDir", $this->loadedConfigs["configs"])) {
            $root_dir = str_replace("%DIR%", __DIR__, $this->loadedConfigs["configs"]["rootDir"]);
        }
        
        foreach ($this->loadedConfigs["configs"] as $key => $config) {
            if(is_string($config)) {
                if(strstr($config, "%DIR%")){
                    $this->loadedConfigs["configs"][$key] = str_replace("%DIR%", __DIR__, $config);
                }
                if(strstr($config, "%ROOT%")){
                    $this->loadedConfigs["configs"][$key] = str_replace("%ROOT%", $root_dir, $config);
                }
            }
        }
    }
    
    /**
     * Get the config file name
     * @return string 
     */
    public function getConfigFile() {
        return $this->configFile;
    }

    /**
     * Set the config file name
     * @param string $configFile
     * @return string 
     */
    public function setConfigFile($configFile) {
        $this->configFile = $configFile;
    }
    
    /**
     * Get the loaded configurations from the config file
     * @return array
     */
    public function getLoadedConfigs() {
        return $this->loadedConfigs;
    }
    
    /**
     * Get the form element configurations
     * @return array
     */
    public function getFormElementConfigs()
    {
        return $this->loadedConfigs["form"];
    }
    
    /**
     * Get the translations path
     * @return string
     */
    public function getTranslationDirectoryPath() {
        return $this->translationDirectoryPath;
    }

    /**
     * Set the translations path
     * @param string $translations_path
     */
    public function setTranslationDirectoryPath($translations_path) {
        $this->translationDirectoryPath = $translations_path;
    }

    /**
     * Get the template directory path
     * @return string
     */
    public function getTemplateDirectoryPath() {
        return $this->templateDirectoryPath;
    }
}
