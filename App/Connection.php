<?php

namespace App;

class Connection {

	public static function getDb() {
		try {
			$conn = new \PDO(
				"mysql:host=localhost;dbname=db_twitter_clone;",
				"root",
				"S@mir123" 
			);
			return $conn;

		} catch (\PDOException $e) {
			//.. tratar de alguma forma ..//
			echo 'Erro capturado: ',  $e->getMessage(), "\n";
		}
	}
}

?>