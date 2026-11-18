<?php
namespace App\Controllers;

use App\db;
use DateTime;

class TaskController
{
    private $db;

    public function __construct(db $db)
    {
        $this->db = $db;
    }

    public function index($userId)
    {
        $stmt = $this->db->getPDO()->prepare("SELECT * FROM tasks WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function store($userId, $data)
    {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $status = $data['status'] ?? 'pending';
        $priority = $data['priority'] ?? 'medium';
        $due_date = $this->normalizeDueDate($data['due_date'] ?? null);
        $due_time = $this->normalizeDueTime($data['due_time'] ?? null);

        if (!$title) {
            return ['error' => 'Task title is required'];
        }
        if ($due_date === false) {
            return ['error' => 'Due date is invalid'];
        }
        if ($due_time === false) {
            return ['error' => 'Due time is invalid'];
        }

        $stmt = $this->db->getPDO()->prepare(
            "INSERT INTO tasks (user_id, title, description, status, priority, due_date, due_time, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $stmt->execute([$userId, $title, $description, $status, $priority, $due_date, $due_time]);

        return ['message' => 'Task added successfully', 'id' => $this->db->getPDO()->lastInsertId()];
    }

    public function update($userId, $id, $data)
    {
        $fields = [];
        $params = [];

        if (isset($data['title'])) {
            $fields[] = 'title = ?';
            $params[] = $data['title'];
        }
        if (isset($data['description'])) {
            $fields[] = 'description = ?';
            $params[] = $data['description'];
        }
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $params[] = $data['status'];
        }
        if (isset($data['priority'])) {
            $fields[] = 'priority = ?';
            $params[] = $data['priority'];
        }
        if (array_key_exists('due_date', $data)) {
            $dueDate = $this->normalizeDueDate($data['due_date']);
            if ($dueDate === false) {
                return ['error' => 'Due date is invalid'];
            }
            $fields[] = 'due_date = ?';
            $params[] = $dueDate;
        }
        if (array_key_exists('due_time', $data) || (array_key_exists('due_date', $data) && !$data['due_date'])) {
            $dueTime = $this->normalizeDueTime($data['due_time'] ?? null);
            if ($dueTime === false) {
                return ['error' => 'Due time is invalid'];
            }
            $fields[] = 'due_time = ?';
            $params[] = $dueTime;
        }

        if (empty($fields)) {
            return ['error' => 'No fields to update'];
        }

        $params[] = $userId;
        $params[] = $id;
        $sql = "UPDATE tasks SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE user_id = ? AND id = ?";
        $stmt = $this->db->getPDO()->prepare($sql);
        $stmt->execute($params);

        return ['message' => 'Task updated successfully'];
    }

    public function destroy($userId, $id)
    {
        $stmt = $this->db->getPDO()->prepare("DELETE FROM tasks WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $id]);
        return ['message' => 'Task deleted successfully'];
    }

    public function getTaskById($userId, $id)
{
    $stmt = $this->db->getPDO()->prepare("SELECT * FROM tasks WHERE user_id = ? AND id = ?");
    $stmt->execute([$userId, $id]);
    return $stmt->fetch();
}

    public function getTasksByStatus($userId, $status)
    {
        $stmt = $this->db->getPDO()->prepare("SELECT * FROM tasks WHERE user_id = ? AND status = ?");
        $stmt->execute([$userId, $status]);
        return $stmt->fetchAll();
    }

    private function normalizeDueDate($dueDate)
    {
        if ($dueDate === null || $dueDate === '') {
            return null;
        }
        if (!is_string($dueDate)) {
            return false;
        }

        $value = trim($dueDate);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return false;
        }

        $date = DateTime::createFromFormat('!Y-m-d', $value);
        $errors = DateTime::getLastErrors();
        if (
            $date === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $date->format('Y-m-d') !== $value
        ) {
            return false;
        }

        return $value;
    }

    private function normalizeDueTime($dueTime)
    {
        if ($dueTime === null || $dueTime === '') {
            return null;
        }
        if (!is_string($dueTime)) {
            return false;
        }

        $value = trim($dueTime);
        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            $value .= ':00';
        }
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return false;
        }

        $time = DateTime::createFromFormat('!H:i:s', $value);
        $errors = DateTime::getLastErrors();
        if (
            $time === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $time->format('H:i:s') !== $value
        ) {
            return false;
        }

        return $value;
    }
}