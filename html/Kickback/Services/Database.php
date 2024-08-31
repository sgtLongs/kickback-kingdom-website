<?php

namespace Kickback\Services;

use \mysqli;
use Kickback\Backend\Config\ServiceCredentials;

class Database {
    private static ?\mysqli $conn = null;

    public static function getConnection(): \mysqli {
        if (self::$conn === null) {
            // Fetching credentials
            $servername = ServiceCredentials::get("sql_server_host");
            $username = ServiceCredentials::get("sql_username");
            $password = ServiceCredentials::get("sql_password");
            $database = ServiceCredentials::get("sql_server_db_name");

            // This documents the types of the variables, and also makes PHPStan happy.
            assert(is_string($servername));
            assert(is_string($username));
            assert(is_string($password));
            assert(is_string($database));

            // Attempting to establish a database connection
            self::$conn = new \mysqli($servername, $username, $password, $database);

            // Error handling
            if (!is_null(self::$conn->connect_error)) {
                throw new \Exception("Connection failed: " . self::$conn->connect_error);
            }
        }

        return self::$conn;
    }

    public static function executeSqlQuery(string $stmt, array $params)
    {
        $connection = Database::getConnection();

        $result = mysqli_execute_query($connection, $stmt, $params);

        return $result;
    }

    public static function executeMultipleSqlQueries(array $stmtParamsPair)
    {
        $connection = Database::getConnection();

        // Begin transaction
        $connection->begin_transaction();

        $results = [];

        try {
            foreach($stmtParamsPair as $key => $value)
            {
                
            }

            // Commit transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback transaction
            $connection->rollback();
            throw $e;
        }

        return true;
    }
}
?>
