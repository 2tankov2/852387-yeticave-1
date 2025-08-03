<?php
declare(strict_types=1);

require_once('init.php');
require_once('models/lots.php');
require_once('models/bets.php');
require_once('models/users.php');
require_once('sent-email.php');

/**
 * @var false|mysqli $connect Ресурс соединения
 * @var ?array<int,array{lot_id: int, lot_name: string, user_win_id: int}> $lots лоты без победителей со сроком меньше 1 дня
 * @var int[] $lot_ids список IDs лотов
 * @var array<int,array{user_id: int, lot_id: int, date_add: string, cost: int} $last_bats массив последних ставок лотов
 * @var int[] $win_ids список IDs пользователей, выигрышной ставки в лотах
 * @var bool $is_update true/false получилось ли обновить запись в таблице лотов БД
 * @var array{user_name: string, email: string, contact: string} $user данные пользователя (имя, email, другие контакты)
 * @var array{id: int, date_end: string, lot_name: string, img_url: string, description: string,
 *     price_start: string, step_bet: int, cat_name: string} $lot данные лота
 **/

$lots = get_lots_without_win_and_finishing($connect);

if (!isset($lots)) {
    exit();
}

$lot_ids = array_map('intval', array_column($lots, 'lot_id'));
$last_bets = get_last_bets_by_lots($connect, $lot_ids);

if (!isset($last_bets)) {
    exit();
}

$win_ids = array_column($last_bets, 'user_id');
if (!empty($win_ids)) {
    foreach ($lot_ids as $key => $lot_id) {

        $is_update = update_lot($connect, [$win_ids[$key], $lot_id]);

        if (!$is_update) {
            exit();
        }

        $user = get_user_by_id($connect, (int)$win_ids[$key]);
        $lot = get_lot_by_id($connect, $lot_id);
        sent_mail($user['email'], $user['user_name'], $lot['id'], $lot['lot_name']);
    }
}