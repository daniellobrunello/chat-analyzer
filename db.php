<?php

class encryptDB {

    private $db;
	private $user;
    private $pass;
    private $pdo;
    private $errorLog;
    
    function __construct() {
        require_once('config.php');
        
        $this->db = DB_NAME;
		$this->pass = DB_PASS;
        $this->user = DB_USER;
        $this->host = DB_HOST;
        $this->errorLog = 'logs/error.log';
		
		$this->initConnection();
    }
    
    public function initConnection () {
        $this->pdo = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        //enables errors
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }


    private function logError($msg) {
        error_log($msg);
    }


    private function updateLastTouch($guid) {
        $sql = "UPDATE chats SET touches = touches + 1 WHERE guid = :guid";
        $statement = $this->pdo->prepare($sql);

        try {
            $statement->execute(["guid" => $guid]);
        } catch(\Exception $e){
            $this->logError($e->getMessage());
            return FALSE;
        }
    }


    public function saveEncryptedChat($guid, $chat) {
        $sql = "INSERT INTO chats (guid, content) VALUES (:guid, :chat)";
        $statement = $this->pdo->prepare($sql);
        
        try {
            $statement->execute(["guid" => $guid, "chat" => $chat]);
        } catch(\Exception $e){
            $this->logError($e->getMessage());
            return FALSE;
        }
    }

    public function getEncryptedChat($guid) {
        $sql = "SELECT content, touches FROM chats WHERE guid = :guid";
        $statement = $this->pdo->prepare($sql);

        try {
            $statement->execute(['guid' => $guid]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch(\Exception $e){
            $this->logError($e->getMessage());
            return FALSE;
        }

        $this->updateLastTouch($guid);

        return $result;
    }

    public function saveEncryptionDetails($details) {        
        $sql = "INSERT INTO encrypt (guid, cipher, iv, tag) VALUES (:guid, :cipher, :iv, :tag)";
        $statement = $this->pdo->prepare($sql);
        
        try {
            $statement->execute($details);
        } catch(\Exception $e){
            $this->logError($e->getMessage());
            return FALSE;
        }
    }


    public function getEncryptionDetails($guid) {
        $sql = "SELECT cipher, iv, tag FROM encrypt WHERE guid = :guid";
        $statement = $this->pdo->prepare($sql);

        try {
            $statement->execute(['guid' => $guid]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch(\Exception $e){
            $this->logError($e->getMessage());
            return FALSE;
        }
    }

}