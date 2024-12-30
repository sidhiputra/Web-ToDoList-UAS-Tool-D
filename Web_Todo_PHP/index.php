<?php
include('config.php');

// Menambahkan tugas baru
if (isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    $sql = "INSERT INTO tasks (task_name) VALUES ('$task_name')";
    $conn->query($sql);
}

// Mengubah status tugas
if (isset($_GET['toggle_status'])) {
    $task_id = intval($_GET['toggle_status']); 
    $sql = "UPDATE tasks SET completed = NOT completed WHERE id = $task_id";
    $conn->query($sql);
    header("Location: index.php"); 
    exit();
}

// Menghapus tugas
if (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    $sql = "DELETE FROM tasks WHERE id = $task_id";
    $conn->query($sql);
}

// Mengedit tugas
if (isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $task_name = $_POST['task_name'];
    $sql = "UPDATE tasks SET task_name = '$task_name' WHERE id = $task_id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM tasks ORDER BY completed ASC";
$result = $conn->query($sql);

$total_tasks = $result->num_rows;
$completed_tasks = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['completed']) {
        $completed_tasks++;
    }
}

$progress_percentage = $total_tasks > 0 ? ($completed_tasks / $total_tasks) * 100 : 0;

$edit_task = null;
if (isset($_GET['edit_task'])) {
    $task_id = $_GET['edit_task'];
    $sql = "SELECT * FROM tasks WHERE id = $task_id";
    $edit_task_result = $conn->query($sql);
    $edit_task = $edit_task_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(119, 176, 170, 0.1), rgba(119, 176, 170, 0.4)), url('background4.jpg') no-repeat center center/cover;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .todo-container {
            background: #003C43;
            padding: 20px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 20px #002b30;
        }

        .todo-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .todo-header h1 {
            font-size: 32px;
            margin: 0;
        }

        .todo-header p {
            font-size: 14px;
            margin: 5px 0 0;
            color: #dfe6e9;
        }

        .progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        .progress-bar {
            height: 8px;
            width: 80%;
            background: #135D66;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar div {
            height: 100%;
            width: 0%;
            background: #4caf50;
        }

        .progress-circle {
            background: #135D66;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
        }

        .add-task {
            display: flex;
            margin-bottom: 20px;
            width: 100%;
        }

        .add-task form {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        .add-task input {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            outline: none;
            margin-right: 10px;
            background: #135D66;
            color: #ffffff;
        }

        .add-task button {
            padding: 12px;
            border: none;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
            background: #4caf50;
            width: 120px;
        }

        .add-task button:hover {
            background: #398439;
        }

        .todo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #135D66;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: transform 0.2s ease, background 0.3s ease;
        }

        .todo-item:hover {
            transform: scale(1.02);
            background: #0a6977;
        }

        .todo-item .checkbox {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #4caf50;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .todo-item .checkbox.completed {
            background: #4caf50;
        }

        .todo-item .task {
            flex: 1;
            font-size: 16px;
        }

        .todo-item .actions {
            display: flex;
            gap: 10px;
        }

        .todo-item .actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: #ffffff;
            font-size: 18px;
        }

        .todo-item .actions button i {
            transition: color 0.3s ease;
        }

        .todo-item .actions button:hover i {
            color: #4caf50;
        }

        #tasks-pending h3, #tasks-completed h3 {
            color: #77B0AA;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .divider {
            height: 2px;
            background: #77B0AA;
            margin: 10px 0;
        }

        .about-link {
            text-align: center;
            margin-top: 20px;
        }

        .about-link a {
            color: #4caf50;
            font-size: 18px;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .about-link a:hover {
            color: #77B0AA;
        }
    </style>
</head>
<body>
    <div class="todo-container">
        <div class="todo-header">
            <h1>MY TODO LIST</h1>
            <p>Your day, well planned!</p>
            <div class="progress">
                <div class="progress-bar">
                    <div style="width: <?php echo $progress_percentage; ?>%;"></div>
                </div>
                <div class="progress-circle"><?php echo $completed_tasks; ?>/<?php echo $total_tasks; ?></div>
            </div>
        </div>

        <div class="add-task">
            <form method="POST" action="">
                <input type="text" name="task_name" placeholder="Write your task here..." required value="<?php echo $edit_task ? $edit_task['task_name'] : ''; ?>">
                <?php if ($edit_task): ?>
                    <input type="hidden" name="task_id" value="<?php echo $edit_task['id']; ?>">
                    <button type="submit" name="edit_task">Update Task</button>
                <?php else: ?>
                    <button type="submit" name="add_task">Add Task</button>
                <?php endif; ?>
            </form>
        </div>

        <div id="tasks-container">
            <div id="tasks-pending">
                <h3>Tasks Pending</h3>
                <div class="divider"></div>
                <?php 
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()): 
                        if (!$row['completed']): ?>
                            <div class="todo-item">
                                <div class="checkbox <?php echo $row['completed'] ? 'completed' : ''; ?>" 
                                     onclick="window.location='?toggle_status=<?php echo $row['id']; ?>'"></div>
                                <div class="task"><?php echo $row['task_name']; ?></div>
                                <div class="actions">
                                    <button onclick="window.location='?edit_task=<?php echo $row['id']; ?>'"><i class="fas fa-edit"></i></button>
                                    <button onclick="window.location='?delete_task=<?php echo $row['id']; ?>'"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        <?php endif; 
                    endwhile;
                ?>
            </div>

            <div id="tasks-completed">
                <h3>Tasks Completed</h3>
                <div class="divider"></div>
                <?php 
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()): 
                        if ($row['completed']): ?>
                            <div class="todo-item">
                                <div class="checkbox completed" onclick="window.location='?toggle_status=<?php echo $row['id']; ?>'"></div>
                                <div class="task"><?php echo $row['task_name']; ?></div>
                                <div class="actions">
                                    <button onclick="window.location='?edit_task=<?php echo $row['id']; ?>'"><i class="fas fa-edit"></i></button>
                                    <button onclick="window.location='?delete_task=<?php echo $row['id']; ?>'"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        <?php endif; 
                    endwhile;
                ?>
            </div>
        </div>

        <div class="about-link">
            <a href="about.html">About</a>
        </div>
    </div>
</body>
</html>