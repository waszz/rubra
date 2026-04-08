<?php
try {
    new PDO("mysql:host=127.0.0.1;dbname=recatista", "root", "curbelo");
    echo "Conexión exitosa.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}