
<?php

	use GuzzleHttp\Client;
	use GuzzleHttp\RequestOptions;

    function postGuzzleRequest($url_param = '', $data = array()){

        $url       =   	url('../busapp/api/'.$url_param.'');
        $client    =   	new Client();
        $response  =   	$client->request('POST', $url, [
                            'form_params' => $data
                        ]);

        return $response;
    }

?>