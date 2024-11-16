<?php

// Get the action/status from the command-line argument
$operation = $argv[1] ?? '';

// Load tasks (assuming this function loads tasks from a JSON file)
$tasks = loadTasks();

// Filter tasks based on the action/status passed
$filteredTasks = [];

if ($operation === 'list-todo') {
    // Filter for tasks with 'todo' status
    $filteredTasks = array_filter($tasks, function($task) {
        return $task['status'] === 'todo';
    });
} elseif ($operation === 'list-in-progress') {
    // Filter for tasks with 'in-progress' status
    $filteredTasks = array_filter($tasks, function($task) {
        return $task['status'] === 'in-progress';
    });
} elseif ($operation === 'list-done') {
    // Filter for tasks with 'done' status
    $filteredTasks = array_filter($tasks, function($task) {
        return $task['status'] === 'done';
    });
} elseif ($operation === 'list') {
    // If the action is 'list', we can either show all tasks or apply default behavior
    $filteredTasks = $tasks; // Show all tasks
} else {
    echo "Unknown action: {$operation}\n";
    exit;
}

// Define column widths for consistency
$idWidth = 2;
$descriptionWidth = 15;
$statusWidth = 12;
$timestampWidth = 10;

// Draw the top border of the table
echo "+----+------------------+-------------+---------------------+---------------------+\n";


echo "| ID | Description      | Status      | Created At          | Updated At          |\n";


echo "+----+------------------+-------------+---------------------+---------------------+\n";

// If there are filtered tasks, display them
if (count($filteredTasks) > 0) {
    foreach ($filteredTasks as $task) {
        // Ensure that each column has the correct padding to align
        $id = str_pad($task['id'], $idWidth, " ", STR_PAD_LEFT);
        $description = str_pad($task['description'], $descriptionWidth, " ", STR_PAD_RIGHT);
        $status = str_pad($task['status'], $statusWidth, " ", STR_PAD_RIGHT);
        $createdAt = str_pad($task['createdAt'], $timestampWidth, " ", STR_PAD_RIGHT);
        $updatedAt = str_pad($task['updatedAt'], $timestampWidth, " ", STR_PAD_RIGHT);

        // Print each task's details in the table
        echo "| {$id} | {$description} | {$status} | {$createdAt} | {$updatedAt} |\n";
    }
} else {
    // Print a message when no tasks match the filter
    echo "| No tasks found for status: {$operation}                                               |\n";
}


echo "+----+------------------+-------------+---------------------+---------------------+\n";
