<?php

namespace Conn;

class DbConnection
{
    private const host = "localhost";
    private const username = "root";
    private const password = "";
    private const dbname = "scarpedb";

    private $connection;

    public function startDbConnection()
    {
        mysqli_report(MYSQLI_REPORT_ERROR);
        $this->connection = mysqli_connect(self::host, self::username, self::password, self::dbname);
        if (mysqli_connect_error()) {
            echo "Errore di connessione al database: " . mysqli_connect_error();
            return false;
        } else {
            return true;
        }
    }

    public function endDbConnection()
    {
        if ($this->connection != null) {
            mysqli_close($this->connection);
        }
    }

    public function getUserData($username)
    {
        $query = "SELECT username,email,pw,admin FROM utenti WHERE username='$username'";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" . mysqli_error($this->connection));

        if ($result) {
            $userData = $result->fetch_assoc();
            $result->free_result();
        } else {
            $userData = FALSE;
        }
        return $userData;
    }

    public function query($sql)
    {
        $result = mysqli_query($this->connection, $sql);
        if (!$result) {
            echo "Errore nell'esecuzione della query: " . mysqli_error($this->connection);
            return false;
        }
        return $result;
    }

    public function prepareAndExecute($sql, ...$params)
    {
    $stmt = $this->connection->prepare($sql);
    
    if (!$stmt) {
        die('Errore nella preparazione della query: ' . mysqli_error($this->connection));
    }

    if (!empty($params)) {
        $types = $params[0]; 
        $stmt->bind_param($types, ...array_slice($params, 1)); 
    }
    
    $stmt->execute();

    $result = $stmt->get_result();

    $result = $result->fetch_assoc();
    
    $stmt->close();

    return $result;  
}

}
?>