<?php
namespace FAA;
use Softwarefactories\AndromedaCore\Rest\HttpFunctions as BaseHttpFunctions;

/**
 * Define common request helper functions
 *
 * @category   HelperClass
 * @package    Core
 * @author     Tobias Teichner <webmaster@teichner.biz>
 * @since      File available since v2.18.0
 **/
class HttpFunctions extends BaseHttpFunctions
{
    /**
     * Check if the call is valid and authorized
     *
     * @param null $host
     * @return bool
     */
    public static function CheckHeaderAuth($host = null): bool
    {
        if (!FAA_CERT_VALIDATION_ACTIVE) {
            return true;
        } else {
            $info = self::GetHeaderAuth();
            if ($info) {
                if ($info['login'] === $host && is_file(FAA_PATHS_ROOT_ABS . '/' . $host . '/.pass')) {
                    // custom host specific password
                    $k = trim(file_get_contents(FAA_PATHS_ROOT_ABS . '/' . $host . '/.pass'));
                    return (password_verify($info['password'], $k));
                } else if ($info['login'] == 'service' && ($info['password'] == FAA_MASTER_PASSWORD || password_verify($info['password'], FAA_MASTER_PASSWORD))) {
                    // global password
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
