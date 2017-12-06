<?php

namespace Zendesk\API;

/**
 * HTTP functions via curl
 * @package Zendesk\API
 */
class Http
{
    public static $curl;

    /**
     * Prepares an endpoint URL with optional side-loading
     *
     * @param string $endPoint
     * @param array $sideload
     * @param array $iterators
     *
     * @return string
     */
    public static function prepare($endPoint, array $sideload = null, array $iterators = null)
    {
        $addParams = array();
        // First look for side-loaded variables
        if (is_array($sideload)) {
            $addParams['include'] = implode(',', $sideload);
        }
        // Next look for special collection iterators
        if (is_array($iterators)) {
            foreach ($iterators as $k => $v) {
                if (in_array($k, array('per_page', 'page', 'sort_order', 'sort_by'))) {
                    $addParams[$k] = $v;
                }
            }
        }
        // Send it back...
        if (count($addParams)) {
            return $endPoint . (strpos($endPoint, '?') === false ? '?' : '&') . http_build_query($addParams);
        } else {
            return $endPoint;
        }
    }

    /**
     * Use the send method to call every endpoint except for oauth/tokens
     *
     * @param Client $client
     * @param string $endPoint
     * @param array $json
     * @param string $method
     * @param string $contentType
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public static function send(
        Client $client,
        $endPoint,
        $json = array(),
        $method = 'GET',
        $contentType = 'application/json'
    ) {
        $url = $client->getApiUrl() . $endPoint;
        $method = strtoupper($method);

        $curl = (isset(self::$curl)) ? self::$curl : new CurlRequest;
        $curl->setopt(CURLOPT_URL, $url);

        if ($method === 'POST') {
            $curl->setopt(CURLOPT_POST, true);

        } else {
            if ($method === 'PUT') {
                $curl->setopt(CURLOPT_CUSTOMREQUEST, 'PUT');

            } else {
                $st = http_build_query((array)$json);
                $curl->setopt(CURLOPT_URL,
                    $url . ($st !== array() ? (strpos($url, '?') === false ? '?' : '&') . $st : ''));
                $curl->setopt(CURLOPT_CUSTOMREQUEST, $method);
            }
        }

        $httpHeader = array('Accept: application/json');
        if ($client->getAuthType() == 'oauth_token') {
            $httpHeader[] = 'Authorization: Bearer ' . $client->getAuthText();

        } else {
            $curl->setopt(CURLOPT_USERPWD, $client->getAuthText());
        }

        /* DO NOT SET CONTENT TYPE IF UPLOADING */
        if (!isset($json['uploaded_data'])) {
            if (isset($json['filename'])) {
                $filename = $json['filename'];
                $file = fopen($filename, 'r');
                $size = filesize($filename);
                $fileData = fread($file, $size);
                $json = $fileData;
                $curl->setopt(CURLOPT_INFILE, $file);
                $curl->setopt(CURLOPT_INFILESIZE, $size);
            } else {
                if (isset($json['body'])) {
                    $curl->setopt(CURLOPT_INFILESIZE, strlen($json['body']));
                    $json = $json['body'];
                }
            }

            $httpHeader[] = 'Content-Type: ' . $contentType;
        } else {
            $contentType = '';
        }

        if ($contentType === 'application/json') {
            $json = json_encode($json);
        }

        $curl->setopt(CURLOPT_POSTFIELDS, $json);
        $curl->setopt(CURLOPT_HTTPHEADER, $httpHeader);
        $curl->setopt(CURLINFO_HEADER_OUT, true);
        $curl->setopt(CURLOPT_RETURNTRANSFER, true);
        $curl->setopt(CURLOPT_CONNECTTIMEOUT, 30);
        $curl->setopt(CURLOPT_TIMEOUT, 30);
        $curl->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setopt(CURLOPT_HEADER, true);
        $curl->setopt(CURLOPT_VERBOSE, true);
        $curl->setopt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setopt(CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        $curl->setopt(CURLOPT_MAXREDIRS, 3);

        $response = $curl->exec();
        if ($response === false) {
            throw new \Exception(sprintf('Curl error message: "%s" in %s', $curl->error(), __METHOD__));
        }
        $headerSize = $curl->getinfo(CURLINFO_HEADER_SIZE);
        $responseBody = substr($response, $headerSize);
        $responseObject = json_decode($responseBody);
        $client->setDebug(
            $curl->getinfo(CURLINFO_HEADER_OUT),
            $curl->getinfo(CURLINFO_HTTP_CODE),
            substr($response, 0, $headerSize),
            (isset($responseObject->error) ? $responseObject : null)
        );

        $responseCode = $client->getDebug()->lastResponseCode;

        if ($responseCode >= 400) {
            print($client->getDebug());

            throw new ResponseException(__METHOD__);
        }

        $curl->close();
        self::$curl = null;

        return $responseObject;
    }

    /**
     * Specific case for OAuth. Run /oauth.php via your browser to get an access token
     *
     * @param Client $client
     * @param string $code
     * @param string $oAuthId
     * @param string $oAuthSecret
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public static function oauth(Client $client, $code, $oAuthId, $oAuthSecret)
    {
        $url = 'https://' . $client->getSubdomain() . '.zendesk.com/oauth/tokens';
        $method = 'POST';

        $curl = (isset(self::$curl)) ? self::$curl : new CurlRequest;
        $curl->setopt(CURLOPT_URL, $url);
        $curl->setopt(CURLOPT_POST, true);
        $curl->setopt(CURLOPT_POSTFIELDS, json_encode(array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $oAuthId,
            'client_secret' => $oAuthSecret,
            'redirect_uri' => ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
            'scope' => 'read'
        )));
        $curl->setopt(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $curl->setopt(CURLINFO_HEADER_OUT, true);
        $curl->setopt(CURLOPT_RETURNTRANSFER, true);
        $curl->setopt(CURLOPT_CONNECTTIMEOUT, 30);
        $curl->setopt(CURLOPT_TIMEOUT, 30);
        $curl->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setopt(CURLOPT_HEADER, true);
        $curl->setopt(CURLOPT_VERBOSE, true);
        $curl->setopt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setopt(CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        $curl->setopt(CURLOPT_MAXREDIRS, 3);
        $response = $curl->exec();
        if ($response === false) {
            throw new \Exception(sprintf('Curl error message: "%s" in %s', $curl->error(), __METHOD__));
        }
        $headerSize = $curl->getinfo(CURLINFO_HEADER_SIZE);
        $responseBody = substr($response, $headerSize);
        $responseObject = json_decode($responseBody);
        $client->setDebug(
            $curl->getinfo(CURLINFO_HEADER_OUT),
            $curl->getinfo(CURLINFO_HTTP_CODE),
            substr($response, 0, $headerSize),
            (isset($responseObject->error) ? $responseObject : null)
        );
        $curl->close();
        self::$curl = null;

        return $responseObject;
    }
}

