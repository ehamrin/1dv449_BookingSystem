<?php


class URL
{
    public static function concatenate(...$urls){

        $string = '';
        foreach($urls as $url){

            if(substr($string,-1)  == '/' && substr($url,0,1) == '/'){

                $string = rtrim($string, '/') . $url;
            }elseif(substr($string,-1)  != '/' && substr($url,0,1) != '/' && $string != ''){
                $string .= '/' . $url;

            }else{
                $string .= $url;
            }
        }
        return $string;
    }

    public static function validate($url){
        return filter_var($url, FILTER_VALIDATE_URL);
    }

}