<?php

namespace GravityDesignNL\SURFsara;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class SURFsaraHandles.
 *
 * @package GravityDesignNL\SURFsara
 */
class SURFsaraHandles {

  /**
   * The Guzzle HTTP client.
   */
  protected $client;

  /**
   * Validation messages.
   */
  protected $messages = [];

  /**
   * Are all settings valid?
   */
  protected $valid = TRUE;

  /**
   * Settings variables.
   *
   *   The configuration array must contain the following entries:
   *     [key]               - The full path to the private-key file.
   *     [cert]              - The full path to the certificate file.
   *     [handleName]        - The handle name.
   *     [handleUrl]         - The base handle url the PID redirects
   *                           to.
   *     [surfsaraApi]       - The SURFsara handle api url
   *     [surfsaraPrefixOrg] - The SURFsara handle api prefix
   *                           organisation code.
   *     [surfsaraPrefixEnv] - The SURFsara handle api prefix
   *                           environment code.
   *     [permissions]       - The permissions needed to set/get a handle.
   *     [overwrite]         - 'true' of 'false', defaults to 'true'.
   *     [verify]            - The configuration verify parameter,
   *                           defaults to FALSE.
   *     [headers]           - The extra headers to be added to a
   *                           request, defaults to empty array.
   */
  protected $key;
  protected $cert;
  protected $handleName;
  protected $handleUrl;
  protected $surfsaraApi;
  protected $surfsaraPrefixOrg;
  protected $surfsaraPrefixEnv;
  protected $permissions;
  protected $overwrite = 'true';
  protected $verify = FALSE;
  protected $headers = [];

  /**
   * SURFsaraHandles constructor.
   */
  public function __construct() {
    $this->client = new Client();
  }

