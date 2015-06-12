<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;

/**
 * The TicketComments class exposes comment methods for tickets
 * @package Zendesk\API
 */
class TicketComments extends ResourceAbstract
{

    const OBJ_NAME = 'comment';
    const OBJ_NAME_PLURAL = 'comments';

    protected function setUpRoutes()
    {
        $this->setRoutes(
          [
            'findAll'     => 'tickets/{ticket_id}/comments.json',
            'makePrivate' => 'tickets/{ticket_id}/comments/{id}/make_private.json'
          ]
        );
    }

    /**
     * Returns all comments for a particular ticket
     *
     * @param array $queryParams
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll( array $queryParams = array() )
    {
        $queryParams = $this->addChainedParametersToParams( $queryParams, [ 'ticket_id' => Tickets::class ] );

        if ( ! $this->hasKeys( $queryParams, array( 'ticket_id' ) )) {
            throw new MissingParametersException( __METHOD__, array( 'ticket_id' ) );
        }

        return parent::findAll( $queryParams );
    }

    /**
     * Make the specified comment private
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function makePrivate( array $params = array() )
    {
        $params = $this->addChainedParametersToParams( $params,
          [ 'id' => get_class( $this ), 'ticket_id' => Tickets::class ] );

        if ( ! $this->hasKeys( $params, array( 'id', 'ticket_id' ) )) {
            throw new MissingParametersException( __METHOD__, array( 'id', 'ticket_id' ) );
        }

        $response = Http::send_with_options(
          $this->client,
          $this->getRoute( __FUNCTION__, $params ),
          [ 'method' => 'PUT' ]
        );

        $this->client->setSideload( null );

        return $response;
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws CustomException
     */
    public function find( $id = null, array $queryQueryParams = array() )
    {
        throw new CustomException( 'Method ' . __METHOD__ . ' does not exist. Try $client->ticket(ticket_id)->comments()->findAll() instead.' );
    }

}
