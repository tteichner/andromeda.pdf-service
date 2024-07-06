<?php
/**
 * Implement the asset functions handling
 *
 * POST /storage/{storage-id}/assets/{id}/derivative/{content-hash}/{derivative-hash}.{ext}
 * %binary file%
 *
 * @category   HelperClass
 * @package    Core
 * @author     Tobias Teichner <webmaster@teichner.biz>
 * @since      File available since v2.18.0
 **/
namespace FAA;
use FAA\Objects\Request;

// import the system config
require_once 'cfg.php';

// extract endpoint params
$uri = $_SERVER['REQUEST_URI'];
$method = strtoupper($_SERVER['REQUEST_METHOD']);
$request = new Request($map, $method, $uri);

// check which page is required
if ($handler = $request->Handler()) {
    $p = "{$request->Handler()->Name()}::{$request->CallBack()}";
    if ('Asset::Progress' != $p) {
        error_log("[DEBUG] Execute $p");
    }

    // is handled by dedicated handler
    if ($request->IsProtected() && !HttpFunctions::CheckHeaderAuth($request->Host())) {
        // no access
        HttpFunctions::SetResponseCode(403);
        echo 'system.msg.invalid_cert';
    } else {
        // handle and sent the response
        $res = $handler->Execute();
        HttpFunctions::SetResponseCode($res->code);
        if ($res->isFile) {
            // Mark as attachment
            header('Content-Disposition: attachment; filename=out.'.preg_replace('#^.*/#', '', $res->type).';');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
        } else {
            header('Content-Type: ' . $res->type);
        }
        echo $res->msg;
    }
} else {
    error_log('[WARN] Not found: ' . $method.' ' . $uri);
    HttpFunctions::SetResponseCode(404);
    echo 'system.msg.not_found';
}
