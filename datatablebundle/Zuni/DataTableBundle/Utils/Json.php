<?php

namespace Zuni\DataTableBundle\Utils;

/**
 *
 * @author Fábio Lemos Elizandro
 */
class Json
{   
    
    private static function isAssoc(array $array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
    
    /**
     * Codifica um json
     * 
     * 
     * @param array $arrayJson
     * @return string json
     */
    public static function encode(array $arrayJson)
    {
        $json = "" ;
        if(self::isAssoc($arrayJson)){
            $json .= "{" ;
            foreach($arrayJson as $key => $value){
                if(!is_array($value)){
                    $value = is_string($value) ? addslashes($value): $value;
                    $json .= '"'.$key.'":' ;
                    $json .= '"'.$value.'",' ;
                }else{
                    $json .= '"'.$key.'":' ;
                    $json .= self::encode($value).",";
                }
            }
            $json = substr_replace($json, "}", -1);
        }else{
            $json .= "[ " ;
            foreach($arrayJson as $value){
                if(!is_array($value)){
                    $value = is_string($value) ? addslashes($value): $value;
                    if(is_numeric($value)){
                        $json .= $value.',' ;
                    }else{
                        $json .= '"'.$value.'",' ;
                    }
                    
                }else{
                    $json .= self::encode($value).",";
                }
            }
            $json = substr_replace($json, "]", -1);
        }
        return $json ;
    }
    
    /**
     * decodifica o json
     * 
     * @param string $json
     * @return array
     */
    public static function decode($json)
    {
        return json_decode($json);
    }
    
}
