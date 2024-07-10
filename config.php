<?php

$host = "mysql:host=localhost; dbname=exercicio; charset=utf8mb4";
$user = "root";
$password = "root";
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'];

try {
  $pdo = new PDO($host, $user, $password, $options);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Erro ao conectar ao banco de dados: $e";
}