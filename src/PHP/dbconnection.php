<?php
namespace Conn;

Class DbConnection{
    private const host = "localhost";
    private const username = "root";
    private const password = "";
    private const dbname = "scarpedb";

    private $connection;

    public function startDbConnection(){
        mysqli_report(MYSQLI_REPORT_ERROR);
        $this->connection = mysqli_connect(self::host, self::username, self::password, self::dbname);
        if(mysqli_connect_error()) {
            echo "Errore di connessione al database: " . mysqli_connect_error();
            return false;
        } else {
            return true;
        }
    }

    public function endDbConnection(){
        if($this->connection != null){
            mysqli_close($this->connection);
        }
    }

    public function getUserData($username){ //restituisce le info di un utente
        $query ="SELECT username,pw,admin FROM utente WHERE username='$username'";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));
    
        if ($result) {
            $userData = $result->fetch_assoc();
            $result->free_result();
        } else {
            $userData = FALSE;
        }
        return $userData;
    }

    public function insertNewUser($username, $password){
        $query ="SELECT username FROM utente WHERE username='$username'"; //controllo se lo username esiste già
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));
        if ($result->num_rows == 0) { //se non mi restituisce lo username cercato allora non esiste
            $result->free_result();
            $query ="INSERT INTO utente (username, pw, km, admin) VALUES('$username', '$password', 0, 0)";
            $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));
            $userCreated = TRUE;
        }else{ 
            $userCreated = FALSE;
            $result->free_result();
        }
       
        return $userCreated;
    }

    public function insertNewReview($username, $scarpa_id, $rating, $comment) {
        $query = "SELECT * FROM recensione WHERE username='$username' AND scarpa_id='$scarpa_id'";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" . mysqli_error($this->connection));
        
        if ($result->num_rows == 0) { // se non mi restituisce la recensione cercata allora non esiste
            $result->free_result();
            $query = "INSERT INTO recensione (username, scarpa_id, voto, commento, data_aggiunta)
                      VALUES('$username', '$scarpa_id', '$rating', '$comment')";
            $currentDate = '2025-01-01';
            $query = "INSERT INTO recensione (username, scarpa_id, voto, commento, data_aggiunta)
                      VALUES('$username', '$scarpa_id', '$rating', '$comment', '$currentDate')";
            $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" . mysqli_error($this->connection));
            $reviewCreated = TRUE;
        } else {
            $reviewCreated = FALSE;
            $result->free_result();
        }
        
        return $reviewCreated;
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

    public function prepareAndExecute($query, $types, ...$params) {
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            die("Errore nella preparazione della query: " . $this->connection->error);
        }
    
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        if (!$stmt->execute()) {
            die("Errore nell'esecuzione della query: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        if ($stmt->errno != '0') {
            die("Errore nell'ottenere il risultato: " . $stmt->error);
        }

        $rows = [];
        if($result){    
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        $stmt->close();
    
        return $rows;
    }

    public function isAdmin ($username){ //restituisce 1 se l'utente è admin, 0 altrimenti
        $query ="SELECT admin FROM utente WHERE username='$username'";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));
        $row = $result->fetch_assoc();
        $result->free_result();
        return $row["admin"];
    }
    
    public function getAllShoes(){ //restituisce le info di tutte le scarpe, dalle più recenti aggiunta alle più vecchie
        $query ="SELECT * FROM scarpa ORDER BY data_aggiunta DESC";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));

        $shoes = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $shoes[] = $row;
            }
            $result->free_result();
        } else {
            $shoes= array();
        }
        return $shoes;
    }

    public function getShoe($id){
        $query ="SELECT * FROM scarpa WHERE id='$id'";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));

        if ($result->num_rows > 0) {
            $shoe = $result->fetch_assoc();
            $result->free_result();
        } else {
            $shoe = FALSE;
        }
        return $shoe;
    }

    public function updateShoe($id, $nome, $marca, $descrizione, $tipo, $feedback, $voto_exp, $immagine){
        $created = false;
        $query = "UPDATE scarpa SET nome=?, marca=?, descrizione=?, tipo=?, feedback=?, votoexp=?, immagine=? WHERE id=?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Errore nella preparazione della query: " . $this->connection->error);
        }
        $stmt->bind_param("sssssisi", $nome, $marca, $descrizione, $tipo, $feedback, $voto_exp, $immagine, $id);
        if ($stmt->execute()) {
            $created = true;
        } else {
            die("Errore durante l'esecuzione della query: " . $stmt->error);
        }
        $stmt->close();
    
        return $created;
    }

    public function addNewShoe($nome, $marca, $descrizione, $tipo, $feedback, $voto_exp, $immagine){
        $created = false;
        $data_aggiunta = date("Y-m-d");

        $query = "INSERT INTO scarpa (nome, marca, descrizione, tipo, feedback, votoexp, immagine, data_aggiunta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Errore nella preparazione della query: " . $this->connection->error);
        }
        $stmt->bind_param("sssssiss", $nome, $marca, $descrizione, $tipo, $feedback, $voto_exp, $immagine, $data_aggiunta);
        if ($stmt->execute()) {
            $created = true;
        } else {
            die("Errore durante l'esecuzione della query: " . $stmt->error);
        }
        $stmt->close();
    
        return $created;
    }

    public function deleteShoe($id){
        $deleted = FALSE;
        $query ="DELETE FROM scarpa WHERE id='$id'";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
           $deleted=TRUE;
        }
        return $deleted;
    }


    public function getAllReviews(){ //restituisce le info di tutte le scarpe, dalle più recenti aggiunta alle più vecchie
        $query ="SELECT * FROM recensione, scarpa WHERE recensione.scarpa_id=scarpa.id ORDER BY recensione.data_aggiunta DESC";
        $result = mysqli_query($this->connection, $query) or die("Errore nell'accesso al database" .mysqli_error($this->connection));

        $reviews = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            $result->free_result();
        } else {
            $reviews= array();
        }
        return $reviews;
    }

    public function deleteReview($scarpa_id, $username){
        $deleted = FALSE;
        $query ="DELETE FROM recensione WHERE scarpa_id='$scarpa_id' AND username='$username'";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
           $deleted=TRUE;
        }
        return $deleted;
    }
}
?>