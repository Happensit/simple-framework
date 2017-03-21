<?php

namespace Commty\Simple\Database;

use PDO;
use Commty\Simple\Exception\DatabaseException;

/**
 * Class SqlManager working only for pgsql, mysql, mysqli
 * @package commty\Database
 *
 * @todo Не надо так o_0
 *
 */
class SqlManager
{
    /**
     * @var string
     */
    protected $dsn = '';

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $tablePrefix = '4yu_';

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * Connection constructor.
     * @param $dsn
     * @param $username
     * @param $password
     * @param $tablePrefix
     */
    public function __construct($dsn, $username, $password, $tablePrefix)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @return PDO
     * @throws DatabaseException
     */
    private function createPdoInstance()
    {

        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET lc_time_names='ru_RU', NAMES utf8",
                PDO::MYSQL_ATTR_LOCAL_INFILE => true
            ]);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            throw new DatabaseException("Database connection could not be established. ;( Reason: " . $e->getMessage());
        }

        return $this->pdo;
    }

    /**
     * @return PDO|SqlManager
     */
    public function getPdoInstance()
    {
        if (!isset($this->pdo)) {
            return $this->createPdoInstance();
        }

        return $this->pdo;
    }

    /**
     * @param $sql
     * @param array $params
     * @return $this
     */
    public function query($sql, $params = [])
    {
        $this->prepare($sql);
        $this->statement->execute($params);
        return $this;
    }

    /**
     * @param $className Entity
     * @return mixed
     */
    public function findOne($className)
    {
        $this->statement->setFetchMode(PDO::FETCH_CLASS, get_class($className));
        return $this->statement->fetch();
    }

    /**
     * @param $className Entity
     * @return array|false
     */
    public function findAll($className)
    {
        return $this->statement->fetchAll(PDO::FETCH_CLASS, get_class($className));
    }

    /**
     * @return mixed
     */
    public function findCount()
    {
        return $this->statement->fetchColumn();
    }

    /**
     * update('{{user}}', ['status'=>1], 'id=:id', [':id'=>2]);
     * @param $table
     * @param $columns
     * @param string $conditions
     * @param array $params
     * @return mixed
     */
    public function update($table, $columns, $conditions = '', $params = [])
    {
        $lines = array();

        foreach ($columns as $name => $value) {
            $lines[] = $name . '=:' . $name;
            $params[':' . $name] = $value;

        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $lines);
        $sql .= ' WHERE ' . $conditions;

        return $this->execute($sql, $params);
    }

    /**
     * @param $sql
     * @param array $params
     * @return bool
     */
    public function execute($sql, $params = [])
    {
        $this->prepare($sql);
        $this->statement->execute($params);
        $rowCount = $this->statement->rowCount();
        $this->statement->closeCursor();
        $this->statement = null;
        return $rowCount;
    }

    /**
     * Get PDO statement
     * @param $sql
     * @return mixed
     * @throws DatabaseException
     */
    private function prepare($sql)
    {
        try {
            $this->statement = $this->getPdoInstance()->prepare($this->normalizeTableName($sql));
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        return $this->statement;
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function normalizeTableName($sql)
    {
        return preg_replace('/{{(.*?)}}/', $this->getTablePrefix() . '\1', $sql);
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param string $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }
}
