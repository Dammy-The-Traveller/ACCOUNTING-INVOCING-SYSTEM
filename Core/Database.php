<?php 
namespace Core;
use PDO;
use PDOException;

/**
 * Class Database
 *
 * A simple database abstraction layer for MySQL using PDO.
 *
//  * @property PDO $connection The PDO connection instance.
//  * @property PDOStatement $statement The last prepared statement.
 *
 * @method __construct(array $config, string|null $username = null, string|null $password = null)
 *     Constructs a new Database instance and establishes a PDO connection.
 *     Falls back to environment variables for username and password if not provided.
 *
 * @method Database query(string $query, array $params = [])
 *     Prepares and executes a SQL query with optional parameters.
 *     Returns the current Database instance for chaining.
 *
 * @method array get()
 *     Fetches all results from the last executed statement as an associative array.
 *
 * @method array|false find()
 *     Fetches the next row from the last executed statement as an associative array.
 *     Returns false if there are no more rows.
 *
 * @method array findOrFail()
 *     Fetches the next row from the last executed statement.
 *     Calls abort() if no result is found.
 *
 * @method void beginTransaction()
 *     Begins a new database transaction.
 *
 * @method void commit()
 *     Commits the current transaction.
 *
 * @method void rollBack()
 *     Rolls back the current transaction.
 *
 * @method int rowCount()
 *     Returns the number of rows affected by the last SQL statement.
 *
 * @method string lastInsertId()
 *     Returns the ID of the last inserted row.
 */
class Database
{
    public $connection;
    public $statement;

    public function __construct($config, $username = null, $password = null, $driver = null)
    {

         // Fallback to environment variables if username or password is not provided
    $username = $username ?? $_ENV['DB_USERNAME'];
    $password = $password ?? $_ENV['DB_PASSWORD'];
    $driver = $driver ?? $_ENV['Driver'];
        $dsn = $driver.':' . http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    
    public function query($query, $params = [])
    {
        $this->statement = $this->connection->prepare($query);

        $this->statement->execute($params);

        return $this;
    }

    public function get()
    {
        return $this->statement->fetchAll();
    }

    public function find()
    {
        return $this->statement->fetch();
    }

    public function findOrFail()
    {
        $result = $this->find();

        if (!$result) {
            abort(); // Implement your abort logic here
        }

        return $result;
    }

    // Transaction management methods
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }


    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function rowCount()
{
    return $this->statement->rowCount(); 
}
public function lastInsertId()
{
    return $this->connection->lastInsertId();
}

public function getPDO(): PDO
{
    return $this->connection;
}
}