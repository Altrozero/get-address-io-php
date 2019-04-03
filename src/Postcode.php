<?php
/**
 * Created by IntelliJ IDEA.
 *
 * Uses the outward inward breakdown provided @
 * https://en.wikipedia.org/wiki/Postcodes_in_the_United_Kingdom#Formatting
 *
 * @author Timothy Wilson <tim.wilson@aceviral.com>
 * @copyright AceViral.com LTD 2019
 * Date: 13/02/2019
 * Time: 18:32
 */

namespace Altrozero\GetAddressIOPHP;

/**
 * Class Postcode
 * @package Altrozero\GetAddressIOPHP
 *
 * Parse / Validate a postcode, provides a few other helpful functions
 * for getting details about the postcode
 *
 * @property $postcode
 */
class Postcode
{
  /**
   * Inputted postcode
   *
   * @var string $postcode
   */
  private $_postcode;

  /**
   * Postcode constructor.
   *
   * Will throw an error if not a validate Postcode
   *
   * @param $postcode
   */
  public function __construct($postcode)
  {
    if (empty($postcode) || !self::validate($postcode)) {
      throw new \InvalidArgumentException;
    }

    $this->_postcode = self::format($postcode);
  }

  /**
   * Get the postcode
   *
   * @return string
   */
  public function get()
  {
    return (string)$this->_postcode;
  }

  /**
   * Get the Outward
   *
   * @return string
   */
  public function getOutward()
  {
    return substr($this->_postcode, 0, -3);
  }

  /**
   * Get the Inward
   *
   * @return string
   */
  public function getInward()
  {
    return substr($this->_postcode, -3);
  }

  /**
   * Get the Area
   *
   * @return string
   */
  public function getArea()
  {
    if (is_numeric(substr($this->_postcode, 1, 1))) {
      return substr($this->_postcode, 0, 1);
    }

    return substr($this->_postcode, 0, 2);
  }

  /**
   * Get the district
   *
   * @return string
   */
  public function getDistrict()
  {
    $start = 1;

    if (!is_numeric(substr($this->_postcode, 1, 1))) {
      $start = 2;
    }

    return substr($this->_postcode, $start, strlen($this->_postcode) - $start - 3);
  }

  /**
   * Get the sector
   *
   * @return string
   */
  public function getSector()
  {
    return substr($this->_postcode, -3, 1);
  }

  /**
   * Get the sector
   *
   * @return string
   */
  public function getUnit()
  {
    return substr($this->_postcode, -2);
  }

  /**
   * Validate a UK postcode
   *
   * @param string $postcode
   *
   * @return boolean
   */
  public static function validate($postcode)
  {
    // Clean the input
    $postcode = self::format($postcode);

    if (
      preg_match("/^([A-Za-z][A-Ha-hJ-Yj-y]?[0-9][A-Za-z0-9]?[0-9][A-Za-z]{2}|[Gg][Ii][Rr] 0[Aa]{2})$/", $postcode)
    ) {
      return true;
    }

    return false;
  }

  /**
   * Format a postcode
   * - Strip spaces
   * - Make it uppercase
   * - Does not validate
   *
   * @param string $postcode
   *
   * @return string
   */
  public static function format($postcode)
  {
    $postcode = preg_replace('/\s+/', '', $postcode);
    $postcode = strtoupper($postcode);

    return $postcode;
  }
}