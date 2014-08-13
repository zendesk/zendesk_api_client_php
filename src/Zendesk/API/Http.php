<?php

namespace Zendesk\API;

/**
 * HTTP functions via curl
 * @package Zendesk\API
 */
class Http {

    /**
     * Prepares an endpoint URL with optional side-loading
     *
     * @param string $endPoint
     * @param array  $sideload
     * @param array  $iterators
     *
     * @return string
     */
    public static function prepare($endPoint, array $sideload = null, array $iterators = null) {
        $addParams = array();
        // First look for side-loaded variables
        if(is_array($sideload)) {
            $addParams['include'] = implode(',', $sideload);
        }
        // Next look for special collection iterators
        if(is_array($iterators)) {
            foreach($iterators as $k => $v) {
                if(in_array($k, array('per_page', 'page', 'sort_order'))) {
                    $addParams[$k] = $v;
                }
            }
        }
        // Send it back...
        if(count($addParams)) {
            return $endPoint.(strpos($endPoint, '?') === false ? '?' : '&').http_build_query($addParams);
        } else {
            return $endPoint;
        }
    }

    /**
     * Use the send method to call every endpoint except for oauth/tokens
     *
     * @param Client $client
     * @param string $endPoint
     * @param mixed  $json
     * @param string $method
     * @param string $contentType
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public static function send(Client $client, $endPoint, $json = null, $method = 'GET', $contentType = 'application/json') {

        $url    = $client->getApiUrl() . $endPoint;
        $method = strtoupper($method);
        if (null == $json) {
            $json = new \stdClass();
        } else if ($contentType == 'application/json' && $method != 'GET' && $method != 'DELETE') {
            $json = json_encode($json);
        }

        if ($method == 'POST') {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            if (is_array($json) && isset($json['filename'])) {
                $file     = fopen($json['filename'], 'r');
                $size     = filesize($json['filename']);
                $fileData = fread($file, $size);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $fileData);
                curl_setopt($curl, CURLOPT_INFILE, $file);
                curl_setopt($curl, CURLOPT_INFILESIZE, $size);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/binary'));
            }
        } else if ($method == 'PUT') {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        } else {
            $curl = curl_init(
                $url . ($json != (object)null ? (strpos($url, '?') === false ? '?' : '&') . http_build_query($json) : '')
            );
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, ($method ? $method : 'GET'));
        }
        if ($client->getAuthType() == 'oauth_token') {
            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: ' . $contentType,
                    'Accept: application/json',
                    'Authorization: Bearer ' . $client->getAuthText()
                )
            );
        } else {
            curl_setopt($curl, CURLOPT_USERPWD, $client->getAuthText());
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: ' . $contentType, 'Accept: application/json'));
        }
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        $response = curl_exec($curl);
        if ($response === false) {
            throw new \Exception('No response from curl_exec in ' . __METHOD__);
        }
        $headerSize   = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $responseBody = substr($response, $headerSize);
        $client->setDebug(
            curl_getinfo($curl, CURLINFO_HEADER_OUT),
            curl_getinfo($curl, CURLINFO_HTTP_CODE),
            substr($response, 0, $headerSize)
        );
        curl_close($curl);

        return json_decode($responseBody);

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
    public static function oauth(Client $client, $code, $oAuthId, $oAuthSecret) {

        $url = 'https://'.$client->getSubdomain().'.zendesk.com/oauth/tokens';
        $method = 'POST';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $oAuthId,
            'client_secret' => $oAuthSecret,
            'redirect_uri' => ($_SERVER['HTTPS'] ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'],
            'scope' => 'read'
        )));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        $response = curl_exec($curl);
        if ($response === false) {
            throw new \Exception('No response from curl_exec in '.__METHOD__);
        }
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $responseBody = substr($response, $headerSize);
        $client->setDebug(
            curl_getinfo($curl, CURLINFO_HEADER_OUT),
            curl_getinfo($curl, CURLINFO_HTTP_CODE),
            substr($response, 0, $headerSize)
        );
        curl_close($curl);

        return json_decode($responseBody);

    }

}
