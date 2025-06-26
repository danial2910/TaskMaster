<?php
namespace App\Controllers;

use App\db;

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
        $due_date = $data['due_date'] ?? null;

        if (!$title) {
            return ['error' => 'Task title is required'];
        }

        $stmt = $this->db->getPDO()->prepare(
            "INSERT INTO tasks (user_id, title, description, status, priority, due_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $stmt->execute([$userId, $title, $description, $status, $priority, $due_date]);

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
        if (isset($data['due_date'])) {
            $fields[] = 'due_date = ?';
            $params[] = $data['due_date'];
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
}