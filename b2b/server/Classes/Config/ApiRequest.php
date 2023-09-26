<?php


namespace Panel\Server\Classes\Config;

class ApiRequest {
    private $url;
    private $headers;
    private $data;
    
    public function __construct($url) {
        $this->url = $url;
        $this->headers = [];
        $this->data = array();
    }
    
    public function setHeader($key, $value) {
        array_push($this->headers,"$key:$value");
    }

    public function setHeaderRaw(array $headers){
        if(is_array($headers) && !empty($headers)){
            foreach($headers as $key=>$value){
                $this->setHeader($key,$value);
            }
        }
    }
    
    public function setData($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function send() {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, count($this->data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
        
        $response = curl_exec($ch);
        
        curl_close($ch);
        
        return $response;
    }

    public function sendAsPost(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, count($this->data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}
