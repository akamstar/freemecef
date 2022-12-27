<?php


namespace Freemecef;

require_once __DIR__.'/config.php';

class EmecefInfo
{
    private $http;
    private $prod;

    public function __construct($token, $prod = false)
    {
        $this->prod = $prod;

        $this->http = new \GuzzleHttp\Client([
            'base_uri' => $prod? PRODURL:TESTURL,
            'timeout'  => 6,
            'headers' => [
                "Authorization" => "Bearer " .$token,
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ]
        ]);
        //var_dump($this->http);
    }

    public function getInfo()
    {
        //get status
        return $this->makeRequest('/status');
    }

    public function getTaxGroups()
    {
        // get taxGroups
        return $this->makeRequest('/taxGroups');
    }

    public function getInvoiceTypes()
    {
        // get invoiceTypes
        return $this->makeRequest('/invoiceTypes');
    }

    public function getPaymentTypes()
    {
        // get paymentTypes
        return $this->makeRequest('/paymentTypes');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function makeRequest($endpoint)
    {
        $endpoint = ($this->prod? "/emcf/api":"/sygmef-emcf/api")."/info".$endpoint;
        $response = $this->http->get($endpoint);
        return new EmecefResponse($response);
    }
}
