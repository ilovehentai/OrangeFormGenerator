<?php

namespace FormGenerator;
use FormGenerator\FormGeneratorException\FormGeneratorCacheException;

class CacheClass{
    
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
     * Build cache file
     */
    public static function iniCache()
    {
        if(!is_dir(self::$_cache_path))
        {
            if(!mkdir(self::$_cache_path))
            {
                throw new FormGeneratorCacheException("Cache building error no such path: " . self::$_cache_path);
            }
        }
    }
    
    /**
     * Save data in the cache file
     * @param string $file
     * @param string $data
     * @param boolean $asUniq 
     */
    public static function saveDataFile($file, $data, $asUniq = false)
    {
        file_put_contents(self::$_cache_path . $file, $data);
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
     * Remove unused cached files
     * @param string $idForm 
     */
    public static function clearFileCache($idForm)
    {
        if(is_dir(self::$_cache_path))
        {
            if (($handle = opendir(self::$_cache_path))){
                while (false !== ($file = readdir($handle))) {
                    if (preg_match("/^" . $idForm . "_([0-9abcdef])+\.php$/", $file)) {
                        unlink(self::$_cache_path . $file);
                    }
                }
                closedir($handle);
            }
        }
    }
    
    public static function expectedCacheName($idForm, $config_file, $template_file)
    {
        return $idForm . "_" . md5(md5_file($config_file) . md5_file($template_file));
    }
}
