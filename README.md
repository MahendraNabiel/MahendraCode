Nama dan NIM
Nama : Nabiel Pramudya Mahendra 
Nim  : 22.01.55.6008
   - Deskripsi project
Proyek ini adalah API untuk mengelola database game menggunakan PHP dan MySQL. API ini mendukung operasi CRUD (Create, Read, Update, Delete) dan dapat diuji menggunakan Postman.

   - Query SQL pembuatan tabel
CREATE TABLE `games` (
  `id` int(10) NOT NULL,
  `title` text NOT NULL,
  `genre` text NOT NULL,
  `release_year` int(10) NOT NULL,
  `price` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   - Daftar endpoint API
Berikut adalah daftar endpoint API yang tersedia beserta deskripsi dan contoh penggunaan:
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

   - Cara instalasi dan penggunaan
Cara Instalasi
1. Persiapkan Lingkungan Pengembangan:
- Pastikan Anda memiliki server lokal seperti XAMPP atau WAMP yang telah terinstal di komputer Anda untuk menjalankan PHP dan MySQL.
2. Buat Database:
Buka phpMyAdmin dan buat database baru dengan nama games.
Jalankan perintah SQL di atas untuk membuat tabel games.
3. Simpan File PHP:
Buat folder baru di dalam direktori htdocsdan beri nama sesuai keinginan Anda.
Simpan kode PHP ke dalam file dengan nama index.phpdi dalam folder tersebut.
4. Edit Koneksi Database:
Jika Anda menggunakan password untuk MySQL, ganti nilai $passpada fungsi getConnection()dengan password Anda.
Cara Penggunaan
1. Menjalankan Server:
Jalankan XAMPP atau WAMP dan aktifkan Apache dan MySQL.
2. Akses API:
Gunakan URL berikut untuk mengakses API:
http://localhost/games/games.php
3. Operasi CRUD:
GET : Untuk mendapatkan semua data atau data berdasarkan ID.
POST : Untuk menambahkan data baru.
PUT : Untuk memperbarui data yang ada.
DELETE : Untuk menghapus data berdasarkan ID.

   - Screenshot hasil pengujian di Postman
Hasil Pengujian berada di Laporan Pengumpulan UTS
