<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Task;

class TaskController
{
    public function index(Request $request, Response $response): Response
    {
        $user_id = $request->getAttribute('user_id');
        $tasks = Task::where('user_id', $user_id)->get();
        
        $response->getBody()->write(json_encode($tasks));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $user_id = $request->getAttribute('user_id');
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['title'])) {
            $response->getBody()->write(json_encode(['error' => 'Title is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $task = Task::create([
            'user_id' => $user_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'due_date' => $data['due_date'] ?? null
        ]);

        $response->getBody()->write(json_encode($task));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $user_id = $request->getAttribute('user_id');
        $task = Task::where('user_id', $user_id)->find($args['id']);
        
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Task not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $user_id = $request->getAttribute('user_id');
        $task = Task::where('user_id', $user_id)->find($args['id']);
        
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Task not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $data = $request->getParsedBody();
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'status' => $data['status'] ?? $task->status,
            'due_date' => $data['due_date'] ?? $task->due_date
        ]);

        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $user_id = $request->getAttribute('user_id');
        $task = Task::where('user_id', $user_id)->find($args['id']);
        
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Task not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $task->delete();
        $response->getBody()->write(json_encode(['message' => 'Task deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function markComplete(Request $request, Response $response, array $args): Response
    {
        $user_id = $request->getAttribute('user_id');
        $task = Task::where('user_id', $user_id)->find($args['id']);
        
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Task not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $task->update(['status' => 'completed']);
        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json');
    }
}