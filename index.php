<?php
  include_once "./header.php";
  include_once "./db_connect.php";
  include_once "./functions.php";

  if ($db_error) {
    echo $db_error_message;
  }

  /* --------------------------------------------------------------------------
  START OF FEEDBACK MESSAGE THAT PRINTS INFO ABOUT THE DATABASE UPDATE
  -------------------------------------------------------------------------- */
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

  /* --------------------------------------------------------------------------
  START OF STATEMENT AND QUERIES THAT ARE USED FOR SHOWING TASKS IN TODO LIST
  -------------------------------------------------------------------------- */

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

  $totalCompleted = 0;

  if (prepareQuery($query)) {
    $stmt->bind_result($id, $taskName, $completed, $priority);
  }
  /* --------------------------------------------------------------------------
  START OF VARIABLES THAT ARE USED FOR CREATING THE OPTION LISTS
  -------------------------------------------------------------------------- */
  // These variables are used for creating the filter option list.
  $getTypeFilter = "filter";
  $filterTypes = array(
    array("all", "View all"),
    array("completed", "Show all completed"),
    array("unfinished", "Only show unfinished tasks"),
    array("high", "Only show high priority"),
    array("normal", "Only show normal priority"),
    array("low", "Only show low priority")
  );

  // These variables are used for creating the sort option list.
  $getTypeSort = "sort";
  $sortTypes = array(
    array("name", "Name"),
    array("asc", "Ascending priority"),
    array("desc", "Descending priority"),
    array("done", "Completed"),
    array("original", "Unsorted")
  );
?>
<!-- HTML STARTS HERE --------------------------------------------------------->
<div class="circle">
  <h1>Todo</h1>
</div>
<p class="slogan">Access your TODO's whenever, whereever.</p>
<form method="POST" action="./index.php">
  <table>
    <thead>
      <td>Task</td>
      <td>Priority</td>
      <td>Completed</td>
      <td>Delete</td>
    </thead>
    <?php while (mysqli_stmt_fetch($stmt)):
      $class = "";
      if ($completed == 1) {
        $class = "done";
      }
    ?>
    <tr class="<?php echo $class; ?>">
      <td><?php echo $taskName; ?></td>
      <td><?php echo changePriorityNumberToString($priority); ?></td>
      <td>
        <?php if ($completed != 1): ?>
        <button type="submit" name="task-completed" value="<?php echo $id; ?>">
          <img src="./img/checkbox.svg" class="icon" alt="checkbox">
        </button>
        <?php else:
          $totalCompleted++;
          ?>
          Marked as done
        <?php endif; ?>
      </td>
      <td>
        <button type="submit icon" name="task-deleted" value="<?php echo $id; ?>">
          <img src="./img/delete.svg" class="icon" alt="trashcan">
        </button>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</form>
<p class="info-text">Total number of completed tasks: <?php echo $totalCompleted; ?></p>
<!-- THIS FORM IS USED FOR SORTING THE TASK LIST ------------------------------>
<form class="flex" method="GET" action="./index.php">
  <label class="small" for="sort">Sort TODO's by:</label>
  <select class="small" name="sort" placeholder="Sort by">
    <!-- TODO: Make sure your sort list works as your filter list. -->
    <?php $filterQuery = isset($_GET["filter"]) ? "&filter=$filter" : "" ?>
    <?php $sortOptionList = createListOfOptions ($sortTypes, $getTypeSort); ?>
  </select>
  <button class="button small" type="submit">Sort</button>
</form>
<!-- THIS FORM IS USED FOR FILTERING THE TASK LIST ---------------------------->
<form class="flex" method="GET" action="./index.php">
  <?php if (isset($_GET["sort"])): ?>
    <input type="hidden" name="sort" value="<?php echo $sort; ?>">
  <?php endif; ?>
  <label class="small" for="filter">Filter TODO's by:</label>
  <select class="small" name="filter" placeholder="Filter by">
    <?php $filterOptionList = createListOfOptions ($filterTypes, $getTypeFilter); ?>
  </select>
  <button class="button small" type="submit">Filter</button>
</form>
<h2>Add another task to the list</h2>
<form method="POST" action="./index.php">
  <div class="input-wrapper">
    <label for="taskname">Task</label>
    <input type="text" name="taskname" id="taskname" required placeholder="Add another task">
  </div>
  <label for="priority">Priority</label>
    <select name="priority">
      <option value="1">Low priority</option>
      <option value="2">Normal priority</option>
      <option value="3">High priority</option>
    </select>
  <button class="button" type="submit" name="add-task">Add</button>
</form>
<?php if ($feedbackMessage) { echo $feedbackMessage; } ?>
<?php include_once "./footer.php" ?>
