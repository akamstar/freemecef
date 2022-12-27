<?php

namespace Freemecef;

class EmecefResponse
{
    private $response;


    public function __construct($response)
    {
        $this->response = $response;
    }


    public function successful()
    {
        return ($this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300);
    }

    public function json()
    {
        return json_decode($this->response->getBody(), true);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (method_exists($this->response,$name)){
            return call_user_func_array([$this->response, $name], $arguments);
        }
    }
}