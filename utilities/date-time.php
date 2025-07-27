<?php
declare(strict_types=1);

const SECS_IN_HOUR = 3600;
const SECS_IN_MINUTE = 60;

const NOUN_PLURAL_FORM = [
    'hours' => ['час', 'часа', 'часов'],
    'minutes' => ['минута', 'минуты', 'минут'],
];

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && (!date_get_last_errors() || array_sum(date_get_last_errors()) === 0);
}

/**
 * Функция форматирует массив времени в строку
 * @param int[] $time массив [количество часов, количество минут]
 * @return string
 */
function time_format(array $time): string
{
    $hours_format = str_pad(strval($time['hours']), 2, "0", STR_PAD_LEFT);
    $minutes_format = str_pad(strval($time['minutes']), 2, "0", STR_PAD_LEFT);
    return "{$hours_format}:{$minutes_format}";
}

/**
 * Функция возвращает остаток времени до данной даты в виде массива часов и минут
 * @param string $date_end дата истечения лота
 * @return int[] [остаток часов до даты, остаток минут]
 */
function get_dt_range(string $date_end): array
{
    $ts = time();
    $end_ts = strtotime($date_end);
    $ts_diff = $end_ts - $ts;

    if ($ts_diff < 0) {
        return [
            'hours' => 0,
            'minutes' => 0
        ];
    }

    $hours_until_end = floor($ts_diff / SECS_IN_HOUR);
    $minutes_until_end = abs(floor($ts_diff % SECS_IN_HOUR / SECS_IN_MINUTE));

    return [
        'hours' => $hours_until_end,
        'minutes' => $minutes_until_end,
    ];
}

/**
 * Возвращает разницу в днях
 * @param string $date дата в формате «ГГГГ-ММ-ДД»
 * @return float
 *
**/
function diff_date(string $date) : float
{
    return floor((strtotime('now') - strtotime($date)) / SECS_IN_HOUR);
}


function history_time_format(string $datetime): string
{
    $dt_add = date_create($datetime);
    $dt_now = date_create('now');
    $dt_diff = date_diff($dt_add, $dt_now);
    if ($dt_diff->format("%a") < 1) {
        $hours_diff = (int)$dt_diff->format("%h");
        $minutes_diff = (int)$dt_diff->format("%i");
        if ($hours_diff > 0) {
            $noun_plural_form_hours = get_noun_plural_form($hours_diff, ...NOUN_PLURAL_FORM['hours']);
            return "{$hours_diff} {$noun_plural_form_hours} назад";
        }
        $noun_plural_form_minutes = get_noun_plural_form($minutes_diff, ...NOUN_PLURAL_FORM['minutes']);
        return "{$minutes_diff} {$noun_plural_form_minutes} назад";
    } else {
        return $dt_add->format("d.m.y в H:i");
    }
}

function is_expiration_date($timer)
{
    if ($timer['hours'] === 0 || $timer['minutes'] === 0) {
        return true;
    }
    return false;
}