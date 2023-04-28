<?php

namespace App\Paillardes\Table;

use Framework\App;
use PDO;
use stdClass;

class PaillardesTable
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
        $req = $this->pdo->query('SELECT * FROM paillardes');
        return $req->fetchAll();
    }

    public function find(int $p): ?stdClass
    {
        $req = $this->pdo->prepare('SELECT * FROM paillardes WHERE pk_id = ?');
        $req->execute([$p]);
        if ($req->rowCount() === 1) {
            return $req->fetch();
        }
        return null;
    }

    /**
     * @return stdClass[] Tableau des résultats
     */
    public function getAuthors(int $p): array
    {
        $req = $this->pdo->prepare('SELECT m.pk_id, m.nom, m.prenom, m.surnom FROM paillardes_auteurs AS pa JOIN membres AS m ON pa.fk_auteur = m.pk_id WHERE pa.fk_paillarde = ? ');
        $req->execute([$p]);
        return $req->fetchAll();
    }

    /**
     * @param stdClass La paillarde
     * @return string
     */
    public function getOriginal(stdClass $p): string
    {
        $formatted = "";
        if ($p->o_titre != NULL) {
            $formatted .= "<span class=\"italic\">" . $p->o_titre . "</span>";
        } else {
            $formatted .= "<span class=\"italic\">Titre inconnu</span>";
        }
        if ($p->o_auteur != NULL) {
            $formatted .= " - " . $p->o_auteur;
        }
        if ($p->o_date != NULL) {
            $formatted .= " (" . date('Y', strtotime($p->o_date)) . ")";
        }
        return $formatted;
    }
}
