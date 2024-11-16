<?php


// Define the path to the JSON file where tasks will be saved
define('TASKS_FILE', __DIR__ . '/../../data/task.json');

// Valid task statuses only could be choosen between
$validStatuses = ['todo', 'in-progress', 'done'];

// Function to load tasks from the JSON file
function loadTasks() {
    if (file_exists(TASKS_FILE)) {
        $json = file_get_contents(TASKS_FILE);
        return json_decode($json, true);
    }
    return [];
}

// Function to save tasks to the JSON file
function saveTasks($tasks) {
    $json = json_encode($tasks, JSON_PRETTY_PRINT);
    file_put_contents(TASKS_FILE, $json);
}

// Get the operation type (add etc..)
$operation = $argv[1] ?? null;

// Ensure the operation is provided and valid
if (!$operation || !in_array($operation, [
    'add', 'update', 'mark-done', 'mark-in-progress', 
    'list-todo', 'list-in-progress', 'list-done', 
    'list', 'delete'
])) {
    echo "Error: Please specify a valid operation: 'add', 'update', 'list', 'mark-done', 'mark-in-progress', 'list-todo', 'list-in-progress', 'list-done',  'delete'.\n";
    exit(1);
}

switch ($operation) {
    case 'add':
        // Add a new task
        $description = readline("Enter description: ");

        if (!$description) {
            echo "Error: Please provide a task description.\n";
            exit(1);
        }

        $status = readline("Enter status (leave empty for 'todo'): ");
        if (empty($status)) {
            $status = 'todo'; // Default to 'todo' if not provided
        }

        if (!in_array($status, $validStatuses)) {
            echo "Error: Invalid status. Allowed statuses are: todo, in-progress, done.\n";
            exit(1);
        }

        // Load existing tasks
        $tasks = loadTasks();

        // Generate a new ID (auto-increment the highest current ID)
        $maxId = 0;
        foreach ($tasks as $task) {
            if (intval($task['id']) > $maxId) {
                $maxId = intval($task['id']);
            }
        }
        $newId = $maxId + 1; // Increment the highest ID

        // Define the current timestamp for createdAt and updatedAt
        $timestamp = date('Y-m-d H:i:s');

        // Create a new task
        $newTask = [
            'id' => (string)$newId,
            'description' => $description,
            'status' => $status,  // Set the user-defined status
            'createdAt' => $timestamp,
            'updatedAt' => $timestamp
        ];

        // Add the new task to the task list
        $tasks[] = $newTask;

        // Save the updated task list back to the JSON file
        saveTasks($tasks);

        // Output the success message
        echo "Task added successfully (ID: {$newId})\n";
        break;

    case 'update':
        // Update an existing task
        $taskId = readline("Enter the task ID you want to update: ");

        // Load existing tasks
        $tasks = loadTasks();

        // Find the task by ID
        $taskIndex = -1;
        foreach ($tasks as $index => $task) {
            if ($task['id'] === $taskId) {
                $taskIndex = $index;
                break;
            }
        }

        // If task is not found
        if ($taskIndex === -1) {
            echo "Error: Task with ID {$taskId} not found.\n";
            exit(1);
        }

        // Task found, now prompt user for updates
        $task = $tasks[$taskIndex];

        // Prompt for new description
        $newDescription = readline("Enter new description (leave empty to keep current): ");
        if ($newDescription) {
            $task['description'] = $newDescription; // Update description if provided
        }

        // Prompt for new status
        $newStatus = readline("Enter new status (leave empty to keep current, default is 'todo'): ");
        if (empty($newStatus)) {
            $newStatus = 'todo'; // Default to 'todo' if not provided
        }
        if (!in_array($newStatus, $validStatuses)) {
            echo "Error: Invalid status. Allowed statuses are: todo, in-progress, done.\n";
            exit(1);
        }
        $task['status'] = $newStatus; // Update status if provided

        // Update the task in the tasks array
        $tasks[$taskIndex] = $task;

        // Save the updated tasks list back to the JSON file
        saveTasks($tasks);

        // Output the success message
        echo "Task updated successfully (ID: {$taskId})\n";
        break;

    case 'mark-done':
        // Mark tasks as "done"
        $taskId = readline("Enter the task ID you want to mark as 'done': ");

        // Load existing tasks
        $tasks = loadTasks();

        // Find the task by ID
        $taskIndex = -1;
        foreach ($tasks as $index => $task) {
            if ($task['id'] === $taskId) {
                $taskIndex = $index;
                break;
            }
        }

        // If task is not found
        if ($taskIndex === -1) {
            echo "Error: Task with ID {$taskId} not found.\n";
            exit(1);
        }

        // Mark the task as 'done'
        $tasks[$taskIndex]['status'] = 'done';
        $tasks[$taskIndex]['updatedAt'] = date('Y-m-d H:i:s'); // Update the timestamp

        // Save the updated tasks list back to the JSON file
        saveTasks($tasks);

        echo "Task marked as 'done' (ID: {$taskId})\n";
        break;

    case 'mark-in-progress':
        // Mark tasks as "in-progress"
        $taskId = readline("Enter the task ID you want to mark as 'in-progress': ");

        // Load existing tasks
        $tasks = loadTasks();

        // Find the task by ID
        $taskIndex = -1;
        foreach ($tasks as $index => $task) {
            if ($task['id'] === $taskId) {
                $taskIndex = $index;
                break;
            }
        }

        // If task is not found
        if ($taskIndex === -1) {
            echo "Error: Task with ID {$taskId} not found.\n";
            exit(1);
        }

        // Mark the task as 'in-progress'
        $tasks[$taskIndex]['status'] = 'in-progress';
        $tasks[$taskIndex]['updatedAt'] = date('Y-m-d H:i:s'); // Update the timestamp

        // Save the updated tasks list back to the JSON file
        saveTasks($tasks);

        echo "Task marked as 'in-progress' (ID: {$taskId})\n";
        break;

    case 'list-todo':
    case 'list-in-progress':
    case 'list-done':
    case 'list':
        // Include the listTasks.php file here to display tasks in the desired format
        include 'listTasks.php';
        break;

    case 'delete':
        // Delete a task by ID
        $taskId = readline("Enter the task ID you want to delete: ");

        // Load existing tasks
        $tasks = loadTasks();

        // Find the task by ID and remove it
        $taskIndex = -1;
        foreach ($tasks as $index => $task) {
            if ($task['id'] === $taskId) {
                $taskIndex = $index;
                break;
            }
        }

        // If task is not found
        if ($taskIndex === -1) {
            echo "Error: Task with ID {$taskId} not found.\n";
            exit(1);
        }

        // Remove the task from the list
        array_splice($tasks, $taskIndex, 1);

        // Save the updated tasks list back to the JSON file
        saveTasks($tasks);

        echo "Task with ID {$taskId} has been deleted.\n";
        break;

    default:
        echo "Invalid operation. Use 'add', 'update', 'list', 'mark-done', 'mark-in-progress', 'list-todo', 'list-in-progress', 'list-done',  or 'delete'.\n";
        exit(1);
}
