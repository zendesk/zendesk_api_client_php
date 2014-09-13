<?php


/**
* HTTP functions via curl
*/
phpinfo();
class Http {
    public static function send($client) {

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://z3nburmaglot.zendesk.com/api/v2/apps/uploads.json');
            curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, array('uploaded_data' => '@Apps.zip'));
            curl_setopt($curl, CURLOPT_USERPWD, $client->getAuthText());

        $response = curl_exec($curl);
		print_r($response);
        curl_close($curl);
    }

}

    public function upload(array $params) {
        $response = Http::send($this->client);
        return $response;
    }
    
        public function upload(array $params) {
        if(!$this->hasKeys($params, array('file'))) {
            throw new MissingParametersException(__METHOD__, array('file'));
        }
        $endPoint = Http::prepare('apps/uploads.json');
        $response = Http::send($this->client, $endPoint, array('uploaded_data' => '@'.$params['file']), 'POST', (isset($params['type']) ? $params['type'] : 'application/binary'));
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }