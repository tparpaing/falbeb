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
        $req = $this->pdo->prepare('
            SELECT *,membres.pk_id,membres.nom as nom,grades.nom as g_nom FROM membres
            LEFT JOIN grades ON grades.pk_id = membres.fk_grade
            WHERE membres.pk_id = ?');
        $req->execute([$p]);
        if ($req->rowCount() === 1) {
            return $req->fetch();
        }
        return null;
    }

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|stdClass[] Les P/M de l'utilisateur
     */
    public function getPM(int $p): ?array
    {
        $req = $this->pdo->prepare('
            SELECT * FROM liens
            LEFT JOIN membres ON membres.pk_id = liens.fk_pere
            LEFT JOIN relations ON relations.pk_id = liens.fk_relation
            WHERE fk_fils = ?
            ORDER BY fk_relation ASC, surnom DESC'
        );
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

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|stdClass[] Les fillot-e-s de l'utilisateur
     */
    public function getFillots(int $p): ?array
    {
        $req = $this->pdo->prepare('
            SELECT * FROM liens
            LEFT JOIN membres ON membres.pk_id = liens.fk_fils
            LEFT JOIN relations ON relations.pk_id = liens.fk_relation
            WHERE fk_pere = ?
            ORDER BY fk_relation, surnom');
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

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|stdClass[] Les adelphes de l'utilisateur
     */
    public function getAdelphes(int $p): ?array
    {
        $req = $this->pdo->prepare('SELECT fk_bapt FROM membres WHERE pk_id = ?');
        $req->execute([$p]);

        if ($req->rowCount() === 1) {
            $bapt = $req->fetch()->fk_bapt;
        } else {
            return null;
        }

        $req = $this->pdo->prepare('
            SELECT * FROM membres
            WHERE fk_bapt = ?
            ORDER BY surnom');
        $req->execute([$bapt]);

        $retArr = [];
        while ($user = $req->fetch()) {
            if ($user->pk_id !== $p) {
                array_push($retArr, $user);
            }
        }

        if (!empty($retArr)) {
            return $retArr;
        }
        return null;
    }
}
