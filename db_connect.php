<?php
  include_once "./config.php";

  $conn = new mysqli($db_hostname, $db_user, $db_password, $db_name);
  $conn->set_charset("utf8");
  $db_error = $conn->connect_errno;
  $db_error_message = "Det går inte att ansluta till databasen just nu (felkod: $db_error).<br>Försök igen senare.";

  $stmt = $conn->stmt_init();

?>
