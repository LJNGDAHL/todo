<?php
  require_once "./db_connect.php";

  /**
   * Prepares a query and executes if valid.
   * @param  string $query A query to the database.
   * @return boolean
   */
  function prepareQuery($query) {
    global $stmt;

    if ($stmt->prepare($query)) {
      $stmt->execute();
      return true;
    }

    return false;
  }

  /**
   * Creates a list of options that is used for printing sort and filter options.
   * @param  array  $array          An array with options on how to sort and filter.
   * @param  string $type           Can be either sort or filter.
   * @return array  $listOfOptions
   */
  function createListOfOptions($array, $type) {

    $listOfOptions = array();

    for ($i = 0; $i < count($array); $i++) {
      $arrayShortName = $array[$i][0];
      $arrayRealName = $array[$i][1];
      $selectedAttribute = "";

      if (isset($_GET["$type"]) && $arrayShortName == $_GET["$type"]) {
        $selectedAttribute = "selected";
      }

      array_push($listOfOptions, "<option value=\"$arrayShortName\" $selectedAttribute>$arrayRealName</option>");
    }

    return $listOfOptions;
  }

  /**
   * Return information about the priority as a string.
   * @param  int     $priority  The priority level the task is marked with.
   * @return string             Used for printing out level of priority.
   */
  function getPriorityByValue($priority) {
    switch ($priority) {
      case "1":
        return "Low priority";
        break;
      case "2":
        return "Normal priority";
        break;
      case "3":
        return "High priority";
        break;
      default:
        return "No priority";
    }
  }

?>
