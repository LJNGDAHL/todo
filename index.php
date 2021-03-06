<?php
  require_once "./lib/config.php";
  require_once "./lib/header.php";
  require_once "./lib/db_connect.php";
  require_once "./lib/functions.php";

  if ($db_error) {
    echo $db_error_message;
  }

  /* --------------------------------------------------------------------------
  START OF FEEDBACK MESSAGE THAT PRINTS INFO ABOUT THE DATABASE UPDATE
  -------------------------------------------------------------------------- */

  $feedbackMessage = "";

  if (isset($_POST["task-completed"])) {
    $taskCompleted = mysqli_real_escape_string($conn, $_POST["task-completed"]);
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
    $taskName = mysqli_real_escape_string($conn, $_POST["taskname"]);
    $priority = mysqli_real_escape_string($conn, $_POST["priority"]);

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

  $totalTasksUncompleted = 0;

  if (prepareQuery($query)) {
    $stmt->bind_result($id, $taskName, $completed, $priority);
  }

  $stmt->store_result();
  $numberOfRows = mysqli_stmt_num_rows($stmt);

  /* --------------------------------------------------------------------------
  START OF VARIABLES THAT ARE USED FOR CREATING THE OPTION LISTS
  -------------------------------------------------------------------------- */

  // These variables are used for creating the filter option list.
  $getFilter = "filter";
  $filterTypes = array(
    array("all", "View all"),
    array("completed", "Only show finished tasks"),
    array("unfinished", "Only show unfinished tasks"),
    array("high", "Only show high priority"),
    array("normal", "Only show normal priority"),
    array("low", "Only show low priority")
  );

  // These variables are used for creating the sort option list.
  $getSort = "sort";
  $sortTypes = array(
    array("name", "Sort by name"),
    array("asc", "Sort by ascending priority"),
    array("desc", "Sort by descending priority"),
    array("done", "Sort by completed"),
    array("original", "Do not sort")
  );
?>
<!-- HTML STARTS HERE --------------------------------------------------------->
  <a href="./index.php" title="Go back to start">
    <div class="circle">
      <h1>Todo</h1>
    </div>
  </a>
  <p class="slogan">Access your TODO's. Whenever. Whereever.</p>
<!-- THIS FORM CONTAINS THE TASK LIST ----------------------------------------->
<?php if($numberOfRows == 0): ?>
<p class="info-text">You don't have any tasks in this view.</p>
<?php else: ?>
<form method="POST" action="./index.php">
  <table>
    <tbody>
      <?php while (mysqli_stmt_fetch($stmt)):
        $class = "";
        if ($completed == 1) {
          $class = " class=\"done\"";
        } elseif ($priority == 3) {
          $class = " class=\"high-priority\"";
        } elseif ($priority == 1) {
          $class = " class=\"low-priority\"";
        }
      ?>
      <tr<?php echo $class; ?>>
        <td><?php echo $taskName; ?></td>
        <td><?php echo getPriorityByValue($priority); ?></td>
        <td class="center-text">
          <?php if ($completed != 1):
            $totalTasksUncompleted++; ?>
          <button type="submit" class="icon-button" name="task-completed" value="<?php echo $id; ?>">
            <svg class="icon">
              <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#checkbox"></use>
            </svg>
          </button>
          <?php else: ?>
          Done
          <?php endif; ?>
        </td>
        <td>
          <button type="submit" class="icon-button" name="task-deleted" value="<?php echo $id; ?>">
            <svg class="icon">
              <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#trashcan"></use>
            </svg>
          </button>
        </td>
      </tr>
      <?php endwhile; $stmt->close(); $conn->close(); ?>
    </tbody>
  </table>
</form>
<p class="info-text">Unfinished tasks in current view: <?php echo $totalTasksUncompleted; ?></p>
<?php endif; ?>
<h2>Sort and filter</h2>
<!-- THIS FORM IS USED FOR SORTING AND FILTERING THE TASK LIST ---------------->
<form method="GET" action="./index.php">
  <div class="flex">
      <label for="sort">Sort TODO's by:</label>
      <div class="select-arrows">
        <select class="small" name="sort" id="sort">
          <?php $filterQuery = isset($_GET["filter"]) ? "&filter=$filter" : "" ?>
          <?php
            $sortOptionList = createListOfOptions($sortTypes, $getSort);
            echo implode("\n", $sortOptionList);
          ?>
        </select>
        <svg class="icon select-arrows">
          <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
        </svg>
      </div>
    <label for="filter">Filter TODO's by:</label>
    <div class="select-arrows">
      <select class="small" name="filter" id="filter">
        <?php
          $filterOptionList = createListOfOptions($filterTypes, $getFilter);
          echo implode("\n", $filterOptionList);
        ?>
      </select>
      <svg class="icon select-arrows">
        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
      </svg>
    </div>
  </div>
  <button class="small button" type="submit">Go</button>
</form>
<h2>Add another task</h2>
<!-- THIS FORM IS USED FOR ADDING ANOTHER TASK -------------------------------->
<form method="POST" action="./index.php">
  <div class="input-wrapper">
    <label for="taskname">Task</label>
    <input type="text" name="taskname" id="taskname" required placeholder="Add another task">
  </div>
  <label for="priority">Priority</label>
  <div class="large">
    <select name="priority" id="priority">
      <option value="3">High priority</option>
      <option value="2" selected>Normal priority</option>
      <option value="1">Low priority</option>
    </select>
    <svg class="icon select-arrows">
      <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
    </svg>
  </div>
  <button class="button" type="submit" name="add-task">Add</button>
</form>
<?php if ($feedbackMessage) { echo "<p class=\"info-text\">$feedbackMessage</p>"; } ?>
<?php require_once "./lib/footer.php"; ?>
