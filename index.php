<?php
  include_once "./header.php";
  include_once "./db_connect.php";
  include_once "./functions.php";

  if ($db_error) {
    echo $db_error_message;
  }

  $feedbackMessage = "";

  if (isset($_POST["task-completed"])) {
    $taskCompleted = $_POST["task-completed"];
    $query = "UPDATE tasks SET complete = 1 WHERE id ='{$taskCompleted}'";

    if (prepareQuery($query)) {
      $feedbackMessage = "Well done. Another task completed.";
    }
  }

  if (isset($_POST["task-deleted"])) {
    $taskToDelete = $_POST["task-deleted"];
    $query = "DELETE FROM tasks WHERE id ='{$taskToDelete}'";

    if (prepareQuery($query)) {
      $feedbackMessage = "The task is now deleted.";
    }
  }

  if (isset($_POST["add-task"])) {
    $taskName = $_POST["taskname"];
    $priority = $_POST["priority"];

    $query = "INSERT INTO tasks VALUES ('', '{$taskName}', 0, '{$priority}')";

    if (prepareQuery($query)) {
      $feedbackMessage = "The task was added to the list";
    }
  }

// var_dump($_POST);
// var_dump($_GET);

?>
<div class="circle">
  <h1>Todo</h1>
</div>
<?php
  global $stmt;
  $query = "SELECT * FROM tasks";

  $filter = "";
  if (isset($_GET["filter"])) { $filter = $_GET["filter"]; }

  if ($filter == "high") {
    $query .= " WHERE priority = 3";
  } elseif ($filter == "normal") {
    $query .= " WHERE priority = 2";
  } elseif ($filter == "low") {
    $query .= " WHERE priority = 1";
  } elseif ($filter == "completed") {
    $query .= " WHERE complete = 1";
  } elseif ($filter == "unfinished") {
    $query .= " WHERE complete = 0";
  }

  $sort = "";
  if (isset($_GET["sort"])) { $sort = $_GET["sort"]; }

  if ($sort == "name") {
    $query .= " ORDER BY taskName";
  } elseif ($sort == "asc") {
    $query .= " ORDER BY priority ASC";
  } elseif ($sort == "desc") {
    $query .= " ORDER BY priority DESC";
  } elseif ($sort == "done") {
    $query .= " ORDER BY complete DESC";
  }

  if (prepareQuery($query)) {
    $stmt->bind_result($id, $taskName, $completed, $priority);
  }
?>

<form class="flex" method="GET" action="./index.php">
  <label class="small" for="sort">Sort TODO's by:</label>
  <select class="small" name="sort" placeholder="Sort by">
    <?php $filterQuery = isset($_GET["filter"]) ? "&filter=$filter" : "" ?>
    <?php
    // TODO: Make sure that sort works the same way as filter.
      $sortTypes = array(
        array("name", "Name"),
        array("asc", "Ascending priority"),
        array("desc", "Descending priority"),
        array("done", "Completed"),
        array("original", "Unsorted")
      );
      for ($i = 0; $i < count($sortTypes); $i++) {
        $sortShortName = $sortTypes[$i][0];
        $sortRealName = $sortTypes[$i][1];
        if (isset($_GET["sort"]) && $sortShortName == $_GET["sort"]) {
          echo "<option value=\"$sortShortName\" selected>$sortRealName</option>";
        } else {
          echo "<option value=\"$sortShortName\">$sortRealName</option>";
        }
      }
    ?>
  </select>
  <button class="button small" type="submit">Sort</button>
</form>
<form class="flex" method="GET" action="./index.php">
    <?php if (isset($_GET["sort"])): ?>
      <input type="hidden" name="sort" value="<?php echo $sort; ?>">
    <?php endif; ?>
    <label class="small" for="filter">Filter TODO's by:</label>
    <select class="small" name="filter" placeholder="Filter by">
    <?php
      $filterTypes = array(
        array("all", "View all"),
        array("completed", "Show all completed"),
        array("unfinished", "Only show unfinished tasks"),
        array("high", "Only show high priority"),
        array("normal", "Only show normal priority"),
        array("low", "Only show low priority")
      );
      for ($i = 0; $i < count($filterTypes); $i++) {
        $filterShortName = $filterTypes[$i][0];
        $filterRealName = $filterTypes[$i][1];
        if (isset($_GET["filter"]) && $filterShortName == $_GET["filter"]) {
          echo "<option value=\"$filterShortName\" selected>$filterRealName</option>";
        } else {
          echo "<option value=\"$filterShortName\">$filterRealName</option>";
        }
      }
    ?>
    </select>
  <button class="button small" type="submit">Filter</button>
</form>
<form method="POST" action="./index.php">
  <table>
    <thead>
      <td>Task</td>
      <td>Priority</td>
      <td>Completed</td>
      <td>Delete</td>
    </thead>
    <?php while (mysqli_stmt_fetch($stmt)):
      // TODO: Fixa denna.
      $totalCompleted = count($completed);
      $class = "";
      if ($completed == 1) {
        $class = "done";
      } elseif ($priority == 3) {
        $class = "high-priority";
      } elseif ($priority == 2) {
        $class = "normal-priority";
      } elseif ($priority == 1) {
        $class = "low-priority";
      }
    ?>
    <tr class="<?php echo $class; ?>">
      <td><?php echo $taskName; ?></td>
      <td><?php echo $priority; ?></td>
      <td>
        <?php if ($completed != 1): ?>
        <button type="submit" name="task-completed" value="<?php echo $id; ?>">
          <img src="./img/checkbox.svg" class="icon" alt="checkbox">
        </button>
        <?php else: ?>
          Marked as done
        <?php endif; ?>
      </td>
      <td>
        <button type="submit icon" name="task-deleted" value="<?php echo $id; ?>">
          <img src="./img/delete.svg" class="icon" alt="trashcan">
        </button>
      </td>
    </tr>
    <!-- <tr> <?php echo $totalCompleted; ?></tr> -->
    <?php endwhile; ?>
  </table>
</form>
<?php // TODO: Fixa nedanstående så att antal räknas rätt. ?>
<!-- <p>Total number of unfinished tasks: <?php echo count($completed == 0); ?></p> -->
<h2>Add another task</h2>
<form method="POST" action="./index.php">
  <div class="input-wrapper">
    <label for="taskname">Task</label>
    <input type="text" name="taskname" id="taskname" required>
  </div>
  <label for="priority">Priority</label>
    <select name="priority">
      <option value="1">Low</option>
      <option value="2">Normal</option>
      <option value="3">High</option>
    </select>
  <button class="button" type="submit" name="add-task">Add</button>
</form>
<?php if ($feedbackMessage) { echo $feedbackMessage; } ?>
<?php include_once "./footer.php" ?>
