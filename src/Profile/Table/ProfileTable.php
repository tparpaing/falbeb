<?php

namespace App\Profile\Table;

use Exception;
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
        $bapt = $this->getBaptId($p);
        if (is_null($bapt)) {
            return $bapt;
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

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|string[] Les infos de baptême de l'utilisateur
     */
    public function getBapt(int $p): ?array
    {
        $bId = $this->getBaptId($p);
        if (is_null($bId)) {
            return $bId;
        }

        $req = $this->pdo->prepare('
            SELECT * FROM bapt
            WHERE pk_id = ?');
        $req->execute([$bId]);

        if ($req->rowCount() === 1) {
            $bapt = $req->fetch();
            return [
                "timestamp" => strtotime($bapt->date),
                "date" => "BAPT" . date("y (d  ", strtotime($bapt->date)) . $this->getMonth(strtotime($bapt->date)) . date(" Y)", strtotime($bapt->date)),
                "lieu" => $bapt->lieu
            ];
        } else {
            throw new Exception("Entry not found in database");
        }
    }

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|int Le nom de la paillarde
     */
    public function getPaillarde(int $p): ?array
    {
        $req = $this->pdo->prepare('
            SELECT fk_paillarde FROM paillardes_auteurs
            WHERE fk_auteur = ?');
        $req->execute([$p]);

        if ($req->rowCount()) {
            $pId = $req->fetch()->fk_paillarde;

            $req = $this->pdo->prepare('
                SELECT titre FROM paillardes
                WHERE pk_id = ?');
            $req->execute([$pId]);
            return [
                'id' => $pId,
                'title' => $req->fetch()->titre
            ];
        }

        return null;
    }

    /**
     * @return array Les légendes
     */
    public function getLegend(): array
    {
        $req = $this->pdo->query('SELECT * FROM relations');
        $pms = $req->fetchAll(PDO::FETCH_ASSOC);

        $fillots = [];
        foreach($pms as $f) {
            array_push($fillots, str_replace('P/M', 'Fillot-e', str_replace('Grand-e', 'Petit-e', $f)));
        }

        return [
            'pms' => $pms,
            'fillots' => $fillots
        ];
    }

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|int L'id de baptême de l'utilisateur
     */
    private function getBaptId(int $p): ?int
    {
        $req = $this->pdo->prepare('SELECT fk_bapt FROM membres WHERE pk_id = ?');
        $req->execute([$p]);

        if ($req->rowCount() === 1) {
            return $req->fetch()->fk_bapt;
        } else {
            return null;
        }
    }

    /**
     * @param int $date La date
     * 
     * @return string Le mois en Français
     */
    private function getMonth(int $date): string
    {
        $months = [
            '01' => 'janvier',
            '02' => 'février',
            '03' => 'mars',
            '04' => 'avril',
            '05' => 'mai',
            '06' => 'juin',
            '07' => 'juillet',
            '08' => 'août',
            '09' => 'septembre',
            '10' => 'octobre',
            '11' => 'novembre',
            '12' => 'décembre'
        ];

        return $months[date('m',$date)];
    }
}
