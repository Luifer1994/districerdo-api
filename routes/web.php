<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $hostname = "76.74.150.106";
    $username = "root";
    $password = "R2$56ZjEFYBnbgkV";
    $dbname = "cursos_mys";

    // Crear conexión
    $conn = new mysqli($hostname, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener lista de todas las tablas
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    // Reemplazar 'localhost' con '76.74.150.106:8085' en todas las tablas
    foreach ($tables as $table) {
        $columnQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '$table' AND DATA_TYPE IN ('char', 'varchar', 'text')";
        $columns = $conn->query($columnQuery);

        while ($col = $columns->fetch_assoc()) {
            $updateQuery = "UPDATE $table SET " . $col['COLUMN_NAME'] . " = REPLACE(" . $col['COLUMN_NAME'] . ", 'localhost', '76.74.150.106:8085')";
            $conn->query($updateQuery);
        }
    }

    echo "Cadenas actualizadas.";


    $conn->close();
});
