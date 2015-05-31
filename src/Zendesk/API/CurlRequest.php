<?php
namespace Zendesk\API;

/**
 * Curl mock functions
 * @package Zendesk\API
 */
class CurlRequest
{
    protected $handle = null;

    public function __construct($url = null)
    {
        $this->handle = curl_init($url);
    }

    public function close()
    {
        return curl_close($this->handle);
    }

    public function copy_handle()
    {
        return curl_copy_handle($this->handle);
    }

    public function errno()
    {
        return curl_errno($this->handle);
    }

    public function error()
    {
        return curl_error($this->handle);
    }

    public function escape($str)
    {
        return curl_escape($this->handle, $str);
    }

    public function exec()
    {
        return curl_exec($this->handle);
    }

    public function file_create($filename, $mimetype = null, $postname = null)
    {
        return curl_file_create($filename, $mimetype, $postname);
    }

    public function getinfo($opt = 0)
    {
        return curl_getinfo($this->handle, $opt);
    }

    public function pause($bitmask)
    {
        return curl_pause($this->handle, $bitmask);
    }

    public function reset()
    {
        return curl_reset($this->handle);
    }

    public function setopt_array($options)
    {
        return curl_setopt_array($this->handle, $options);
    }

    public function setopt($option, $value)
    {
        return curl_setopt($this->handle, $option, $value);
    }

    public function strerror($errornum)
    {
        return curl_strerror($errornum);
    }

    public function unescape($str)
    {
        return curl_unescape($this->handle, $str);
    }

    public function version($age = CURLVERSION_NOW)
    {
        return curl_version($age);
    }
}
