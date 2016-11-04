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

  $filterTypes = array(
    array("all", "View all"),
    array("completed", "Show all completed"),
    array("unfinished", "Only show unfinished tasks"),
    array("high", "Only show high priority"),
    array("normal", "Only show normal priority"),
    array("low", "Only show low priority")
  );

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


?>
