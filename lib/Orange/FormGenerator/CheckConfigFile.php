<?php

namespace FormGenerator;

class CheckConfigFile{
    
    public static $result;
    
    /**
     * Verify in configuration file if fields exists and if each field has an id.
     * @param array $config 
     */
    public static function check(array $config)
    {
        self::$result = "";
        
        if(!empty($config))
        {
            if(array_key_exists("fields", $config))
            {
                foreach($config['fields'] as $key => $field)
                {
                    if(!array_key_exists("id", $field) || empty($field['id']))
                    {
                        self::$result .= "No field id found at position $key\n";
                    }
                    else
                    {
                        if(self::loopForDuplicates($field['id'], $config['fields'], $key))
                        {
                            self::$result .= "Found duplicate field id: {$field['id']} at position: $key\n";
                        }
                    }
                }
            }
            else
            {
                self::$result = "No fields declared!";
            }
        }
        else
        {
            self::$result = "No data in config file!";
        }
    }
    
    /**
     * Check for duplicates id in configuration files
     * @param string $key_id
     * @param array $list_of_keys
     * @param int $ignore_position
     * @return boolean 
     */
    private static function loopForDuplicates($key_id, array $list_of_keys, $ignore_position)
    {
        $founded = false;
        if(!empty($list_of_keys))
        {
            $c = 0;
            while($c < count($list_of_keys) && $founded === false)
            {
                
                if(array_key_exists("id", $list_of_keys[$c]) 
                            && $list_of_keys[$c]['id'] == $key_id && $ignore_position != $c)
                {
                    $founded = true;
                }
                $c++;
            }
        }
        
        return $founded;
    }
}
