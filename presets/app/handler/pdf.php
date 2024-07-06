<?php
namespace FAA\Handler;
use FAA\HttpFunctions;
use FAA\Interfaces\IHandler;
use FAA\Objects\{Request, Result};

/**
 * Class FileParser
 *
 * @package FAA\Handler
 */
class Pdf implements IHandler
{
    /**
     * The request context
     *
     * @var Request
     */
    private $request;

    /**
     * Derivative constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function Name()
    {
        return 'Pdf';
    }

    public function Execute(): Result
    {
        $res = new Result();
        if ($this->request->CallBack() === 'Html2Pdf') {
            $res = $this->Html2Pdf();
        } else {
            $res->code = 405;
        }
        return $res;
    }

    /**
     * Convert an email to json and return the result
     *
     * @return Result
     */
    private function Html2Pdf(): Result
    {
        $res = new Result();
        $body = HttpFunctions::GetBody();
        if (isset($body['content']) && $body['content']) {
            // Prepare som source and target variables with paths
            $n = md5(microtime(true));
            $temp = sys_get_temp_dir() . '/' . $n;
            $in = $temp . '/data.html';
            $out = $temp . '/data.pdf';
            $root = sys_get_temp_dir() . '/' . $n;

            // Create the working directory
            if (is_dir($root) || mkdir($root)) {
                if (file_put_contents($in, $body['content'])) {
                    // Convert to json
                    $cmd = 'wkhtmltopdf ' . $in . ' ' . $out;
                    error_log("[DEBUG] Start convert with command $cmd");
                    if (HttpFunctions::CallExecutable($cmd)) {
                        // Return the results as json
                        $res->code = 201;
                        $res->type = 'application/pdf';
                        $res->msg = file_get_contents($out);
                        $res->isDownload = true;

                        // Clear the folder and return true
                        unlink($in);
                        unlink($out);
                        rmdir($temp);
                    } else {
                        $res->code = 500;
                        $res->msg = 'system.msg.failed_parse_file';
                    }
                } else {
                    $res->code = 500;
                    $res->msg = 'system.msg.failed_write_file';
                }
            } else {
                $res->code = 500;
                $res->msg = 'system.msg.failed_write_file';
            }
        } else {
            $res->code = 400;
            $res->msg = 'system.msg.invalid_payload';
        }
        return $res;
    }
}
