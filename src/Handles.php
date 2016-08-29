<?php

namespace GravityDesignNL\SURFsara;

use GuzzleHttp\Client;

class Handles {

  /**
   * Variables.
   *
   *   The configuration array must contain the following entries:
   *     [key] - The full path to the private-key file.
   *     [cert] - The full path to the certificate file.
   *     [handle-name] - The handle name.
   *     [handle-url] - The base handle url the PID redirects to.
   *     [surfsara-url] - The SURFsara handle api url
   *     [surfsara-prefix] - The SURFsara handle api prefix.
   *     [overwrite] - 'true' of 'false', defaults to 'true'.
   */
  protected $key;
  protected $cert;
  protected $handleName;
  protected $handleUrl;
  protected $surfsaraApi;
  protected $surfsaraPrefix;
  protected $overwrite = 'true';
  
  /**
   * Set the handle.
   */
  public function setHandle() {

    $json = [
      'values' => [[
        'index' => 100,
        'type' => 'HS_ADMIN',
        'data' => [
          'value' => [
            'index' => 200,
            'handle' => '0.NA/1000',
            'permissions' => '011111110011',
            'format' => 'admin'
          ],
          'format' => 'admin',
        ]
      ],[
        'index' => 1,
        'type' => 'URL',
        'data' => $this->getHandleUrl(),
      ]]
    ];

    $extra_headers = ['Authorization' => 'Handle clientCert="true"'];
    $config = [
      'headers' => $extra_headers,
      'verify' => false,
      'ssl_key' => $this->getKey(),
      'cert' => $this->getCert(),
      'json' => $json,
    ];

    $surfsara_url = $this->getSurfsaraUrl();
    $client = new Client();

    return $client->put($surfsara_url, $config);
  }

  /**
   * Compose and return the full SURFsara url.
   *
   * @return mixed|string
   */
  public function getSurfsaraUrl() {

    $api = $this->getSurfsaraApi();
    $prefix = $this->getSurfsaraPrefix();
    $name = $this->getHandleName();
    $overwrite = $this->getOverwrite();
    return $api . '/' . $prefix . '/' . $name . '?overwrite=' . $overwrite;
  }


  /**
   * @return mixed
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * @param mixed $key
   */
  public function setKey($key) {
    $this->key = $key;
  }

  /**
   * @return mixed
   */
  public function getCert() {
    return $this->cert;
  }

  /**
   * @param mixed $cert
   */
  public function setCert($cert) {
    $this->cert = $cert;
  }

  /**
   * @return mixed
   */
  public function getHandleName() {
    return $this->handleName;
  }

  /**
   * @param mixed $handleName
   */
  public function setHandleName($handleName) {
    $this->handleName = $handleName;
  }

  /**
   * @return mixed
   */
  public function getHandleUrl() {
    return $this->handleUrl;
  }

  /**
   * @param mixed $handleUrl
   */
  public function setHandleUrl($handleUrl) {
    $this->handleUrl = $handleUrl;
  }

  /**
   * @return mixed
   */
  public function getSurfsaraApi() {
    return $this->surfsaraApi;
  }

  /**
   * @param mixed $surfsaraApi
   */
  public function setSurfsaraApi($surfsaraApi) {
    $this->surfsaraApi = $surfsaraApi;
  }

  /**
   * @return mixed
   */
  public function getSurfsaraPrefix() {
    return $this->surfsaraPrefix;
  }

  /**
   * @param mixed $surfsaraPrefix
   */
  public function setSurfsaraPrefix($surfsaraPrefix) {
    $this->surfsaraPrefix = $surfsaraPrefix;
  }

  /**
   * @return mixed|string
   */
  public function getOverwrite() {
    return $this->overwrite;
  }

  /**
   * @param mixed|string $overwrite
   */
  public function setOverwrite($overwrite) {
    $this->overwrite = $overwrite;
  }

}
