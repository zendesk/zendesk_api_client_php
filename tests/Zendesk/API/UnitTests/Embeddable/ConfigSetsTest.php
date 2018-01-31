<?php

namespace Zendesk\API\UnitTests\Embeddable;

use Faker\Factory;
use Zendesk\API\UnitTests\BasicTest;

class ConfigSetsTest extends BasicTest
{
    /**
     * Tests if the client can call and build the create config sets endpoint
     */
    public function testCreate()
    {
        $faker = Factory::create();
        $params = [
            'color' => $faker->hexColor,
            'position' => $faker->randomElement(['left', 'right']),
        ];

        $this->assertEndpointCalled(function () use ($params) {
             $this->client->embeddable->configSets()->create($params);
        }, 'embeddable/api/config_sets.json', 'POST', [
            'apiBasePath' => '/',
            'postFields' => ['config_set' => $params],
        ]);
    }

    /**
     * Tests if the client can call and build the update config sets endpoint
     */
    public function testUpdate()
    {
        $faker = Factory::create();
        $id = $faker->numberBetween(1);
        $params = [
            'color' => $faker->hexColor,
            'position' => $faker->randomElement(['left', 'right']),
        ];

        $this->assertEndpointCalled(function () use ($params, $id) {
            $this->client->embeddable->configSets()->update($id, $params);
        }, "embeddable/api/config_sets/{$id}.json", 'PUT', [
            'apiBasePath' => '/',
            'postFields' => ['config_set' => $params],
        ]);
    }
}
