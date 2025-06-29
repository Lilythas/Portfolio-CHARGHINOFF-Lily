<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'portfoliojed');
if ($conn->connect_error) {
    die('Erreur de connexion : ' . $conn->connect_error);
}
?>