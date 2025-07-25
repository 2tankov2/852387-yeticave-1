<?php
declare(strict_types=1);

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Форматирует сумму и добавляет к ней знак рубля
 * @param int $price
 * @return string
 */
function price_format(int $price): string
{
    return number_format($price, 0, ',', ' ') . ' ₽';
}

/**
 * Получаем данные из БД в виде ассоциативного массива или завершаем код с ошибкой
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param mixed $data Данные для вставки на место плейсхолдеров
 *
 * @return ?array
**/

function get_items(mysqli $link, string $sql, ...$data): ?array
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!isset($result)) {
        die(mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получаем данные из БД в виде ассоциативного массива или завершаем код с ошибкой
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param mixed $data Данные для вставки на место плейсхолдеров
 *
 * @return ?array
 **/

function get_item(mysqli $link, string $sql, ...$data): ?array
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return null;
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Находит елемент(ассоциативный массив) с данными по максимальной ставе
 *
 **@var array $bets все ставки по лоту
 * @return array{customer_id: string, lot_id: string, date_add: string, cost: string}
 */

function find_max_bet(array $bets): array
{
    return array_reduce($bets, function ($acc, $bet) {
        return $acc['cost'] < $bet['cost'] ? $bet : $acc;
    }, $bets[0]);
}

/**
 * Создает новую ссылку с данными параметрами
 * @var string $path адрес данной страницы
 * @var array $data требуемые значения параметров, которые нужно заменить/добавить в $_GET
 * @return string новый адрес ссылки: адрес страницы + строка запроса
 **/

function create_new_url(string $path, array $data = []): string
{
    $params = $_GET;

    if (empty($data)) {
        return "/$path";
    }
    foreach ($data as $key => $value) {
        $params[$key] = $value;
    }
    $query = http_build_query($params);
    return "/{$path}?{$query}";
}

/**
 * Возвращает отфильтрованный массив значений заданных в случае успеха или false в случае неудачи
 * @param string $name данное имя поля
 * @return mixed
 *
**/

function get_post_value(string $name): mixed
{
    return filter_input(INPUT_POST, $name, FILTER_SANITIZE_SPECIAL_CHARS);
}

function get_data_pagination($cur_page, $items_count, $page_items): array
{
//считаем кол-во страниц и смещение
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
//заполняем массив номерами всех страниц
    $pages = range(1, $pages_count);
    return [$pages_count, $offset, $pages];
}