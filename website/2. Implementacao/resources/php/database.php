<?php
require("__config.php");

class DatabaseConnectionException extends Exception
{
    public function errorMessage() 
    {
        // Error message
        $errorMsg = 'Erro ao se conectar ao Banco de Dados: '.$this->getFile().': <b>'.$this->getMessage().'</b>';
        return $errorMsg;
    }
}

class DatabaseExecuteException extends Exception
{
    public function errorMessage() 
    {
        // Error message
        $errorMsg = 'Erro ao tentar processar o requerimento: '.$this->getFile().': <b>'.$this->getMessage().'</b>';
        return $errorMsg;
    }
}

class DatabaseQueryException extends Exception
{
    public function errorMessage() 
    {
        // Error message
        $errorMsg = 'Erro ao retornar a consulta: '.$this->getFile().': <b>'.$this->getMessage().'</b>';
        return $errorMsg;
    }
}

class Database
{
    // Connection
    public $conn;

    // Variables to connect to database
    private const USING_SSL = USING_SSL_CONNECTION;
    private const HOSTNAME = DB_SERVER;
    private const PORT = DB_PORT;
    private const USERNAME = DB_USERNAME;
    private const PASSWORD = DB_PASSWORD;
    private const DATABASE = DB_DATABASE;
    private $options;

    public function __construct()
    {
        $this->connect();
    }
  
    private function connect()
    {
        try
        {
            $url = "sqlsrv:Server=" . self::HOSTNAME . "," . self::PORT . ";Database=" . self::DATABASE;

            if(self::USING_SSL)
            {
                $this->options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::MYSQL_ATTR_SSL_CA => DB_SSL_FILEPATH,
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                );
            }

            $this->conn = new PDO($url, self::USERNAME, self::PASSWORD, $this->options);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            throw new DatabaseConnectionException($e->getMessage());
        }
    }

    public function executeCommand($command)
    {
        try
        {
            $count = $this->conn->exec($command);
        }
        catch(PDOException $e)
        {
            throw new DatabaseExecuteException("Comando invalidado.");
        }
    }

    public function getRowFromQuery($command)
    {
        try
        {
            $stmt = $this->conn->query($command); // Returns an object from class PDOStatement

            #if($this->validateQuery($stmt) === false)
            #{
                #throw new DatabaseQueryException("Credenciais incorretas.");
            #}
        }
        catch(PDOException $e)
        {
            throw new Exception("Ocorreu um erro inesperado: " . $e->getMessage());
        }

        return $this->retrieveNextRow($stmt);
    }

    public function getAllRowsFromQuery($command)
    {
        try
        {
            $stmt = $this->conn->query($command); // Returns an object from class PDOStatement
        }
        catch(PDOException $e)
        {
            throw new DatabaseQueryException($e->getMessage());
        }

        // Return query result
        return $this->retrieveAllRows($stmt);
    }

    public function getLastID()
    {
        return $this->conn->lastInsertId();
    }

    private function validateQuery($statement)
    {
        return $statement->rowCount() === 1;
    }

    private function retrieveNextRow($statement)
    {
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    private function retrieveAllRows($statement)
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Close the connection
    public function close()
    {
        $this->conn = null;
    }
}

class DBCommands
{
                                   
}
?>