<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Default responses
$app->get('/', function (Request $request, Response $response, array $args) {
    return resolveDefault($request, $response, $args);
});

$app->post('/', function (Request $request, Response $response, array $args) {
    return resolveDefault($request, $response, $args);
});

// Main endpoint
$app->get('/api/v1/albums', function (Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-Type', MIME_TYPES['JSON']);

    // Prepare a base response
    $responseData = [ 'result' => null, 'exception' => null ];

    // Get the parameters that came with the request
    $params = $request->getQueryParams();

    if (hasTextAtIndex($params, 'band-name')) {
        $bandName = $params['band-name'];

        if (
            hasTextAtIndex($_SERVER, 'SPOTIFY_CLIENT_ID')
            &&
            hasTextAtIndex($_SERVER, 'SPOTIFY_CLIENT_SECRET')
        ) {
            $client = new GuzzleHttp\Client([ 'base_uri' => SPOTIFY_API_BASE ]);

            try {
                $tokenResponse = $client->post(SPOTIFY_TOKEN_PROVIDER, [
                    'headers'       => [ 'Authorization' => 'Basic ' . getSpotifyAuthorization() ],
                    'form_params'   => [
                        'grant_type'    => 'client_credentials'
                    ]
                ]);

                $token = fromJson($tokenResponse->getBody()->getContents())->access_token;

                $authHeaders = [ 'Authorization' => 'Bearer ' . $token ];

                $searchResponse = $client->get('search', [
                    'headers'       => $authHeaders,
                    'query'         => [
                        'q'             => $bandName,
                        'type'          => 'artist'
                    ]
                ]);

                $searchResponse = fromJson($searchResponse->getBody()->getContents());

                $artists = &$searchResponse->artists->items;

                if (count($artists) > 0) {
                    $artist = &$artists[0];

                    $albumsResponse = $client->get('artists/' . $artist->id . '/albums', [
                        'headers'       => $authHeaders,
                        'query'         => [ 'ids' => $artist->id ]
                    ]);

                    $albums = fromJson($albumsResponse->getBody()->getContents());

                    foreach ($albums->items as &$album) {
                        $responseData['result'][] = [
                            'name'      => $album->name,
                            'released'  => $album->release_date,
                            'tracks'    => $album->total_tracks,
                            'cover'     => [
                                    'height'    => $album->images[0]->height,
                                    'width'     => $album->images[0]->width,
                                    'url'       => $album->images[0]->url
                            ]
                        ];
                    }
                } else {
                    $responseData['result'] = [];
                }

                $response = $response->write(getJson($responseData));
            } catch (GuzzleHttp\Exception\ClientException $exception) {
                $responseData['exception'] = 
                    $exception->hasResponse()
                        ? fromJson($exception->getResponse()->getBody()->getContents())
                        : $exception->getMessage();

                $response = $response
                    ->withStatus(HTTP_STATUS['INTERNAL_SERVER_ERROR'])
                    ->write(getJson($responseData));
            }
        } else {
            $responseData['exception'] = 'Unable to process request, please ensure that the environment variables "SPOTIFY_CLIENT_ID" and "SPOTIFY_CLIENT_SECRET" are present, restart the server and try again.';

            $response = $response
                ->withStatus(HTTP_STATUS['INTERNAL_SERVER_ERROR'])
                ->write(getJson($responseData));
        }
    } else {
        $responseData['exception'] = 'Bad request, to keep going, the parameter "band-name" must be present and contain at least one character.';

        $response =
            $response
                ->withStatus(HTTP_STATUS['BAD_REQUEST'])
                ->write(getJson($responseData));
    }

    return $response;
});

?>