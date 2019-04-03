<?php
/**
 * Created by IntelliJ IDEA.
 *
 * @author Timothy Wilson <tim.wilson@aceviral.com>
 * @copyright AceViral.com LTD 2019
 * Date: 13/02/2019
 * Time: 19:00
 */

namespace Altrozero\GetAddressIOPHP\Test;

require 'vendor/autoload.php';

use Altrozero\GetAddressIOPHP\Postcode;
use PHPUnit\Framework\TestCase;

class PostcodeTest extends TestCase
{
  public function inputPostcodesValidator()
  {
    return [
      ['NE29 0EG', true],
      ['CT13 9FD', true],
      ['B37 7UA', true],
      ['W1H 2LQ', true],
      ['W2 1JB', true],
      ['NE290EG', true],
      ['CT139FD', true],
      ['B377UA', true],
      ['W1H2LQ', true],
      ['BANGARANG', false],
      ['C1 11', false],
      ['BHK37 7UA', false],
      ['W1H 22LQ', false],
      ['W122LQFD', false],
      ['W1 H2H', false],
    ];
  }

  public function inputPostcodesParts()
  {
    return [
      ['NE29 0EG', true, 'NE29', '0EG', 'NE', '29', '0', 'EG'],
      ['B37 7UA', true, 'B37', '7UA', 'B', '37', '7', 'UA'],
      ['W1H 2LQ', true, 'W1H', '2LQ', 'W', '1H', '2', 'LQ'],
      ['W2 1JB', true, 'W2', '1JB', 'W', '2', '1', 'JB'],
      ['NE290EG', false, 'NE290', '90EG', 'N', '1', 'NE', '9'],
      ['CT139FD', false, 'CT139FD', 'CT139FD', 'CT139FD', 'CT139FD', 'CT139FD', 'CT139FD'],
    ];
  }

  /**
   * @dataProvider inputPostcodesValidator
   *
   * @param string $postcode
   * @param bool $outcome
   */
  public function testPostcodeValidator($postcode, $outcome):void
  {
    $this->assertEquals(Postcode::validate($postcode), $outcome);
  }

  public function testThrowExceptionIfBadPostcodeIsPassed()
  {
    $this->expectException(\InvalidArgumentException::class);
    new Postcode('BANGARANG');
  }

  public function testGetPostcodeBack()
  {
    $postcode = 'NE29 0EG';

    $pcObj = new Postcode($postcode);

    $this->assertEquals(Postcode::format($postcode), $pcObj->get());
  }

  /**
   * @dataProvider inputPostcodesParts
   *
   * @param string $postcode
   * @param bool $outcome
   * @param string $outward
   * @param string $inward
   * @param string $area
   * @param string $district
   * @param string $sector
   * @param string $unit
   */
  public function testGetPostcodeParts($postcode, $outcome, $outward, $inward, $area, $district, $sector, $unit)
  {
    $pcObj = new Postcode($postcode);

    // Outward
    $this->assertEquals(
      ($pcObj->getOutward() == $outward),
      $outcome
    );

    // Inward
    $this->assertEquals(
      ($pcObj->getInward() == $inward),
      $outcome
    );

    // Area
    $this->assertEquals(
      ($pcObj->getArea() == $area),
      $outcome
    );

    // District
    $this->assertEquals(
      ($pcObj->getDistrict() == $district),
      $outcome
    );

    // sector
    $this->assertEquals(
      ($pcObj->getSector() == $sector),
      $outcome
    );

    // unit
    $this->assertEquals(
      ($pcObj->getUnit() == $unit),
      $outcome
    );
  }
}