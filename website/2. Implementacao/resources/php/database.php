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

    public function getLastIDFrom($table)
    {
        $result = $this->getAllRowsFromQuery("SELECT IDENT_CURRENT('$table') AS ID");
        return $result[0]['ID'];
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

class StoredProcedure extends Database
{
    public function store_pessoa_juridica($pj)
    {
        $endereco_pj = $pj->endereco;
        $estado = $endereco_pj->getEstado();
        $municipio = $endereco_pj->getMunicipio();
        $bairro = $endereco_pj->getBairro();
        $logradouro = $endereco_pj->getLogradouro();

        $sth = $this->conn->prepare("SET NOCOUNT ON; EXEC ins_pessoa_juridica ?, ?, ?, ?, ?, ?, ?;");
        $sth->bindParam(1, $pj->nome);
        $sth->bindParam(2, $pj->cnpj);
        $sth->bindParam(3, $pj->nomefantasia);
        $sth->bindParam(4, $estado);
        $sth->bindParam(5, $municipio);
        $sth->bindParam(6, $bairro);
        $sth->bindParam(7, $logradouro);
        $sth->execute();
        $sth->nextRowset();
    }

    public function store_pessoa_fisica($pf)
    {
        $endereco_pf = $pf->endereco;
        $estado = $endereco_pf->getEstado();
        $municipio = $endereco_pf->getMunicipio();
        $bairro = $endereco_pf->getBairro();
        $logradouro = $endereco_pf->getLogradouro();

        $sth = $this->conn->prepare("SET NOCOUNT ON; EXEC ins_pessoa_fisica ?, ?, ?, ?, ?, ?, ?, ?;");
        $sth->bindParam(1, $pf->nome);
        $sth->bindParam(2, $pf->cpf);
        $sth->bindParam(3, $pf->email);
        $sth->bindParam(4, $pf->telefone);
        $sth->bindParam(5, $estado);
        $sth->bindParam(6, $municipio);
        $sth->bindParam(7, $bairro);
        $sth->bindParam(8, $logradouro);
        $sth->execute();
        $sth->nextRowset();
    }

    public function store_nota_fiscal($nota_fiscal)
    {
        $sth = $this->conn->prepare("SET NOCOUNT ON; EXEC ins_notafiscal ?, ?, ?, ?, ?, ?, ?, ?;");
        $valortotal = 0;

        $date = new DateTime($nota_fiscal->data);
        $date = $date->format('Y-m-d H:i:s');

        $endereco_nota = $nota_fiscal->endereco;
        $estado = $endereco_nota->getEstado();
        $municipio = $endereco_nota->getMunicipio();
        $bairro = $endereco_nota->getBairro();
        $logradouro = $endereco_nota->getLogradouro();

        $sth->bindParam(1, $valortotal);
        $sth->bindParam(2, $date);
        $sth->bindParam(3, $nota_fiscal->desconto);
        $sth->bindParam(4, $nota_fiscal->codpessoa);
        $sth->bindParam(5, $estado);
        $sth->bindParam(6, $municipio);
        $sth->bindParam(7, $bairro);
        $sth->bindParam(8, $logradouro);
        $sth->execute();
        $sth->nextRowset();
    }

    public function store_fatura($fatura, $nota_id)
    {
        $sth = $this->conn->prepare("SET NOCOUNT ON; EXEC ins_fatura ?, ?, ?, ?, ?, ?;");

        $dtvencimento = new DateTime($fatura->ft_dtvencimento);
        $dtvencimento = $dtvencimento->format('Y-m-d H:i:s');

        $dtpagamento = new DateTime($fatura->ft_dtpagamento);
        $dtpagamento = $dtpagamento->format('Y-m-d H:i:s');

        $var_type;
        if($fatura->ft_cartao == '')
        {
            $var_type = PDO::PARAM_NULL;
        }
        else
        {
            $var_type = PDO::PARAM_INT;
        }

        $sth->bindParam(1, $dtvencimento);
        $sth->bindParam(2, $dtpagamento);
        $sth->bindParam(3, $fatura->ft_valor);
        $sth->bindParam(4, $nota_id);
        $sth->bindParam(5, $fatura->ft_pagamento);
        $sth->bindParam(6, $fatura->ft_cartao, $var_type);
        $sth->execute();
        $sth->nextRowset();
    }

    public function store_item_nota_fiscal($item, $numnota)
    {
        $sth = $this->conn->prepare("SET NOCOUNT ON; EXEC ins_itemnotafiscal ?, ?, ?, ?, ?, ?, ?, ?;");
        $valortotal = 0;
        $sth->bindParam(1, $numnota);
        $sth->bindParam(2, $valortotal);
        $sth->bindParam(3, $item->unidade);
        $sth->bindParam(4, $item->quantidade);
        $sth->bindParam(5, $item->desconto);
        $sth->bindParam(6, $item->cod);
        $sth->bindParam(7, $item->descricao);
        $sth->bindParam(8, $item->valorunitario);
        $sth->execute();
        $sth->nextRowset();
    }
}

class DBCommands
{
                                   
}
?>