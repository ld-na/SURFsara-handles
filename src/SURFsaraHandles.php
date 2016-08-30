<?php

namespace GravityDesignNL\SURFsara;

use GuzzleHttp\Client;

/**
 * Class SURFsaraHandles.
 *
 * @package GravityDesignNL\SURFsara
 */
class SURFsaraHandles {

  /**
   * Variables.
   *
   *   The configuration array must contain the following entries:
   *     [key]             - The full path to the private-key file.
   *     [cert]            - The full path to the certificate file.
   *     [handle-name]     - The handle name.
   *     [handle-url]      - The base handle url the PID redirects to.
   *     [surfsara-url]    - The SURFsara handle api url
   *     [surfsara-prefix] - The SURFsara handle api prefix.
   *     [overwrite]       - 'true' of 'false', defaults to 'true'.
   *     [verify]          - The configuration verify parameter,
   *                         defaults to FALSE.
   *     [headers]         - The extra headers to be added to a request,
   *                         defaults to empty array.
   */
  protected $key;
  protected $cert;
  protected $handleName;
  protected $handleUrl;
  protected $surfsaraApi;
  protected $surfsaraPrefix;
  protected $overwrite = 'true';
  protected $verify = FALSE;
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
                'format' => 'admin',
              ],
              'format' => 'admin',
            ],
          ],
          [
            'index' => 1,
            'type' => 'URL',
            'data' => $this->getHandleUrl(),
          ],
        ],
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

    // Return FALSE if handle can't be set because of missing or
    // invalid settings.
    return FALSE;
  }

  /**
   * Compose and return the full SURFsara url.
   *
   * @return string $url
   *   The composite url to the SURFsara environment.
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
   * Get the full path to the private-key file.
   *
   * @return string
   *   The full path to the private-key file.
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * Set the full path to the private-key file.
   *
   * @param string $key
   *   The full path to the private-key file.
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
   * Get the full path to the certificate file.
   *
   * @return string
   *   The full path to the certificate file.
   */
  public function getCert() {
    return $this->cert;
  }

  /**
   * Set the full path to the certificate file.
   *
   * @param string $cert
   *   The full path to the certificate file.
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
   * Get the handle name.
   *
   * @return string
   *   The handle name.
   */
  public function getHandleName() {
    return $this->handleName;
  }

  /**
   * Set the handle name.
   *
   * @param string $handleName
   *   The handle name.
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
   * Get the base handle url the PID redirects to.
   *
   * @return string
   *   The base handle url the PID redirects to.
   */
  public function getHandleUrl() {
    return $this->handleUrl;
  }

  /**
   * Set the base handle url the PID redirects to.
   *
   * @param string $handleUrl
   *   The base handle url the PID redirects to.
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
   * Get the SURFsara handle api url.
   *
   * @return string
   *   The SURFsara handle api url.
   */
  public function getSurfsaraApi() {
    return $this->surfsaraApi;
  }

  /**
   * Set the SURFsara handle api url.
   *
   * @param string $surfsaraApi
   *   The SURFsara handle api url.
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
   * Get the SURFsara handle api prefix.
   *
   * @return string
   *   The SURFsara handle api prefix.
   */
  public function getSurfsaraPrefix() {
    return $this->surfsaraPrefix;
  }

  /**
   * Set the SURFsara handle api prefix.
   *
   * @param string $surfsaraPrefix
   *   The SURFsara handle api prefix.
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
   * Get the overwrite value.
   *
   * @return string
   *   The overwrite value.
   */
  public function getOverwrite() {
    return $this->overwrite;
  }

  /**
   * Set the overwrite value.
   *
   * @param string $overwrite
   *   The overwrite value to 'true' of 'false'.
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
   * Get the verify value.
   *
   * @return bool
   *   The verify value.
   */
  public function isVerify() {
    return $this->verify;
  }

  /**
   * Set the verify value.
   *
   * @param bool $verify
   *   The verify value.
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
   * Get the extra headers to be added to a request.
   *
   * @return array
   *   The extra headers to be added to a request.
   */
  public function getHeaders() {
    return $this->headers;
  }

  /**
   * Set the extra headers to be added to a request.
   *
   * @param array $headers
   *   The extra headers to be added to a request.
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
