<?php
/**
 * Created by IntelliJ IDEA.
 *
 * @author Timothy Wilson <tim.wilson@aceviral.com>
 * @copyright AceViral.com LTD 2019
 * Date: 03/04/2019
 * Time: 12:02
 */

namespace Altrozero\GetAddressIOPHP\Test;

require 'vendor/autoload.php';

use Altrozero\GetAddressIOPHP\GetAddress;
use PHPUnit\Framework\TestCase;

class GetAddressTest extends TestCase
{
    /**
     * @var GetAddress $getAddress
     */
    public static $getAddress;

    public function setUp(): void
    {
        self::$getAddress = new GetAddress($GLOBALS['GETADDRESS_API_KEY']);
    }

    /**
     * Test that a bad postcode doesn't make a call
     *
     * @test
     */
    public function testBadPostcodeCatch()
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$getAddress->find('BANGARANG');
    }

    /**
     * Test that we get a list of addresses back correctly
     *
     * @test
     */
    public function testGetList()
    {
        $addresses = self::$getAddress->find('SR51NA');

        $this->assertObjectHasAttribute(
            'addresses',
            $addresses,
            "No addresses attribute in returned response");

        $this->assertGreaterThan(
            1,
            sizeof($addresses->addresses)
        );
    }

    /**
     * Test that we can get a specific address in detail
     *
     * @test
     */
    public function testGetAddress()
    {
        $address = self::$getAddress->find('SR51NA', 132, false, true, true);

        $this->assertObjectHasAttribute(
            'building_number',
            $address->addresses[0],
            "Failed to get the building number on a specific address"
        );
    }
}
