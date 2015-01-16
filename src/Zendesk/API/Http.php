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
                if(in_array($k, array('per_page', 'page', 'sort_order', 'sort_by'))) {
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
     * @param array  $json
     * @param string $method
     * @param string $contentType
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public static function send(Client $client, $endPoint, $json = array(), $method = 'GET', $contentType = 'application/json') {
        $url    = $client->getApiUrl() . $endPoint;
        $method = strtoupper($method);

        $curl = curl_init($url);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);

        } else if ($method === 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');

        } else {
            $st = http_build_query((array) $json);
            curl_setopt($curl, CURLOPT_URL, $url . ($st !== array() ? (strpos($url, '?') === false ? '?' : '&') . $st : ''));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        $httpHeader = array('Accept: application/json');
        if ($client->getAuthType() == 'oauth_token') {
            $httpHeader[] = 'Authorization: Bearer ' . $client->getAuthText();

        } else {
            curl_setopt($curl, CURLOPT_USERPWD, $client->getAuthText());
        }

        /* DO NOT SET CONTENT TYPE IF UPLOADING */
        if (!isset($json['uploaded_data'])) {
            $httpHeader[] = 'Content-Type: '.$contentType;
        } else {
            $contentType = '';
        }

        if ($contentType === 'application/json') {
            $json = json_encode($json);
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
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
            throw new \Exception(sprintf('Curl error message: "%s" in %s', curl_error($curl),  __METHOD__));
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
            throw new \Exception(sprintf('Curl error message: "%s" in %s', curl_error($curl),  __METHOD__));
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
