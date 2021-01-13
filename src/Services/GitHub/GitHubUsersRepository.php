<?php

namespace Osana\Challenge\Services\GitHub;

use Osana\Challenge\Domain\Users\Company;
use Osana\Challenge\Domain\Users\Id;
use Osana\Challenge\Domain\Users\Location;
use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Name;
use Osana\Challenge\Domain\Users\Profile;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Domain\Users\User;
use Osana\Challenge\Domain\Users\UsersRepository;
use Tightenco\Collect\Support\Collection;

class GitHubUsersRepository implements UsersRepository
{
    public function findByLogin(Login $name, int $limit = 0): Collection
    {
        $usuarios = fopen('..\data\users.csv', "r");      
        $users = new Collection();
        while ($user = fgetcsv($usuarios)) {
            if (strncasecmp($user[1], $name->getValue(), strlen($name->getValue())) === 0) {
                $users->add($user);
                break;
            }
        }

        fclose($usuarios);
        return $users;
    }

    public function getByLogin(Login $name, int $limit = 0): User
    {

        $usuarios = fopen('..\data\users.csv', "r");
        while ($user = fgetcsv($usuarios)) {
            if ($user[1] == $name->getValue()) {
                $perfiles = fopen('..\data\profiles.csv', "r");
                while ($perfil = fgetcsv($perfiles)) {
                    if ($perfil[0] == $user[0]) {
                        $perfil = $this->instanciarPerfil($perfil[3], $perfil[1], $perfil[2]);
                        $user = $this->instanciarUser($user[0], $user[1], $perfil);
                        return $user;
                      
                       
                    }
                }
            }
        }
    }
    public function instanciarPerfil(String $nombre, String $compania, String $localidad)
    {
        $nombre = new Name($nombre);
        $company = new Company($compania);
        $location = new Location($localidad);
        return new Profile($nombre, $company, $location);
    }
    public function instanciarUser(String $id, String $login,Profile $perfil)
    {
        $id = new Id($id);
        $login = new Login($login);
        $local = Type::GitHub();
        return  new User($id, $login, $local, $perfil);
    }

    public function add(User $user): void
    {
        throw new OperationNotAllowedException();
    }
    public function ultimoId():String 
    {
        throw new OperationNotAllowedException();


    }
}
