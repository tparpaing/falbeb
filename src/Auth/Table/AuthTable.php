<?php

namespace App\Auth\Table;

use Framework\App;
use PDO;
use stdClass;

class AuthTable
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(string $email, string $password): ?stdClass
    {
        $id = $this->validateUserLogin($email, $password);
        if ($id != null) {
            $req = $this->pdo->prepare('SELECT
                users.pk_id as id,
                users.email,
                users.login,
                users.password,
                users.fk_membre,
                membres.nom,
                membres.prenom,
                membres.surnom,
                membres.tva,
                grades.nom as grade,
                grades.symbole as grade_symbole,
                bapt.nom as bapt,
                bapt.lieu as bapt_lieu,
                bapt.date as bapt_date
                FROM users
                JOIN membres ON users.fk_membre = membres.pk_id
                JOIN grades ON membres.fk_grade = grades.pk_id
                JOIN bapt ON membres.fk_bapt = bapt.pk_id
                WHERE users.pk_id = ?
            ');
            $req->execute([$id]);
            return $req->fetch();
        }
        return null;
    }

    /**
     * Retourne l'id du membre si la combinaison login/password est correcte, retourne null sinon
     * 
     * @param string $email L'adresse e-mail
     * @param string $password Le mot de passe
     * @return null|int L'id du membre si correct, null sinon
     */
    private function validateUserLogin(string $email, string $password): ?int
    {
        $req = $this->pdo->prepare('SELECT pk_id,password FROM users WHERE email = ?
        ');
        $req->execute([$email]);
        if ($req->rowCount() === 1) {
            $res = $req->fetch();
            if (password_verify($password, $res->password)) {
                return $res->pk_id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
