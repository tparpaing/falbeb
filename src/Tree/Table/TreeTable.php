<?php

namespace App\Tree\Table;

use PDO;
use stdClass;

class TreeTable
{
	/**
	 * @var PDO
	 */
	private $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function findMember(int $id): ?stdClass
	{
		$req = $this->pdo->prepare('SELECT
            membres.nom,
            membres.prenom,
            membres.surnom,
            membres.tva,
            grades.nom as grade,
            grades.symbole as grade_symbole,
            bapt.nom as bapt,
            bapt.lieu as bapt_lieu,
            bapt.date as bapt_date
            FROM members
            JOIN grades ON membres.fk_grade = grades.pk_id
            JOIN bapt ON membres.fk_bapt = bapt.pk_id
            WHERE pk_id = ?
        ');
		$req->execute([$id]);
		if ($req->rowCount() === 1) {
			return $req->fetch();
		}
		return null;
	}

	public function getMembers(int $rank = -1): ?stdClass
	{
		if ($rank == -1) {
			$reqToAdd = " WHERE NOT(membres.fk_grades = ?)";
			$rank = 8;
		} else {
			$reqToAdd = " WHERE membres.fk_grades = ?";
		}
		$req = $this->pdo->prepare('SELECT
            membres.nom,
            membres.prenom,
            membres.surnom,
            grades.nom as grade,
            grades.symbole as grade_symbole,
            bapt.date as bapt_date
            FROM members
            JOIN grades ON membres.fk_grade = grades.pk_id
            JOIN bapt ON membres.fk_bapt = bapt.pk_id
        ' . $reqToAdd);
		$req->execute([$rank]);
		if ($req->rowCount() > 0) {
			return $req->fetch();
		}
		return null;
	}
}
