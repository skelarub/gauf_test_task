<?php

  function getError($e) {
    http_response_code(500);
    die ($e->getMessage());
  }

  if($_POST){
    $driver = 'mysql';
    $host =  'localhost';
    $dbname = 'gauf.local';
    $user = 'root';
    $pwd = '';
    $opt = [
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
      $conn = new PDO($driver.':dbname='.$dbname.';host='.$host.';charset=utf8', $user, $pwd, $opt);
    } catch (PDOException $e) {
      getError($e);
    }

    $username = isset($_POST['username']) ? htmlentities($_POST['username'], ENT_QUOTES | ENT_DISALLOWED) : 'username';
    $telephone = isset($_POST['telephone']) ? htmlentities($_POST['telephone'], ENT_QUOTES | ENT_DISALLOWED) : 'telephone';
    $email = isset($_POST['email']) ? htmlentities($_POST['email'], ENT_QUOTES | ENT_DISALLOWED) : 'email';

    foreach ($_FILES["files"]["error"] as $key => $error) {
      if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["files"]["tmp_name"][$key];
        $namefile = pathinfo($_FILES["files"]["name"][$key])['filename'];
        $pathfile = "uploads/".pathinfo($_FILES["files"]["name"][$key])['basename'];

        try {
          if (!move_uploaded_file($tmp_name, $pathfile)) {
            throw new Exception('Невозможно переместить файл.');
          }
        } catch (Exception $e) {
          getError($e);
        }

        $sql = 'INSERT INTO test_task (`username`,`telephone`,`email`, `namefile`, `pathfile`)';
        $sql.= 'VALUE(?,?,?,?,?);';
        try {
          $stmt = $conn->prepare($sql);
          $stmt->execute([$username, $telephone, $email, $namefile, $pathfile]);
        } catch (PDOException $e) {
          getError($e);
        }


      }
    }

    $stmt = null;
    $conn = null;

  }
