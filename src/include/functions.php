<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../include/constants.php';

function getJson($data) {
    return json_encode($data);
}

function fromJson($data) {
    return json_decode($data);
}

function hasTextAtIndex(&$array, $index) {
    return isset($array[$index]) && !empty($array[$index]);
}

/*
 * Build authorization data, as explained in the official documentation.
 * 
 * Client Credentials Flow
 * https://developer.spotify.com/documentation/general/guides/authorization-guide/
 */
function getSpotifyAuthorization() {
    return
        hasTextAtIndex($_SERVER, 'SPOTIFY_CLIENT_ID')
        &&
        hasTextAtIndex($_SERVER, 'SPOTIFY_CLIENT_SECRET')
            ? base64_encode($_SERVER['SPOTIFY_CLIENT_ID'] . ':' . $_SERVER['SPOTIFY_CLIENT_SECRET'])
            : null;
}

function resolveDefault (Request $request, Response $response, array $args) {
    return
        $response
            ->withHeader('Content-Type', MIME_TYPES['JSON'])
            ->write(
                getJson([
                    'exception' => 'Welcome to SpotPull! To keep going, please try again with one of the following endpoints: /api/v1/albums?band-name={band-name}'
                ])
            );
}

?>