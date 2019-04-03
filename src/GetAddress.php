<?php
/**
 * Created by IntelliJ IDEA.
 *
 * @author Timothy Wilson <tim.wilson@aceviral.com>
 * Date: 14/02/2019
 * Time: 08:28
 */

namespace Altrozero\GetAddressIOPHP;

use Altrozero\GetAddressIOPHP\Exceptions\BadKeyException;
use Altrozero\GetAddressIOPHP\Exceptions\BadRequestException;
use Altrozero\GetAddressIOPHP\Exceptions\BadResponseException;
use Altrozero\GetAddressIOPHP\Exceptions\ConnectionException;
use Altrozero\GetAddressIOPHP\Exceptions\RequestLimitReachedException;
use \Exception;
use InvalidArgumentException;
use stdClass;

class GetAddress
{
    /**
     * Base URL for GetAddressIO
     */
    const URL_BASE = 'https://api.getAddress.io';

    /**
     * To hold the token from GetAddressIO for authentication
     *
     * @var string $apiKey
     */
    private $apiKey = '';

    public function __construct($apiKey)
        {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException;
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Fire of a request
     *
     * @param string $url Use _buildUrl to help create it
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws BadKeyException
     * @throws RequestLimitReachedExceptionr
     * @throws Exception
     *
     * @return mixed
     */
    private function request($url)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERPWD => "apikey:" . $this->apiKey
        ]);

        $returned = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (!self::validateHTTPCode($httpCode)) {
            return false;
        }

        curl_close($ch);

        return $returned;
    }

    /**
     * Check for valid HTTP response code, if not throws the appropriate exception
     *
     * @param string $code
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws BadKeyException
     * @throws RequestLimitReachedException
     * @throws Exception
     *
     * @return bool
     */
    public function validateHTTPCode($code)
    {
        switch ($code) {
            case 0:
                throw new ConnectionException('Issue with curl configuration or networking.');
            case 200:
                return true;
            case 404:
                throw new ConnectionException('Page not found!');
                break;
            case 400:
                throw new BadRequestException('Bad request, if find probably bad postcode');
                break;
            case 401:
                throw new BadKeyException('Invalid API Key');
                break;
            case 429:
                throw new RequestLimitReachedException('Too many requests made today. Upgrade for more.');
                break;
            case 500:
                throw new BadResponseException('GetAddress.io has problems, 99.99% SLA???!?!?!');
                break;
        }

        return false;
    }

    /**
     * Build the request URL
     *
     * @param Array|string $function (required)
     * @param array $parameters (optional)
     *
     * @return string
     */
    public function buildUrl($function, array $parameters = Array())
    {
        $url = self::URL_BASE;

        if (is_array($function)) {
            // Loop over the function parts
            foreach ($function as $part) {
                $url .= '/' . urlencode($part);
            }
        } else {
            $url .= urlencode($function);
        }

        if (is_array($parameters) && !empty($parameters)) {
            $url .= '?';

            foreach($parameters as $key => $parameter) {
                $url .= urlencode($key) . '=';

                if ($parameter === true) {
                    $url .= 'true';
                } else if ($parameter === false) {
                    $url .= 'false';
                } else {
                    $url .= urlencode($parameter);
                }

                $url .= '&';
            }
        }
        echo $url;

        return $url;
    }

    /**
     * Run a find request
     *
     * @param $postcode
     * @param null $house
     * @param bool $format
     * @param bool $sort
     * @param bool $expand
     * @return mixed
     * @throws BadKeyException
     * @throws BadRequestException
     * @throws BadResponseException
     * @throws ConnectionException
     * @throws InvalidArgumentException
     *
     * @return stdClass
     */
    public function find($postcode, $house = null, $format = false, $sort = true, $expand = false)
    {
        // Check it's not a bad postcode before we waste a call to the server
        if (!Postcode::validate($postcode)) {
            throw new \InvalidArgumentException;
        }

        // Build the function
        $function = [
            'find',
            $postcode
        ];

        if (!empty($house)) {
            $function[] = $house;
        }

        // Get the URL
        $url = $this->buildUrl(
            $function,
            [
                'format' => $format,
                'sort'   => $sort,
                'expand' => $expand
            ]
        );

        // Parse the response
        $response = $this->request($url);
        $response = json_decode($response);

        if ($response === null) { // Validate the response
            throw new BadResponseException('JSON Decode could not parse results');
        }

        // Return the response
        return $response;
    }
}