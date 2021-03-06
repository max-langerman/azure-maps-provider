<?php

use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\AzureMaps\AzureMaps;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * Created by PhpStorm.
 * User: Max Langerman
 * Date: 7/15/20
 * Time: 10:40 PM
 */

class AzureMapsTest extends BaseTestCase
{

    /**
     * @return string|null the directory where cached responses are stored
     */
    protected function getCacheDir()
    {
        if (isset($_SERVER['USE_CACHED_RESPONSES']) && true === $_SERVER['USE_CACHED_RESPONSES']) {
            return __DIR__.'/.cached_responses';
        }

        return null;
    }

    public function testGeocodeWithRealAddress()
    {
        if (!isset($_SERVER['AZURE_MAPS_SUBSCRIPTION_KEY'])) {
            $this->markTestSkipped('You need to configure the AZURE_MAPS_SUBSCRIPTION_KEY value in phpunit.xml');
        }

        $subscriptionKey = $_SERVER['AZURE_MAPS_SUBSCRIPTION_KEY'];
        $provider = new AzureMaps($this->getHttpClient($subscriptionKey), $subscriptionKey);

        $results = $provider->geocodeQuery(GeocodeQuery::create('Yehuda Hamaccabi 15, Tel aviv'));

        $this->assertInstanceOf(AddressCollection::class, $results);
        $this->assertCount(4, $results);

        $result = $results->first();

        $this->assertInstanceOf(Address::class, $result);
        $this->assertEquals(32.09388, $result->getCoordinates()->getLatitude(), '', 0.001);
        $this->assertEquals(34.78596, $result->getCoordinates()->getLongitude(), '', 0.001);
        $this->assertNotNull($result->getBounds());
        $this->assertEquals(32.09298, $result->getBounds()->getSouth(), '', 0.001);
        $this->assertEquals(34.7849, $result->getBounds()->getWest(), '', 0.001);
        $this->assertEquals(32.09478, $result->getBounds()->getNorth(), '', 0.001);
        $this->assertEquals(34.78702, $result->getBounds()->getEast(), '', 0.001);
        $this->assertEquals(15, $result->getStreetNumber());
        $this->assertEquals('Yehuda Hamaccabi Street', $result->getStreetName());
        $this->assertEquals(6266924, $result->getPostalCode());
        $this->assertEquals('Israel', $result->getCountry()->getName());
        $this->assertEquals('IL', $result->getCountry()->getCode());
    }

    public function testReverseWithRealCoordinates()
    {
        if (!isset($_SERVER['AZURE_MAPS_SUBSCRIPTION_KEY'])) {
            $this->markTestSkipped('You need to configure the AZURE_MAPS_SUBSCRIPTION_KEY value in phpunit.xml');
        }

        $subscriptionKey = $_SERVER['AZURE_MAPS_SUBSCRIPTION_KEY'];
        $provider = new AzureMaps($this->getHttpClient($subscriptionKey), $subscriptionKey);
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(32.09388,34.78596));

        $this->assertInstanceOf(AddressCollection::class, $results);
        $this->assertCount(1, $results);

        $result = $results->first();

        $this->assertInstanceOf(Address::class, $result);
        $this->assertEquals(32.093769, $result->getCoordinates()->getLatitude(), '');
        $this->assertEquals(34.785957, $result->getCoordinates()->getLongitude(), '');
        $this->assertNotNull($result->getBounds());
        $this->assertEquals(32.093765, $result->getBounds()->getSouth(), '');
        $this->assertEquals(34.785731, $result->getBounds()->getWest(), '');
        $this->assertEquals(32.093772, $result->getBounds()->getNorth(), '');
        $this->assertEquals(34.786375, $result->getBounds()->getEast(), '');
        $this->assertEquals(15, $result->getStreetNumber());
        $this->assertEquals('Yehuda Hamaccabi Street', $result->getStreetName());
        $this->assertEquals(6266924, $result->getPostalCode());
        $this->assertEquals('Israel', $result->getCountry()->getName());
        $this->assertEquals('IL', $result->getCountry()->getCode());
    }
}