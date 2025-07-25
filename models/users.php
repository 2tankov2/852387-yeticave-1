<?php
declare(strict_types=1);

require_once ('utilities/helpers.php');

/**
 * Получает список пользователей или завершаем код с ошибкой
 * @param mysqli $connect Ресурс соединения
 * @return ?array<int,array{id: string, date_add: string, name: string, password: string, contact: string}
 **/
function get_users(mysqli $connect): ?array
{
    $sql = 'SELECT * FROM `users`';
    return get_items($connect, $sql);
}

/**
 * Формирует и выполняет SQL-запрос на добавление нового пользователя
 * @param mysqli $connect Ресурс соединения
 * @param string[] $data данные для добавления лота в БД
 * @return boolean
 **/

function set_user(mysqli $connect, array $data): bool
{
    $sql = 'INSERT INTO users(name, email, password, contact)
                VALUES (?, ?, ?, ?)';
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = db_get_prepare_stmt($connect, $sql, $data);
    return mysqli_stmt_execute($stmt);
}

/**
 * Получает данные пользователя по EMAIL из таблицы БД
 * @param mysqli $connect Ресурс соединения
 * @param string $email EMAIL лота
 * @return ?array{id: string, date_add: string, name: string, email: string, password: string, contact: string}
 **/
function get_user_by_email(mysqli $connect, string $email): ?array
{
    $sql = "SELECT * FROM users WHERE email = ?";
    return get_item($connect, $sql, $email);
}

//stora@internet.ru  пароль 111111