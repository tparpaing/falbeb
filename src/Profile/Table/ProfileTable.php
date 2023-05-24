<?php

namespace App\Profile\Table;

use Framework\App;
use PDO;
use stdClass;

class ProfileTable
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return stdClass[] Tableau des résultats
     */
    public function getAll(): array
    {
        $req = $this->pdo->query('SELECT * FROM membres');
        return $req->fetchAll();
    }

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|stdClass L'utilisateur
     */
    public function find(int $p): ?stdClass
    {
        $req = $this->pdo->prepare('SELECT * FROM membres WHERE pk_id = ?');
        $req->execute([$p]);
        if ($req->rowCount() === 1) {
            return $req->fetch();
        }
        return null;
    }

    /**
     * 
     */
    public function getPM(int $p): ?array
    {
        $req = $this->pdo->prepare('SELECT * FROM liens LEFT JOIN membres ON membres.pk_id = liens.fk_pere WHERE fk_fils = ?');
        $req->execute([$p]);

        $retArr = [];
        while ($user = $req->fetch()) {
            array_push($retArr, $user);
        }

        if (!empty($retArr)) {
            return $retArr;
        }
        return null;
    }

    public function getFillots(int $p): ?array
    {
        return null;
    }
}
