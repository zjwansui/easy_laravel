<?php


namespace Zjwansui\EasyLaravel\Tools;


use Illuminate\Support\Facades\Log;

class CaptureLog
{
    private array $startingFields;
    private string $filename;

    /**
     * @throws \JsonException
     */
    public function __construct($header, $body, $response)
    {
        $dir = storage_path('logs/capture/' . date('Y') . '/' . date('m') . '/');
        $this->filename = $dir . date('d') . '.log';
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        Log::info($_SERVER['REQUEST_URI']);
        if ($_SERVER['REQUEST_URI'] === '/api/documentation' || strpos($_SERVER['HTTP_REFERER'] ?? '', 'documentation') !== false) {
            $response = 'docs';
        } elseif (!is_array($response)) {
            try {
                $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            }catch (\Exception $exception){
            }
        }

        $this->startingFields = [
            'header' => $header,
            'request' => $body,
            'response' => $response
        ];
        $this->_start();
    }

    /**
     * @throws \JsonException
     */
    private function _start(): void
    {
        $this->_log($this->_getStartWith($this->startingFields));
    }

    /**
     * @throws \JsonException
     */
    private function _getStartWith(array $fields): string
    {
        $date = date('Y-m-d H:i:s');
        $ip = $_SERVER['HTTP_X_REAL_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
        $method = PHP_SAPI === 'cli' ? 'cli' : ($_SERVER['REQUEST_METHOD'] ?? 'unknown');
        $other = $this->_prepareFields($fields);

        return <<<EOF
+-----------------------------------------------------------
                      {$date}
+-----------------------------------------------------------
 |   Client  : {$ip}
 |   Method  : {$method}{$other}
EOF;
    }


    private function _prepareFields($fields): string
    {

        $log = '';
        if ($fields && is_array($fields)) {
            $log .= <<<EOF
\n
EOF;
            foreach ($fields as $k => $v) {
                if (!is_scalar($k)) {
                    continue;
                }
                if (is_array($v)) {
                    $vData = '';
                    array_walk($v,
                        /**
                         * @throws \JsonException
                         */
                        static function ($value, $key) use (&$vData) {
                            if (is_array($value)) {
                                if ($key === 'trace' && is_array($value[0])) {
                                    return;
                                }
                                $vData .= $key . ':' . json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE) . "\n";
                            } else {
                                $vData .= $key . ':' . $value . "\n";
                            }
                        });

                    $v = $vData;
                }
                $k = str_replace('_', ' ', ucwords($k, '_'));
                $log .= <<<EOF
 |   $k :  \n
$v\n
EOF;

            }
            $log = rtrim($log, "\n");
        }
        return $log;
    }

    private function _log(string $string): void
    {
        $this->_write($string);
    }

    private function _write(string $string): void
    {
        $handler = fopen($this->filename, 'ab');
        fwrite($handler, "$string\n");
        fclose($handler);
    }

}