  /**
   * Set the handle.
   */
  public function setHandle() {
    $response = [];

    try {
      if ($this->isValid()) {
        $json = [
          'values' => [
            [
              'index' => 1,
              'type' => 'URL',
              'data' => [
                'format' => 'string',
                'value' => $this->getHandleUrl(),
              ],
            ],
            [
              'index' => 100,
              'type' => 'HS_ADMIN',
              'data' => [
                'format' => 'admin',
                'value' => [
                  'index' => 200,
                  'handle' => $this->getSurfsaraPrefix(),
                  'permissions' => $this->getPermissions(),
                ],
              ],
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
        try {
          $result = $this->client->put($url, $config);
          $response['result'] = (in_array($result->getStatusCode(), [200, 201])) ? TRUE : FALSE;
          $response['code'] = $result->getStatusCode();
          $response['message'] = $result->getReasonPhrase();
        }
        catch (ClientException $exception) {
          // Return FALSE if handle can't be set because of a failing
          // connection.
          $response['result'] = FALSE;
          $response['code'] = $exception->getCode();
          $response['message'] = $exception->getMessage();
        }
      }
    }
    catch (\Exception $invalid) {
      // Return FALSE if handle can't be set because of missing or
      // invalid settings.
      $response['result'] = FALSE;
      $response['code'] = $invalid->getCode();
      $response['message'] = $invalid->getMessage();
    }

    // Return response.
    return $response;
  }

  /**
   * Check if all input is available and valid.
   *
   * @throws \Exception
   *   Throws message why the settings are not valid.
   */
  public function isValid() {
    // Run validations.
    $this->isValidKey();
    $this->isValidCert();
    $this->isValidHandleName();
    $this->isValidHandleUrl();
    $this->isValidSurfsaraApi();
    $this->isValidSurfsaraPrefixOrg();
    $this->isValidSurfsaraPrefixEnv();
    $this->isValidOverwrite();
    $this->isValidPermissions();
    $this->isValidVerify();
    $this->isValidHeaders();

    if ($this->getValid() === FALSE) {
      throw new \Exception('Your settings are not valid. Please check the messages or contact your administrator.', 9000);
    }

    return $this->getValid();
  }

  /**
   * Compose and return the full SURFsara url.
   *
   * @return string $url
   *   The composite url to the SURFsara environment.
   */
  private function getSurfsaraUrl() {
    $api = $this->getSurfsaraApi();
    $prefix = $this->getSurfsaraPrefix();
    $name = $this->getHandleName();
    $url = $api . '/' . $prefix . '/' . $name;
    if (!empty($this->getOverwrite())) {
      $url = $url . '?overwrite=' . $this->getOverwrite();
    }
    return $url;
  }

  /**
   * Return the full SURFsara prefix.
   *
   * @return string
   *   The SURFsara prefix.
   */
  private function getSurfsaraPrefix() {
    return $this->getSurfsaraPrefixOrg() . '/' . $this->getSurfsaraPrefixEnv();
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
   */
  private function isValidKey() {
    if (empty($this->getKey())) {
      $this->setMessage(new \Exception('The path to the private-key file can not be empty', 9001));
      $this->setInvalid();
    }
    if (!file_exists($this->getKey())) {
      $this->setMessage(new \Exception('The private-key file can not be found.', 9002));
      $this->setInvalid();
    }
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
   */
  private function isValidCert() {
    if (empty($this->getCert())) {
      $this->setMessage(new \Exception('The path to the sertificate file can not be empty', 9003));
      $this->setInvalid();
    }
    if (!file_exists($this->getCert())) {
      $this->setMessage(new \Exception('The certificate file can not be found.', 9004));
      $this->setInvalid();
    }
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
   */
  private function isValidHandleName() {
    if (empty($this->getHandleName())) {
      $this->setMessage(new \Exception('The handle name can not be empty', 9005));
      $this->setInvalid();
    }
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
   */
  private function isValidHandleUrl() {
    if (empty($this->getHandleUrl())) {
      $this->setMessage(new \Exception('The handle url can not be empty', 9006));
      $this->setInvalid();
    }
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
   * @param string $surfsara_api
   *   The SURFsara handle api url.
   */
  public function setSurfsaraApi($surfsara_api) {
    $this->surfsaraApi = $surfsara_api;
  }

  /**
   * Check if SURFsara API url is available and valid.
   */
  private function isValidSurfsaraApi() {
    if (empty($this->getSurfsaraApi())) {
      $this->setMessage(new \Exception('The SURFsara api url can not be empty', 9007));
      $this->setInvalid();
    }
  }

  /**
   * Get the SURFsara handle api organisation prefix.
   *
   * @return string
   *   The SURFsara handle api organisation prefix.
   */
  public function getSurfsaraPrefixOrg() {
    return $this->surfsaraPrefixOrg;
  }

  /**
   * Set the SURFsara handle api organisation prefix.
   *
   * @param string $surfsara_prefix_organisation
   *   The SURFsara handle api organisation prefix.
   */
  public function setSurfsaraPrefixOrg($surfsara_prefix_organisation) {
    $this->surfsaraPrefixOrg = $surfsara_prefix_organisation;
  }

  /**
   * Check if SURFsara organisation prefix is available and valid.
   */
  private function isValidSurfsaraPrefixOrg() {
    if (empty($this->getSurfsaraPrefixOrg())) {
      $this->setMessage(new \Exception('The organisation prefix can not be empty', 9008));
      $this->setInvalid();
    }
  }

  /**
   * Get the SURFsara handle api environment prefix.
   *
   * @return string
   *   The SURFsara handle api environment prefix.
   */
  public function getSurfsaraPrefixEnv() {
    return $this->surfsaraPrefixEnv;
  }

  /**
   * Set the SURFsara handle api environment prefix.
   *
   * @param string $surfsara_prefix_environment
   *   The SURFsara handle api environment prefix.
   */
  public function setSurfsaraPrefixEnv($surfsara_prefix_environment) {
    $this->surfsaraPrefixEnv = $surfsara_prefix_environment;
  }

  /**
   * Check if SURFsara environment prefix is available and valid.
   */
  private function isValidSurfsaraPrefixEnv() {
    if (empty($this->getSurfsaraPrefixEnv())) {
      $this->setMessage(new \Exception('The environment prefix can not be empty', 9009));
      $this->setInvalid();
    }
  }

  /**
   * Get the permissions value.
   *
   * @return string
   *   The permissions value;
   */
  public function getPermissions() {
    return $this->permissions;
  }

  /**
   * Set the permissions value.
   *
   * @param string $permissions
   *   The permissions value.
   */
  public function setPermissions($permissions) {
    $this->permissions = $permissions;
  }

  /**
   * Check if permissions are available and valid.
   */
  private function isValidPermissions() {
    if (empty($this->getPermissions())) {
      $this->setMessage(new \Exception('The permissions can not be empty', 9010));
      $this->setInvalid();
    }
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
   */
  private function isValidOverwrite() {
    $valid_options = ['true', 'false'];
    if (!empty($this->getOverwrite()) && !in_array($this->getOverwrite(), $valid_options)) {
      $this->setMessage(new \Exception('The overwrite should be a string set to either "true" or "false".', 9011));
      $this->setInvalid();
    }
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
   */
  private function isValidVerify() {
    if (!is_bool($this->isVerify())) {
      $this->setMessage(new \Exception('The verify should be a boolean set to either "true" or "false".', 9012));
      $this->setInvalid();
    }
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
   */
  private function isValidHeaders() {
    if (!is_array($this->getHeaders())) {
      $this->setMessage(new \Exception('The headers should be set in an array.', 9013));
      $this->setInvalid();
    }
  }

  /**
   * Get the messages.
   *
   * @return array
   *   The collected messages.
   */
  public function getMessages() {
    return $this->messages;
  }

  /**
   * Set a message.
   *
   * @param \Exception $message
   *   An exception containing a code and a message.
   */
  private function setMessage(\Exception $message) {
    $this->messages[] = $message;
  }

  /**
   * Clear the messages array.
   */
  public function clearMessages() {
    $this->messages = [];
  }

  /**
   * Get validation status.
   *
   * @return bool
   *   The validation status.
   */
  public function getValid() {
    return $this->valid;
  }

  /**
   * Set validation status.
   *
   * @param bool $valid
   *   The validation status.
   */
  public function setValid($valid = TRUE) {
    $this->valid = $valid;
  }

  /**
   * Set validation status to FALSE.
   */
  public function setInvalid() {
    $this->setValid(FALSE);
  }

}
