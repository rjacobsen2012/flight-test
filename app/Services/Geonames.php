<?php

namespace App\Services;

use App\Contracts\Services\GeonamesContract;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Geonames
 * @package App\Services
 */
class Geonames implements GeonamesContract
{
    protected $client;

    /**
     * Geonames constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $lat
     * @param $long
     * @return array
     */
    public function getAddress($lat, $long)
    {
        $address = null;

        try {
            $url = $this->getAddressUrl($lat, $long);
            $address = (array) $this->query($url, true)->address;
        } catch (GuzzleException $e) {
            catch_and_return('There was a problem retrieving the address data.', $e);
        }

        return $address;
    }

    /**
     * @param $lat
     * @param $long
     * @return array
     */
    public function getCountry($lat, $long)
    {
        $country = null;

        try {
            $url = $this->getCountryUrl($lat, $long);
            $country = (array) $this->query($url)->countryName;
        } catch (GuzzleException $e) {
            catch_and_return('There was a problem retrieving the country data.', $e);
        }

        return $country;
    }

    /**
     * @param $lat
     * @param $long
     * @return string
     */
    protected function getAddressUrl($lat, $long): string
    {
        $url = config('geonames.url') .
            "/extendedFindNearby?lat={$lat}&lng={$long}&username=" .
            config('geonames.user');
        return $url;
    }

    /**
     * @param $lat
     * @param $long
     * @return string
     */
    protected function getCountryUrl($lat, $long): string
    {
        $url = config('geonames.url') .
            "/countryCodeJSON?lat={$lat}&lng={$long}&username=" .
            config('geonames.user');
        return $url;
    }

    /**
     * @param string $url
     * @return mixed
     * @throws GuzzleException
     */
    protected function query(string $url, $rest = false)
    {
        if ($rest) {
            $response = $this->client->request(
                'GET',
                $url,
                ['headers' => ['Accept' => 'application/json', 'Content-type' => 'application/json']]);
        } else {
            $response = $this->client->request('GET', $url);
        }

        return json_decode($response->getBody()->getContents());
    }
}
