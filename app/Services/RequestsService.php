<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use stdClass;

class RequestsService 
{
  
  /*
  * @description - the respective property is responsible for storing the configuration file name in /config
  * @var string
  */
  private string $config;

  /*
  * @description - the respective property is responsible for storing the endpoint of the request
  * @var string
  */
  private string $baseUrl;

  /*
  * @description - the respective property is responsible for storing the headers of the request
  * @var array
  */
  private array $headers = ['Content-Type' => 'application/json'];

  /*
  * @description - the respective property is responsible for define request type (GET, POST, PUT, DELETE), 
  * this is defined automatically by the method constructEndpoint
  * @var string
  */
  private string $method;

  /**
   * @description - the respective property is responsible for storing the response of the request
   *
   * @var string
   */
  public string $status;

  public function __construct(string $config)
  {
    $this->config = $config;
    $this->baseUrl = config($config.'.base_url');
  }

  /**
   * @description - the respective method is responsible for setting the headers of the request
   *
   * @param array $headers
   * @return void
   */
  public function setHeaders(array $headers = [])
  {
    $this->headers = [...$this->headers, ...$headers];
  }

  /**
   * @description - the respective method is responsible for set status before send request
   *
   * @param string $resource
   * @param array $params
   * @return void
   */
  private function setStatus($status)
  {
    $this->status = $status;
  }

  /**
   * @description - the respective method is responsible for get status of request
   *
   * @param string $resource
   * @param array $params
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * @description - the respective method is responsible for construct the endpoint of the request
   *
   * @param string $resource name endpoint
   * @param array $params - the respective property is responsible for storing the parameters of the request
   * @return string
   */
  public function endpoint(string $resource, array $params = [])
  {
    $endpoint        = $this->baseUrl.$this->constructEndpoint($resource);
    foreach ($params as $key => $value) 
      $endpoint = str_replace('{'.$key.'}', $value, $endpoint);
    return $endpoint;
  }

  /**
   * @description - the respective method is responsible for construct the endpoint of the request
   *
   * @param string $resource
   * @return string
   */
  public function constructEndpoint(string $resource)
  {
    $resource = config($this->config.'.endpoints.'.$resource);
    if(!$resource)
      throw new Exception('[RequestsService - seResource] Endpoint not found');

    $this->method = $resource['method'];
    return $resource['endpoint'];
  }

  /**
   * @description - the respective method is responsible for send the request
   *
   * @param string $resource name endpoint
   * @param array $params - the respective property is responsible for storing the parameters of the request
   * @param array $payload - the respective property is responsible for storing the payload of the request
   * @return Response
   */
  public function send(string $resource, array $params = [], array $payload = [])
  {
    try {
        $endpoint = $this->endpoint($resource, $params);
        $http     = Http::withHeaders($this->headers);

        switch ($this->method) {
          case 'GET':
            $response = $http->get($endpoint);
            break;
          case 'POST':
            $response = $http->post($endpoint, $payload);
            break;
          case 'PUT':
            $response = $http->put($endpoint, $payload);
            break;
          case 'PATCH':
            $response = $http->patch($endpoint, $payload);
            break;
          case 'DELETE':
            $response = $http->delete($endpoint);
            break;
        }
        
        $this->setStatus($response->status());
        return $response;
      } catch (\Throwable $th) {
        throw new Exception($th->getMessage(), 1);
      }
  }

}