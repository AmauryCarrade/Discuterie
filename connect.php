<?php
	function SQLiteConnect() {
		$database = 'data/data.sqlite';
		$pdo = NULL;

		try {
			$pdo = new PDO('sqlite:' . $database);
		}
		catch(Exception $e) {
			die('Erreur : impossible de charger la base de données. <br />'.$e->getMessage());
		}

		return $pdo;
	}
