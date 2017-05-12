<?php

namespace FileStorage;

use Colibri\HttpRequest\HttpRequestException;
use Colibri\HttpRequest\Provider\Curl;

/**
 * Class Client
 *
 * @package FileStorage
 */
class Client
{

  /**
   * @var Curl
   */
  protected $curl;

  /**
   * Client constructor.
   *
   * @param Curl $curl
   * @param      $rootPath
   * @param      $token
   */
  public function __construct(Curl $curl, $rootPath, $token)
  {
    $this->curl = $curl;
    $this->curl->uri($rootPath)->getUri()->setQuery('token', $token);
  }

  /**
   * @param     $filepath
   * @param     $name
   * @param int $category
   * @return mixed
   * @throws HttpRequestException
   * @throws \Exception
   */
  public function uploadFile($filepath, $name, $category = 1, $mimetype = null)
  {
    $params = [
      'file' => Curl::file($filepath, $mimetype),
      'name' => $name,
      'category_id' => $category,
      'protected' => 0,
    ];

    $response = $this->post('/upload', $params);

    return $response->response->uploaded_file_uid;
  }

  /**
   * @param $hash
   * @return mixed
   * @throws \Exception
   */
  public function removeFile($hash)
  {
    return $this->get("/$hash/remove")->response->message;
  }

  /**
   * @param $hash
   * @return mixed
   * @throws \Exception
   */
  public function getDirectLink($hash)
  {
    return $this->get("/$hash")->response->links->direct;
  }

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkToken()
  {
    return $this->get('/protected-auth/status')->response->status !== 'guest';
  }

  /**
   * @param array $params
   * @return mixed
   * @throws \Exception
   */
  public function generateToken(array $params = [])
  {
    $response = $this->get('/protected-auth/create-token', $params);

    return $response->response->token;
  }

  /**
   * @param       $path
   * @param array $params
   * @return array|object
   * @throws \Exception
   */
  public function post($path, array $params = [])
  {
    $this->curl->getUri()->setPath($path);

    try {
      $response = $this->curl->post($params)->getJsonBody();
      if ($response->status === 'error') {
        throw new \Exception($response->response->message);
      }
    } catch (\Exception $exception) {
      throw $exception;
    }

    return $response;
  }

  /**
   * @param       $path
   * @param array $params
   * @return array|object
   * @throws \Exception
   */
  public function get($path, array $params = [])
  {
    $this->curl->getUri()->setPath($path);

    try {
      $response = $this->curl->get($params)->getJsonBody();
      if ($response->status === 'error') {
        throw new \Exception($response->response->message);
      }
    } catch (\Exception $exception) {
      throw $exception;
    }

    return $response;
  }

}