<?php

namespace Osana\Challenge\Services\Local;

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

class LocalUsersRepository implements UsersRepository
{
    public function findByLogin(Login $login, int $limit = 0): Collection
    {
        $usuarios = fopen('..\data\users.csv', "r");
        $users = new Collection();
        while ($user = fgetcsv($usuarios)) {
            if (strncasecmp($user[1], $login->getValue(), strlen($login->getValue())) === 0) {
                $users->add($user);
                break;
            }
        }
        fclose($usuarios);

        return $users;
    }

    public function getByLogin(Login $login, int $limit = 0): User
    {
        $usuarios = fopen('..\data\users.csv', "r");
        while ($user = fgetcsv($usuarios)) {
            if ($user[1] == $login->getValue()) {
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
    public function instanciarUser(String $id, String $login, Profile $perfil)
    {
        $id = new Id($id);
        $login = new Login($login);
        $local = Type::Local();
        return  new User($id, $login, $local, $perfil);
    }


    public function add(User $user): void
    {

        $usuarios = fopen('..\data\users.csv', "a");
        $cadena = array($user->getId()->getValue(), $user->getLogin()->getValue(), $user->getType()->getValue());
        fputcsv($usuarios, $cadena);
        fclose($usuarios);
        $perfiles = fopen('..\data\profiles.csv', "a");
        $perfil = array($user->getId()->getValue(), $user->getProfile()->getCompany()->getValue(), $user->getProfile()->getLocation()->getValue(), $user->getProfile()->getName()->getValue());
        fputcsv($perfiles, $perfil);
        fclose($perfiles);
    }

    public function ultimoId(): String
    {
        $users = fopen('..\data\users.csv', "r");
        while ($user = fgetcsv($users)) {
            $id = $user[0];
        }
        return $id;
    }
}
