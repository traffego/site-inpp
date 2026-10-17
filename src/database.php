<?php
// src/database.php

function db_connect(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
        $opcoes = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
        } catch (PDOException $e) {
            die('Erro de conexão com o banco de dados: ' . (APP_DEBUG ? $e->getMessage() : 'Verifique os logs do servidor.'));
        }
    }
    
    return $pdo;
}

function db_query(string $sql, array $params = []): PDOStatement {
    $pdo = db_connect();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch(string $sql, array $params = []): array|false {
    $stmt = db_query($sql, $params);
    return $stmt->fetch();
}

function db_fetch_all(string $sql, array $params = []): array {
    $stmt = db_query($sql, $params);
    return $stmt->fetchAll();
}

function db_insert(string $table, array $data): string|false {
    if (empty($data)) return false;
    
    $colunas = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $colunas, $placeholders);
    db_query($sql, array_values($data));
    
    return db_connect()->lastInsertId();
}

function db_update(string $table, array $data, string $where, array $where_params = []): int {
    if (empty($data)) return 0;
    
    $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
    $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, $set, $where);
    
    $params = array_merge(array_values($data), $where_params);
    $stmt = db_query($sql, $params);
    
    return $stmt->rowCount();
}

function db_delete(string $table, string $where, array $params = []): int {
    $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
    $stmt = db_query($sql, $params);
    
    return $stmt->rowCount();
}

function db_count(string $sql, array $params = []): int {
    $stmt = db_query($sql, $params);
    return (int) $stmt->fetchColumn();
}

function db_execute(string $sql, array $params = []): PDOStatement {
    return db_query($sql, $params);
}

