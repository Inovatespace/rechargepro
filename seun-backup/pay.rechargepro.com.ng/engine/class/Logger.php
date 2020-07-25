<?php
class Logger extends Exception
{

    const MISSING_PARAMETERS = '1';
    const VERSION_NOT_FOUND = '2';
    const SERVICE_NOT_FOUND = '3';
    const FUNCTION_NOT_FOUND = '4';
    const ACTION_NOT_FOUND = '5';
    const REQUEST_METHOD_DISABLED = '6';
    const UNAUTHORIZED = '7';
    const TO_MANY_REQUESTS = '8';


    private $_errorVal = array(
        '1' => 'Required parameters: %s',
        '2' => 'Version with name "%s" is not found.',
        '3' => 'Service with name "%s" is not found.',
        '4' => 'Function with name "%s" is not found.',
        '5' => 'Action with name "%s" is not found in service "%s".',
        '6' => 'The used HTTP request method "%s" is not allowed for the action.',
        '7' => 'You are not authorized to do this action.',
        '8' => 'Max request per time unit reached.');


    public function __construct($config = array(), $name = '', $message = '')
    {
        $this->config = $config;

        if (isset($this->_errorVal[$name])) {
            $message = sprintf($this->_errorVal[$name], $message);
        }

        parent::__construct($message);

    }


    public function enable_display_error($enabled = false)
    {
        ini_set('display_errors', $enabled);
    }

    public function enable_error()
    {
        $this->_max_type = E_NOTICE;
        set_error_handler(array(&$this, 'handle_errors'));
    }


    public function enable_fatal($enabled = true)
    {
        register_shutdown_function(array(&$this, 'handle_shutdown'));
    }


    public function enable_exception($enabled = true)
    {
        set_exception_handler(array(&$this, 'handle_exception'));
    }

    public function enable_method_file($enabled = true)
    {
        $this->_enabled_file = $enabled;

        $path = !isset($this->config['path']) ? '.' : $this->config['path'];
        if ($path == '.')
            $path = dirname(__file__);
        $this->_config_file['path'] = $path;
        $this->_config_file['file'] = $this->clean_path($path) . $this->create_filename();
    }


    private static function file_write($value, $path)
    {
        if (!file_exists($path)){
            self::file_create($path);
            }
            
            if(file_exists($path)){
        $fh = fopen($path, 'a');
        fwrite($fh, $value);
        fclose($fh);
        }
    }


    private static function file_create($path)
    {
        $fh = @fopen($path, 'w');
        if ($fh == null)
            return false;
        fwrite($fh, "<?php exit() ?> \n");
        fclose($fh);
        if (file_exists($path))
            return true;
        return false;
    }


    public function handle_errors($type, $message, $file, $line, $context)
    {
        if ($this->_max_type >= $type) {
            $data['%message%'] = $message;
            $data['%file%'] = $file;
            $data['%line%'] = $line;
            $data['%type%'] = "Notice";
            $line = $this->prepare_line($data);
            $this->send($line);
        }
    }


    public function handle_exception($exception)
    {

        $trace = $exception->getTrace();
        foreach ($trace as $key => $stackPoint) {

            $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
        }
        $result = array();
        foreach ($trace as $key => $stackPoint) {
            $result[] = sprintf($key, $stackPoint['file'], $stackPoint['line'], $stackPoint['function'],
                implode(', ', $stackPoint['args']));
        }

        print $_SERVER['REMOTE_ADDR'] . "- [" . date("m/d/y") . " " . date('H:i:s') .
            "] Exception: " . $exception->getMessage();
        $data['%message%'] = $exception->getMessage();
        $data['%file%'] = $exception->getFile();
        $data['%line%'] = $exception->getLine();
        $data['%type%'] = 'Exception';
        $line = $this->prepare_line($data);
        $this->send($line);
    }


    public function handle_shutdown()
    {
        if (!function_exists('error_get_last'))
            return;
        $error = error_get_last();
        if ($error !== null) {
            $data['%message%'] = $error['message'];
            $data['%file%'] = $error['file'];
            $data['%line%'] = $error['line'];
            $data['%type%'] = 'Fatal';
            $line = $this->prepare_line($data);
            $this->send($line);
        }
    }


    public function log($value)
    {
        if (!is_string($value) && !is_int($value)) {
            $value = print_r($value, true);
        }

        // Get the line and file where the log is called
        $trace = debug_backtrace();

        $data['%message%'] = $value;
        $data['%file%'] = $trace[0]['file'];
        $data['%line%'] = $trace[0]['line'];
        $data['%type%'] = 'Log';
        $line = $this->prepare_line($data);
        $this->send($line);
    }


    private function prepare_line($params)
    {
        $find = array(
            '%ip%',
            '%date%',
            '%time%');
        $replace = array(
            $_SERVER['REMOTE_ADDR'],
            date("m/d/y"),
            date('H:i:s'));
        $line = str_replace($find, $replace,
            '%ip% - %date% %time% @ %type% # %message% * %file% $ %line% |');
        foreach ($params as $find => $replace) {
            $line = str_replace($find, $replace, $line);
        }
        return $line . "\r\n";
    }


    private function create_filename()
    {
        return date('Ymd') . '.php';
    }


    private function clean_path($path)
    {
        $clean_path = str_replace('\\', '/', $path);
        return substr($clean_path, -1) == '/' ? $clean_path : $clean_path . '/';
    }


    public function send($line)
    {

        // Log file
        if ($this->_enabled_file) {
            $this->file_write($line, $this->_config_file['file']);
        }


    }


    public function special_log($service = "", $response = "", $transactionid = "")
    {
        if (is_array($response)) {
            $response = implode(",", $response);
        }

        $stringData = "Service - ".date("Y/m/d H:i:s")." @ Charms # $response * $service $ $transactionid\r\n";

        $path = !isset($this->config['path']) ? '.' : $this->config['path'];
        $path = $this->clean_path($path) . "/charms_" . date('Ymd') . ".php";
        if (file_exists($path)) {
            self::file_write($stringData, $path);
            return true;
        } else {
            self::file_create($path);
            self::file_write($stringData, $path);
        }
        return false;
    }

}

?>