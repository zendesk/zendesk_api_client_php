<?php

namespace Zendesk\API\UnitTests;

class MockCurlRequest
{
    protected $handle = null;
    private $options = [];
    private $constants = [];

    public function __construct($url = null)
    {
        foreach ([
                     'CURLINFO_HEADER_OUT',
                     'CURLOPT_AUTOREFERER',
                     'CURLOPT_COOKIESESSION',
                     'CURLOPT_DNS_USE_GLOBAL_CACHE',
                     'CURLOPT_DNS_CACHE_TIMEOUT',
                     'CURLOPT_FTP_SSL',
                     'CURLOPT_PRIVATE',
                     'CURLOPT_FTPSSLAUTH',
                     'CURLOPT_PORT',
                     'CURLOPT_FILE',
                     'CURLOPT_INFILE',
                     'CURLOPT_INFILESIZE',
                     'CURLOPT_URL',
                     'CURLOPT_PROXY',
                     'CURLOPT_VERBOSE',
                     'CURLOPT_HEADER',
                     'CURLOPT_HTTPHEADER',
                     'CURLOPT_NOPROGRESS',
                     'CURLOPT_NOBODY',
                     'CURLOPT_FAILONERROR',
                     'CURLOPT_UPLOAD',
                     'CURLOPT_POST',
                     'CURLOPT_FTPLISTONLY',
                     'CURLOPT_FTPAPPEND',
                     'CURLOPT_FTP_CREATE_MISSING_DIRS',
                     'CURLOPT_NETRC',
                     'CURLOPT_FOLLOWLOCATION',
                     'CURLOPT_PUT',
                     'CURLOPT_USERPWD',
                     'CURLOPT_PROXYUSERPWD',
                     'CURLOPT_RANGE',
                     'CURLOPT_TIMEOUT',
                     'CURLOPT_TIMEOUT_MS',
                     'CURLOPT_TCP_NODELAY',
                     'CURLOPT_POSTFIELDS',
                     'CURLOPT_PROGRESSFUNCTION',
                     'CURLOPT_REFERER',
                     'CURLOPT_USERAGENT',
                     'CURLOPT_FTPPORT',
                     'CURLOPT_FTP_USE_EPSV',
                     'CURLOPT_LOW_SPEED_LIMIT',
                     'CURLOPT_LOW_SPEED_TIME',
                     'CURLOPT_RESUME_FROM',
                     'CURLOPT_COOKIE',
                     'CURLOPT_SSLCERT',
                     'CURLOPT_SSLCERTPASSWD',
                     'CURLOPT_WRITEHEADER',
                     'CURLOPT_SSL_VERIFYHOST',
                     'CURLOPT_COOKIEFILE',
                     'CURLOPT_SSLVERSION',
                     'CURLOPT_TIMECONDITION',
                     'CURLOPT_TIMEVALUE',
                     'CURLOPT_CUSTOMREQUEST',
                     'CURLOPT_STDERR',
                     'CURLOPT_TRANSFERTEXT',
                     'CURLOPT_RETURNTRANSFER',
                     'CURLOPT_QUOTE',
                     'CURLOPT_POSTQUOTE',
                     'CURLOPT_INTERFACE',
                     'CURLOPT_KRB4LEVEL',
                     'CURLOPT_HTTPPROXYTUNNEL',
                     'CURLOPT_FILETIME',
                     'CURLOPT_WRITEFUNCTION',
                     'CURLOPT_READFUNCTION',
                     'CURLOPT_HEADERFUNCTION',
                     'CURLOPT_MAXREDIRS',
                     'CURLOPT_MAXCONNECTS',
                     'CURLOPT_CLOSEPOLICY',
                     'CURLOPT_FRESH_CONNECT',
                     'CURLOPT_FORBID_REUSE',
                     'CURLOPT_RANDOM_FILE',
                     'CURLOPT_EGDSOCKET',
                     'CURLOPT_CONNECTTIMEOUT',
                     'CURLOPT_CONNECTTIMEOUT_MS',
                     'CURLOPT_SSL_VERIFYPEER',
                     'CURLOPT_CAINFO',
                     'CURLOPT_CAPATH',
                     'CURLOPT_COOKIEJAR',
                     'CURLOPT_SSL_CIPHER_LIST',
                     'CURLOPT_BINARYTRANSFER',
                     'CURLOPT_NOSIGNAL',
                     'CURLOPT_PROXYTYPE',
                     'CURLOPT_BUFFERSIZE',
                     'CURLOPT_HTTPGET',
                     'CURLOPT_HTTP_VERSION',
                     'CURLOPT_SSLKEY',
                     'CURLOPT_SSLKEYTYPE',
                     'CURLOPT_SSLENGINE',
                     'CURLOPT_SSLENGINE_DEFAULT',
                     'CURLOPT_SSLCERTTYPE',
                     'CURLOPT_CRLF',
                     'CURLOPT_ENCODING',
                     'CURLOPT_PROXYPORT',
                     'CURLOPT_UNRESTRICTED_AUTH',
                     'CURLOPT_FTP_USE_EPRT',
                     'CURLOPT_HTTP200ALIASES',
                     'CURLOPT_HTTPAUTH',
                     'CURLOPT_PROXYAUTH',
                     'CURLOPT_MAX_RECV_SPEED_LARGE',
                     'CURLOPT_MAX_SEND_SPEED_LARGE'
                 ] as $k) {
            $this->constants[constant($k)] = $k;
        }
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
        return json_encode($this->getopt_array());
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
        $this->options = $options;

        return curl_setopt_array($this->handle, $options);
    }

    public function setopt($option, $value)
    {
        //$this->options[ (isset($this->constants[$option]) ? $this->constants[$option] : $option) ] = $value;
        $this->options[$option] = $value;

        return curl_setopt($this->handle, $option, $value);
    }

    public function getopt_array()
    {
        return $this->options;
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
