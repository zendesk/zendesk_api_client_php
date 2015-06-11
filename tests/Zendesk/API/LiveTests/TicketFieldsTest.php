<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Resources\TicketFields;

/**
 * Ticket Fields test class
 */
class TicketFieldsTest extends BasicTest
{
    protected $id = 123;

    public function testCreate()
    {
        $postFields = [
          'type'  => 'text',
          'title' => 'Age'
        ];
        $this->mockApiCall(
          'POST',
          'ticket_fields.json',
          [
            'ticket_field' =>
              [
                'id'    => $this->id,
                'type'  => $postFields['type'],
                'title' => $postFields['title']
              ]
          ],
          [
            'bodyParams' => [
              TicketFields::OBJ_NAME => $postFields
            ],
            'statusCode' => '201'
          ]
        );

        $field = $this->client->ticketFields()->create( $postFields );
        $this->httpMock->verify();
        $this->assertEquals( is_object( $field->ticket_field ), true, 'Should return an object called "ticket_field"' );
        $this->assertGreaterThan( 0, $field->ticket_field->id, 'Returns a non-numeric id for ticket_field' );
        $this->assertEquals( $field->ticket_field->type, 'text', 'Type of test ticket field does not match' );
        $this->assertEquals( $field->ticket_field->title, 'Age', 'Title of test ticket field does not match' );
    }

    public function testAll()
    {
        $this->mockApiCall(
          'GET',
          'ticket_fields.json',
          [
            'ticket_fields' => [
              [
                'id' => 1
              ],
              [
                'id' => 1
              ],
            ]
          ]
        );
        $fields = $this->client->ticketFields()->findAll();
        $this->httpMock->verify();
        $this->assertEquals( is_array( $fields->ticket_fields ), true,
          'Should return an object containing an array called "ticket_fields"' );
        $this->assertGreaterThan( 0, $fields->ticket_fields[0]->id, 'Returns a non-numeric id in first ticket field' );
    }

    public function testFind()
    {
        $this->mockApiCall(
          'GET',
          "ticket_fields/{$this->id}.json",
          [
            'ticket_field' => [
              'id' => $this->id
            ]
          ]
        );
        $fields = $this->client->ticketField( $this->id )->find();
        $this->assertEquals( is_object( $fields->ticket_field ), true,
          'Should return an object called "ticket_field"' );
        $this->assertEquals( $this->id, $fields->ticket_field->id, 'Returns an incorrect id in ticket field object' );
    }

    public function testUpdate()
    {
        $bodyParams = [ 'title' => 'Another value' ];
        $this->mockApiCall(
          'PUT',
          "ticket_fields/{$this->id}.json",
          [
            'ticket_field' => [
              'id'    => $this->id,
              'type'  => 'text',
              'title' => 'Another value'
            ]
          ],
          [
            'bodyParams' => [
              TicketFields::OBJ_NAME => $bodyParams
            ]
          ]
        );

        $field = $this->client->ticketField( $this->id )->update(
          null,
          $bodyParams
        );
        $this->httpMock->verify();

        $this->assertEquals( is_object( $field->ticket_field ), true, 'Should return an object called "ticket_field"' );
        $this->assertGreaterThan( 0, $field->ticket_field->id, 'Returns a non-numeric id for ticket_field' );
        $this->assertEquals( $field->ticket_field->type, 'text', 'Type of test ticket field does not match' );
        $this->assertEquals( $field->ticket_field->title, 'Another value',
          'Title of test ticket field does not match' );
    }

    public function testDelete()
    {
        $this->mockApiCall(
          'DELETE',
          "ticket_fields/{$this->id}.json",
          [ true ]
        );
        $this->client->ticketField( $this->id )->delete();
    }
}
