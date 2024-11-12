<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, genreization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];
$request = [];

if (isset($_SERVER['PATH_INFO'])) {
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
}

function getConnection() {
    $host = 'localhost';
    $db   = 'games';
    $user = 'root';
    $pass = ''; // Ganti dengan password MySQL Anda jika ada
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

function response($status, $data = NULL) {
    header("HTTP/1.1 " . $status);
    if ($data) {
        echo json_encode($data);
    }
    exit();
}

$db = getConnection();

switch ($method) {
    case 'GET':
        if (!empty($request) && isset($request[0])) {
            $id = $request[0];
            $stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
            $stmt->execute([$id]);
            $games = $stmt->fetch();
            if ($games) {
                response(200, $games);
            } else {
                response(404, ["message" => "gamess not found"]);
            }
        } else {
            $stmt = $db->query("SELECT * FROM games");
            $games = $stmt->fetchAll();
            response(200, $games);
        }
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->title) || !isset($data->genre) || !isset($data->price) || !isset($data->release_year)) {
            response(400, ["message" => "Missing required fields"]);
        }
        $sql = "INSERT INTO games (title, genre, price, release_year) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$data->title, $data->genre, $data->price, $data->release_year])) {
            response(201, ["message" => "Games created", "id" => $db->lastInsertId()]);
        } else {
            response(500, ["message" => "Failed to create games"]);
        }
        break;
    
    case 'PUT':
        if (empty($request) || !isset($request[0])) {
            response(400, ["message" => "games ID is required"]);
        }
        $id = $request[0];
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->title) || !isset($data->genre) || !isset($data->price) || !isset($data->release_year)) {
            response(400, ["message" => "Missing required fields"]);
        }
        $sql = "UPDATE games SET title = ?, genre = ?, price = ?, release_year = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$data->title, $data->genre, $data->price, $data->release_year, $id])) {
            response(200, ["message" => "Games updated"]);
        } else {
            response(500, ["message" => "Failed to update games"]);
        }
        break;
    
        case 'DELETE':
            if (empty($request) || !isset($request[0])) {
                response(400, ["message" => "gamess ID is required"]);
            }
            $id = $request[0];
            $sql = "DELETE FROM games WHERE id = ?";
            $stmt = $db->prepare($sql);
            if ($stmt->execute([$id])) {
                response(200, ["message" => "games deleted"]);
            } else {
                response(500, ["message" => "Failed to delete games"]);
            }
            break;
    
    default:
        response(405, ["message" => "Method not allowed"]);
        break;
}
?>