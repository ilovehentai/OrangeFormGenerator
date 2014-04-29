<?php

namespace FormGenerator\FormGeneratorCache;
use FormGenerator\FormGenerator\FormConfig;
use FormGenerator\FormGeneratorException\FormGeneratorCacheException;

class FormGeneratorCache{
    
    public static $_cache_path;
    
    /**
     * Set the cache path
     * @param string $path 
     */
    public static function setCachePath($path = "")
    {
        if(!empty($path))
        {
            self::$_cache_path = $path;
        }
        else
        {
            self::$_cache_path = FormConfig::getDefaultCacheDir();
        }
        
    }   
    
    /**
     * Initialize cache file path
     */
    public static function iniCache()
    {
        if(!is_dir(self::$_cache_path))
        {
            if(!mkdir(self::$_cache_path))
            {
                throw new FormGeneratorCacheException("Cache building error, error on open or create path: " . self::$_cache_path);
            }
        }
    }
    
    /**
     * Save data in the cache file
     * @param string $file
     * @param string $data
     */
    public static function saveDataFile($file, $data)
    {
        if(!file_put_contents(self::$_cache_path . $file, $data))
        {
            throw new FormGeneratorCacheException("Unable to write cache file: " . self::$_cache_path . $file);
        }
    }
    
    /**
     * Checks whether a cache file exists
     * @param type $file
     * @return boolean 
     */
    public static function checkCacheFile($file)
    {
        $result = false;
        
        if(is_file(self::$_cache_path . $file))
        {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Get cache content
     * @param string $file
     * @return boolean
     */
    public static function getCacheContent($file)
    {
        if(self::checkCacheFile($file))
        {
            return file_get_contents(self::$_cache_path . $file);
        }
        
        return false;
    }
    
    /**
     * Remove unused cached files for a specific form
     * @param string $idForm 
     */
    public static function clearFileCache($idForm)
    {
        if(is_dir(self::$_cache_path))
        {
            foreach (new \DirectoryIterator(self::$_cache_path) as $dir) {
                if ($dir->isDot()) {
                    continue;
                }
                static::unlinkCacheFile($idForm, $dir);
            }
        }
    }
    
    /**
     * Generate an expected cache file name for a form from his config file content 
     * and template file content
     * If any of those two files contents changes a new cache name will be generated
     * indicating that content for that form has changed.
     * @param string $idForm
     * @param string $config_file
     * @param string $template_file
     * @return string
     */
    public static function expectedCacheName($idForm, $config_file, $template_file)
    {
        return $idForm . "_" . md5(md5_file($config_file) . md5_file($template_file));
    }
    
    /**
     * Delete the cache file from filesystem
     * @param string $idForm
     * @param \DirectoryIterator $dir
     */
    private static function unlinkCacheFile($idForm, \DirectoryIterator $dir)
    {
        if (preg_match("/^" . $idForm . "_([0-9abcdef])+$/",  $dir->getFilename())) {
            if(!unlink($dir->getRealPath()))
            {
                throw new FormGeneratorCacheException("Unable to delete cache file: " . $dir->getFilename());
            }
        }
    }
}
