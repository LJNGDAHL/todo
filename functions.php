<?php
  require_once './db_connect.php';

  /**
   * This function prepares a query and executes the query is valid.
   * @param  [type] $query [description]
   * @return [type]        [description]
   */
  function prepareQuery($query) {
    global $conn;
    global $stmt;

    if ($stmt->prepare($query)) {
      $stmt->execute();
      return TRUE;
    }
    return NULL;
  }

/**
 * A function that creates a option elements from an array.
 * @param  array $array  A two dimensional array containing both short and long name.
 * @param  string $type  A string with information about which GET type to look for.
 */
function createListOfOptions ($array, $type) {

  for ($i = 0; $i < count($array); $i++) {
    $arrayShortName = $array[$i][0];
    $arrayRealName = $array[$i][1];

    if (isset($_GET["$type"]) && $arrayShortName == $_GET["$type"]) {
      echo "<option value=\"$arrayShortName\" selected>$arrayRealName</option>";
    } else {
    echo "<option value=\"$arrayShortName\">$arrayRealName</option>";
    }
  }
}

function changePriorityNumberToString($priority) {
  switch ($priority) {
    case "1":
      return "No stress";
      break;
    case "2":
      return "Do when possible";
      break;
    case "3":
      return "Urgent!";
      break;
    default:
      return "No priority";
  }
}
?>
