<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Services\Local\LocalUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StoreUserController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    public function __construct(LocalUsersRepository $localUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {

        $login = $request->getHeaderLine("login");
        $name = $request->getHeaderLine("name");
        $company = $request->getHeaderLine("company");
        $location = $request->getHeaderLine("location");
        try {
            $code=200;
            $error=false;            
            $id = $this->localUsersRepository->ultimoId();
            $perfil = $this->localUsersRepository->instanciarPerfil($name, $company, $location);
            $user = $this->localUsersRepository->instanciarUser($id, $login, $perfil);
            $this->localUsersRepository->add($user);
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
