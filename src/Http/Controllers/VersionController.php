<?php

namespace Osana\Challenge\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VersionController
{
    public function __invoke(Request $request, Response $response): Response
    {

        try {           
            $data = [
                'name' => env('API_NAME'),
                'version' => env('API_VERSION')
            ];
        } catch (\Exception $ex) {            
            $data = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }
        $response->getBody()->write(json_encode($data));

        return $response->withStatus(200, 'OK')
            ->withHeader('Content-Type', 'application/json');
    }
}
