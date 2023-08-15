<?php

namespace App\Models\Databases;

class DBConnection implements IConnection
{
    private string $username;
    private string $password;
    private string $dsn;
    private ?\PDO $db;
    private \PDOStatement $statement;

    /**
     * @param string|null $username DBに接続する際のユーザー名
     * @param string|null $password DBに接続する際のパスワード
     * @param array{
     *  driver: string,
     *  host: string,
     *  db_name: string,
     *  port: string,
     *  charset: string,
     * } $dsnOptions dsn文字列の値
     */
    public function __construct(string $username = null, string $password = null, array $dsnOptions = [])
    {
        $this->setUserName($username ?? $_ENV['DB_USERNAME']);
        $this->setPassword($password ?? $_ENV['DB_PASSWORD']);
        $this->setDsn(
            $dsnOptions['driver'] ?? $_ENV['DB_DRIVER'],
            $dsnOptions['host'] ?? $_ENV['DB_HOSTNAME'],
            $dsnOptions['db_name'] ?? $_ENV['DB_NAME'],
            $dsnOptions['port'] ?? $_ENV['DB_PORT'],
            $dsnOptions['charset'] ?? $_ENV['DB_CHARSET']
        );
    }

    public function __destruct()
    {
        $this->close();
    }

    public function setUserName(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setDsn(string $driver = 'mysql', string $host = 'localhost', string $dbName = 'bbs', string $port = '3306', string $charset = 'utf8mb4'): void
    {
        $this->dsn = "{$driver}:host={$host};dbname={$dbName};port:{$port};charset={$charset};";
    }

    public function connect(): void
    {
        $this->db = new \PDO($this->dsn, $this->username, $this->password);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function close(): void
    {
        $this->db = null;
    }

    /**
     * SQLの実行のみをするメソッド
     *
     * @param string $sql
     * @param array $parameterList プリペアードステートメントのキーと値の設定値配列
     * @return boolean クエリが成功したかどうか
     */
    public function executeQuery(string $sql, array $parameterList = []): bool
    {
        $this->statement = $this->db->prepare($sql);
        return $this->statement->execute($parameterList);
    }

    /**
     * SELECT文字の結果セットをすべて返す
     *
     * @param string $sql
     * @param array $parameterList プリペアードステートメントのキーと値の設定値配列
     * @return array 結果セットの配列
     * @throws PDOException
     */
    public function fetchResultsAll(string $sql, array $parameterList = []): array
    {
        $this->executeQuery($sql, $parameterList);
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchFirstResult(string $sql, array $parameterList = []): array
    {
        $this->executeQuery($sql, $parameterList);
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return $this->db->lastInsertId($name);
    }
}
