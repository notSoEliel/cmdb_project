<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase Database
 *
 * Implementa el patrón Singleton para garantizar una única conexión a la base de datos.
 */
class Database {
    /** @var self La única instancia de la clase. */
    private static $instance = null;

    /** @var PDO La conexión PDO. */
    private $connection;

    /** @var PDOStatement El objeto de la declaración preparada. */
    private $statement;

    /**
     * El constructor es privado. Establece la conexión a la base de datos.
     */
    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Nunca muestres errores detallados en producción.
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Previene la clonación de la instancia.
     */
    private function __clone() {}

    /**
     * Método estático que controla el acceso a la instancia Singleton.
     *
     * @return self La instancia de la clase Database.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prepara y ejecuta una consulta SQL.
     *
     * @param string $sql La consulta SQL.
     * @param array $params Los parámetros para la consulta.
     * @return self
     */
    public function query($sql, $params = []) {
        try {
            $this->statement = $this->connection->prepare($sql);
            $this->statement->execute($params);
        } catch (PDOException $e) {
            die('Error en la consulta: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Obtiene todos los resultados de la consulta.
     * @return array
     */
    public function get() {
        return $this->statement->fetchAll();
    }

    /**
     * Obtiene el primer resultado de la consulta.
     * @return mixed
     */
    public function find() {
        return $this->statement->fetch();
    }

    /**
     * Obtiene el ID del último registro insertado.
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
}