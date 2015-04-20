<?php

namespace Drupal\amazons3;

use Guzzle\Http\QueryString;
use Guzzle\Http\Url;

/**
 * Represents an s3:// stream URL.
 *
 * @class S3Url
 * @package Drupal\amazons3
 */
class S3Url extends Url {

  /**
   * Return the bucket associated with the URL.
   *
   * @return string
   */
  public function getBucket() {
    return $this->getHost();
  }

  /**
   * Set the bucket.
   *
   * @param string $bucket
   */
  public function setBucket($bucket) {
    $this->setHost($bucket);
  }

  /**
   * Return the S3 object key.
   *
   * @return string
   */
  public function getKey() {
    // Remove the leading slash getPath() keeps in the path.
    return substr($this->getPath(), 1);
  }

  /**
   * Set the S3 object key.
   *
   * This automatically prepends a slash to the path.
   *
   * @param string $key
   */
  public function setKey($key) {
    $this->setPath('/' . $key);
  }

  /**
   * Overrides factory() to support bucket configs.
   *
   * @param string $url
   *   Full URL used to create a Url object
   * @param \Drupal\amazons3\StreamWrapperConfiguration $config
   *   (optional) Configuration to associate with this URL.
   *
   * @return static
   *   An S3Url.
   */
  public static function factory($url, StreamWrapperConfiguration $config = null) {
    !$config ? $bucket = null : $bucket = $config->getBucket();

    $defaults = array('scheme' => 's3', 'host' => $bucket, 'path' => null, 'port' => null, 'query' => null,
      'user' => null, 'pass' => null, 'fragment' => null);

    if (false === ($parts = parse_url($url))) {
      throw new \InvalidArgumentException('Was unable to parse malformed url: ' . $url);
    }

    $parts += $defaults;

    // Convert the query string into a QueryString object
    if ($parts['query'] || 0 !== strlen($parts['query'])) {
      $parts['query'] = QueryString::fromString($parts['query']);
    }

    return new static($parts['scheme'], $parts['host'], $parts['user'],
      $parts['pass'], $parts['port'], $parts['path'], $parts['query'],
      $parts['fragment']);
  }
}