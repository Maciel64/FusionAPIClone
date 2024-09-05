<?php

namespace App\Traits;

trait ApiResponse
{

  protected $resource = [];
  
  private function content(string $message, array $data, bool $success, $statusCode = null, $errorCode = null): array
  {
      $content = [
          'status'  => ($success)?true: false,
          'message' => $message,
          'data'    => $data,
      ];

      switch (true) {
          case $statusCode === null:
              $headerCode = ($success) ? 200 : 400;
              break;
          default:
              $headerCode = $statusCode;
              break;
      }

      if(!$success) $content['errorCode'] = $errorCode;

      return compact('headerCode', 'content');
  }

  /**
   *
   * @param string|array $message Filename of setting and index (example: organization.create)
   * @param array|bool|object $data Required data [] or false to return error message
   * @param null $statusCode
   * @param null $errorCode
   * @return Response|Application|ResponseFactory
   */
  protected function response(string $message, array|bool|object $data = [], $forceFail = false, $statusCode = null, $errorCode = null)
  {
    if(!$data or $forceFail) {
      if(!$data) $data = [];
      $data = $forceFail? $data: [];
        $message  = 'response.'.$message . '.fail';
        $message  = __($message, $this->resource);
        $response = $this->content($message, $data, false, $statusCode, $errorCode);
    }else{
        if(!is_array($data) and is_bool($data)) $data = [];
        if(is_object($data)) $data = $data->toArray();
        $message  = 'response.'.$message . '.success';
        $message  = __($message, $this->resource);
        $response = $this->content($message, $data, true, $statusCode);
    }

    return response($response['content'], $response['headerCode']);
  }

}