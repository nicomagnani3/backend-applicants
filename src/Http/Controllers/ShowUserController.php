<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Services\Local\LocalUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Osana\Challenge\Services\GitHub\GitHubUsersRepository;

class ShowUserController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    /** @var GitHubUsersRepository */
    private $gitHubUsersRepository;
    public function __construct(LocalUsersRepository $localUsersRepository, GitHubUsersRepository $gitHubUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
        $this->gitHubUsersRepository = $gitHubUsersRepository;
    }

    public function __invoke(Request $request, Response $response, array $params): Response
    {
        $type = new Type($params['type']);
        $login = new Login($params['login']);

        try {
            $code = 200;
            $error = false;
            if ($type == 'local') {
                $user = $this->localUsersRepository->getByLogin($login);
            } else {
                $user = $this->gitHubUsersRepository->getByLogin($login);
            }

            if ($user != NULL) {
                $data = [
                    "id" => $user->getId()->getValue(),
                    "login" =>  $user->getLogin()->getValue(),
                    "type" =>  $user->getType(),
                    "profile" => [
                        "name" => $user->getProfile()->getName()->getValue(),
                        "company" => $user->getProfile()->getCompany()->getValue(),
                        "location" => $user->getProfile()->getLocation()->getValue()

                    ]
                ];
            }
        } catch (\Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }
        $respuesta = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $data : $message,
        ];
        $response->getBody()->write(json_encode($respuesta));

        return $response->withStatus(200, 'OK')
            ->withHeader('Content-Type', 'application/json');
    }
}
