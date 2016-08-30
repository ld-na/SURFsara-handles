<?php

namespace GravityDesignNL\SURFsara;

use GuzzleHttp\Client;

class SURFsaraHandles {

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
  protected $verify = false;
  protected $headers = [];

  /**
   * Set the handle.
   */
  public function setHandle() {

    if ($this->isValid()) {

      $json = [
        'values' => [
          [
            'index' => 100,
            'type' => 'HS_ADMIN',
            'data' => [
              'value' => [
                'index' => 200,
                'handle' => '0.NA/' . $this->getSurfsaraPrefix(),
                'permissions' => '011111110011',
                'format' => 'admin'
              ],
              'format' => 'admin',
            ]
          ],
          [
            'index' => 1,
            'type' => 'URL',
            'data' => $this->getHandleUrl(),
          ]
        ]
      ];

      $config = [
        'headers' => $this->getHeaders(),
        'verify' => $this->isVerify(),
        'ssl_key' => $this->getKey(),
        'cert' => $this->getCert(),
        'json' => $json,
      ];

      $url = $this->getSurfsaraUrl();
      $client = new Client();

      return $client->put($url, $config);
    }

    // Return FALSE if handle can't be set because of missing or invalid settings.
    return FALSE;
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

    $url = $api . '/' . $prefix . '/' . $name;

    if (!empty($this->getOverwrite()) && $this->isValidOverwrite()) {
      $url = $url . '?overwrite=' . $this->getOverwrite();
    }

    return $url;
  }


  /**
   * Check if all input is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  public function isValid() {
    if (!$this->isValidKey() || !$this->isValidCert() || !$this->isValidHandleName() ||
        !$this->isValidHandleUrl() || !$this->isValidSurfsaraApi() || !$this->isValidSurfsaraPrefix() ||
        !$this->isValidOverwrite() || !$this->isValidVerify() || !$this->isValidHeaders()
       ) {
      return FALSE;
    }

    return TRUE;
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
   * Check if key is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidKey() {
    return !empty($this->getKey());
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
   * Check if cert is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidCert() {
    return !empty($this->getCert());
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
   * Check if handle name is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidHandleName() {
    return !empty($this->getHandleName());
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
   * Check if handle url is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidHandleUrl() {
    return !empty($this->getHandleUrl());
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
   * Check if SURFsara API url is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidSurfsaraApi() {
    return !empty($this->getSurfsaraApi());
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
   * Check if SURFsara prefix is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidSurfsaraPrefix() {
    return !empty($this->getSurfsaraPrefix());
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

  /**
   * Check if overwrite is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidOverwrite() {
    $valid_options = ['true', 'false'];
    if (!empty($this->getOverwrite()) && !in_array($this->getOverwrite(), $valid_options)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * @return boolean
   */
  public function isVerify() {
    return $this->verify;
  }

  /**
   * @param boolean $verify
   */
  public function setVerify($verify) {
    $this->verify = $verify;
  }

  /**
   * Check if verify is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidVerify() {
    return (is_bool($this->isVerify()));
  }

  /**
   * @return array
   */
  public function getHeaders() {
    return $this->headers;
  }

  /**
   * @param array $headers
   */
  public function setHeaders($headers) {
    $this->headers = $headers;
  }

  /**
   * Check if headers is available and valid.
   *
   * @return bool
   *   Returns true when valid, false if not.
   */
  private function isValidHeaders() {
    return (is_array($this->getHeaders()));
  }

}
