<?php

Cron_Autoloader::Register();
//    As we always try to run the autoloader before anything else, we can use it to do a few
//        simple checks and initialisations
//Cron_Shared_ZipStreamWrapper::register();
// check mbstring.func_overload
if (ini_get('mbstring.func_overload') & 2) {
    throw new Cron_Exception('Multibyte function overloading in PHP must be disabled for string functions (2).');
}


/**
 * Cron_Autoloader
 *
 * @category    Cron
 * @package     Cron
 */
class Cron_Autoloader
{
    /**
     * Register the Autoloader with SPL
     *
     */
    public static function Register() {
        if (function_exists('__autoload')) {
            //    Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__autoload');
        }
        //    Register ourselves with SPL
        return spl_autoload_register(array('Cron_Autoloader', 'Load'));
    }   //    function Register()


    public static function Load($pClassName){

        if ((class_exists($pClassName,FALSE))) {
            //    Either already loaded
            return FALSE;
        }

        $pClassFilePath = Cron_ROOT .
                          str_replace('_',DIRECTORY_SEPARATOR,$pClassName) .
                          '.php';

        if ((file_exists($pClassFilePath) === FALSE) || (is_readable($pClassFilePath) === FALSE)) {
            //    Can't load
            return FALSE;
        }

        require($pClassFilePath);
    }

}
