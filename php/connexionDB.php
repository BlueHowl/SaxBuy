<?php
  class connexionDB {
    private $host    = '';   // nom de l'host
    private $name    = '';     // nom de la base de donnée
    private $user    = '';        // utilisateur
    private $pass    = '';        // mot de passe
    private $connexion;
                    
    function __construct($host = null, $name = null, $user = null, $pass = null){
      if($host != null){
        $this->host = $host;           
        $this->name = $name;           
        $this->user = $user;          
        $this->pass = $pass;
      }
      try{
        $this->connexion = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name,
          $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES UTF8', 
          PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
      }catch (PDOException $e){
        echo 'Error : impossible to connect to the DB !';
        die();
      }
    }

    public function query($sql, $data = array()){

      $req = $this->connexion->prepare($sql);

      foreach($data as $key => $value)
      {
        if(is_int($value))
          $param = PDO::PARAM_INT;
        elseif(is_bool($value))
          $param = PDO::PARAM_BOOL;
        elseif(is_null($value))
          $param = PDO::PARAM_NULL;
        elseif(is_string($value))
          $param = PDO::PARAM_STR;
        else
          $param = FALSE;
           
        if($param)
          $req->bindValue(":$key",$value,$param);         
      }

      $req->execute();
      return $req;
    }
    
    public function insert($sql, $data = array()){

      $req = $this->connexion->prepare($sql);

      foreach($data as $key => $value)
      {
        if(is_int($value))
          $param = PDO::PARAM_INT;
        elseif(is_bool($value))
          $param = PDO::PARAM_BOOL;
        elseif(is_null($value))
          $param = PDO::PARAM_NULL;
        elseif(is_string($value))
          $param = PDO::PARAM_STR;
        else
          $param = FALSE;
           
        if($param)
          $req->bindValue(":$key",$value,$param);         
      }

      $req->execute();
    }

  }
  
  $salt_prefix = "";
  $salt_suffix = "";
  
  $DB = new connexionDB();
?>