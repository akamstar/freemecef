<?php


namespace Freemecef;

require_once __DIR__.'/config.php';

class Emecef
{
    private $http, $payment_type, $id, $invoice_type;
    private $client, $products, $ifu, $operator, $endpoint;
    //private $prod, $token;

    const ACTION_CONFIRM = 'confirmer',
        ACTION_CANCEL = 'annuler';

    public function __construct($token, $prod = false)
    {
        // $this->token = $token;
        $this->payment_type = [];

        $this->prod = $prod;
        $this->endpoint = ($prod? "/emcf/api":"/sygmef-emcf/api")."/invoice";


        $this->http = new \GuzzleHttp\Client([
                'base_uri' => $prod? PRODURL:TESTURL,
                'timeout'  => 6,
                'headers' => [
                    "Authorization" => "Bearer " .$token,
                    "Content-Type" => "application/json",
                    "Accept" => "application/json",
                ]
            ]);

    }

    public function getIfu()
    {
        if(!$this->ifu) {
            $infoResponse = $this->getStatus();
            //dd($info);
            $info = $infoResponse->json();
            return (is_array($info) && array_key_exists('ifu',$info))? $info['ifu']: false;
        }
        return $this->ifu;
    }

    public function setIfu($ifu)
    {
        $this->ifu = $ifu;
        return $this;
    }

    public function setInvoiceType($invice_type)
    {
        $this->invoice_type = $invice_type;
        return $this;
    }

    public function getStatus()
    {
        $response = $this->http->get($this->endpoint.'/');
        return new EmecefResponse($response);
    }

    public function setClient($name, $ifu=null, $contact=null, $address=null)
    {
        $this->client = ["name" => $name];
        if ($ifu) $this->client['ifu'] = $ifu;
        if ($contact) $this->client['contact'] = $contact;
        if ($address) $this->client['address'] = $address;

        return $this;
    }

    public function addPaymentType($type, $amount)
    {
        $this->payment_type[] = ['name' =>$type, 'amount' => $amount];
        return $this;
    }

    public function setOperator($name, $id ="")
    {
        $this->operator = ['id' => $id, 'name' => $name];
        return $this;
    }

    public function setReference($id)
    {
        $this->id = $id;

        return $this;
    }

    public function addProduct($name, $price, $qty, $tax = 'A', $specTax = null)
    {
        $product = [
            'name' => $name,
            'price' => $price,
            'quantity' => $qty,
            'taxGroup' => $tax
        ];
        if ($specTax) $product['taxSpecific'] = $specTax;

        $this->products[] = $product;
        return $this;
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invoice()
    {
        $data['ifu'] = $this->getIfu();
        if (!$this->invoice_type){
            return new \Exception("You must set invoice type");
        }
        $data['type'] = $this->invoice_type;

        if (in_array($this->invoice_type,['FA','EA'])){
            if (!$this->invoice_type){
                return new \Exception("You must set a reference");
            }
            $data['reference'] = $this->id;
        }

        $data['items'] = $this->products;

        if (!$this->client){
            return new \Exception("You must set client info");
        }
        $data['client'] = $this->client;

        if (!$this->operator){
            return new \Exception("You must set operator info");
        }
        $data['operator'] = $this->operator;

        $data['payment'] = $this->payment_type;
        //dd($data);
        $response = $this->http->post($this->endpoint.'/',[
            'body' => json_encode($data),

            ]);
        return new EmecefResponse($response);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute($uid, $action='confirm')
    {
        $response = $this->http->put($this->endpoint.'/'. $uid.'/'.$action);
        return new EmecefResponse($response);
    }

    // Get unfinished invoice info

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($ref)
    {
        $response = $this->http->get($this->endpoint."/".$ref);
        return new EmecefResponse($response);
    }

}
