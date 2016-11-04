<?php
  require_once './db_connect.php';

  function prepareQuery($query) {
    global $conn;
    global $stmt;

    if ($stmt->prepare($query)) {
      $stmt->execute();
      return TRUE;
    }
    return NULL;
  }
?>
