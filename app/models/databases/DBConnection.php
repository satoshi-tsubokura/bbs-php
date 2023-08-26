<?php

namespace App\Models\Databases;

/**
 * データベースとのやり取りを行うためのクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
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
        $this->connect();
    }

    /**
     * インスタンス破棄時、データベースとの接続を切断する
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * データベースユーザー名を設定する
     *
     * @param string $username
     * @return void
     */
    public function setUserName(string $username): void
    {
        $this->username = $username;
    }

    /**
     * データベースパスワードを設定する
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * DSN文字列の設定
     *
     * @param string $driver
     * @param string $host
     * @param string $dbName
     * @param string $port
     * @param string $charset
     * @return void
     */
    public function setDsn(string $driver = 'mysql', string $host = 'localhost', string $dbName = 'bbs', string $port = '3306', string $charset = 'utf8mb4'): void
    {
        $this->dsn = "{$driver}:host={$host};dbname={$dbName};port:{$port};charset={$charset};";
    }

    /**
     * データベース接続処理を行う
     *
     * @return void
     */
    public function connect(): void
    {
        $this->db = new \PDO($this->dsn, $this->username, $this->password);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * データベース切断処理を行う
     *
     * @return void
     */
    public function close(): void
    {
        $this->db = null;
    }

    /**
     * トランザクション開始処理を行う
     *
     * @return boolean トランザクションが開始されたか
     */
    public function transaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * コミット処理を行う
     *
     * @return boolean コミット成功したか
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * ロールバック処理を行う
     *
     * @return boolean ロールバックが成功したか
     */
    public function rollback(): bool
    {
        return $this->db->rollback();
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
     * SELECTクエリの結果セットをすべて返す
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

    /**
     * SELECTクエリの結果セットの先頭を返す
     *
     * @param string $sql
     * @param array $parameterList
     * @return array|false 結果セットの配列を返す。結果セットが0行の場合はfalseを返す
     */
    public function fetchFirstResult(string $sql, array $parameterList = []): array|false
    {
        $this->executeQuery($sql, $parameterList);
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 最後に挿入した行の主キーを取得する
     *
     * @param string|null $name ID が返されるべきシーケンスオブジェクト名を指定します。
     * @return string|false 主キーを返す。主キーが取得できなかった場合、falseを返す
     */
    public function lastInsertId(?string $name = null): string|false
    {
        return $this->db->lastInsertId($name);
    }
}
