-- добавляем в таблицу categories существующие категории объявлений (лотов)
INSERT categories(name, code) VALUE ('Доски и лыжи', 'boards'),
                              ('Крепления', 'attachment'),
                              ('Ботинки', 'boots'),
                              ('Одежда', 'clothing'),
                              ('Инструменты', 'tools'),
                              ('Разное', 'other');

-- добавляем в таблицу users данные нескольких выдуманных пользователей
INSERT INTO users(name, email, password, contact)
VALUES ('Елена', 'stor@internet.ru', 'asJHfd4UE3m', 'телефон 8450934324'),
       ("Егор", 'smir@yandex.ru', 'wHyt23bvyK', 'telegram @smita'),
       ('Макс', 'frik@mail.ru', 'dge7jMN4', 'telegram @frick');

-- добавляем в таблицу lots существующий список объявлений (лотов)
INSERT INTO lots(user_id, name, description, img_url, price, date_end, step_bet, cat_id)
VALUES (1,
        '2014Rossignol District Snowboard',
        '',
        '/img/lot-1.jpg',
        10999,
        '2025-06-04',
        100,
        1),
        (2,
        'DC Ply Mens 2016/2017 Snowboard',
        '',
        '/img/lot-2.jpg',
        159999,
        '2025-06-09',
        120,
        1),
        (2,
         'Крепления Union Contact Pro 2015 года размер L/XL',
         '',
         '/img/lot-3.jpg',
         8000,
         '2025-06-07',
         150,
         2),
        (2,
         'Ботинки для сноуборда DC Mutiny Charocal',
         'без дефектов',
         '/img/lot-4.jpg',
         10999,
         '2025-07-23',
         250,
         3),
        (1,
         'Куртка для сноуборда DC Mutiny Charocal',
         'немного б/у, без пятен, замки все работают',
         '/img/lot-5.jpg',
         7500,
         '2025-06-10',
         200,
         4),
        (1,
         'Маска Oakley Canopy',
         'новая',
         '/img/lot-6.jpg',
         5400,
         '2025-06-03',
         100,
         6);

-- добавляем пару ставок для любого объявления
INSERT INTO bets(user_id, lot_id, cost)
VALUES (2, 6, 5500),
       (3, 6, 5600),
       (2, 5, 7700),
       (3, 3, 8300),
       (1, 3, 8150);

-- получаем все категории из таблицы categories;
SELECT *  FROM categories;

-- получаем самые новые, открытые лоты. Каждый лот включает:
-- название, стартовую цену, ссылку на изображение, цену, название категории;

SELECT l.id,
       l.date_end,
       l.name 'lot_name',
       price 'price_start',
       img_url,
       MAX(b.cost) 'cost',
       c.name 'cat_name'
FROM lots l
    LEFT JOIN bets b ON b.lot_id = l.id
    INNER JOIN categories c ON l.cat_id = c.id
WHERE l.date_end > DATE(NOW())
group by l.id
ORDER BY l.date_add DESC LIMIT 10;

-- показываем лот по его ID (например id = 1). Получаем также название категории, к которой принадлежит лот;
SELECT l.*, c.name 'cat_name' FROM lots l
    INNER JOIN categories c ON l.cat_id = c.id
                              WHERE l.id = 1;

-- обновляем название лота по его идентификатору;
UPDATE lots SET name = 'Ботинки' WHERE id = 4;
-- проверяем изминения в таблице
SELECT *, c.name 'cat_name' FROM lots l
    INNER JOIN categories c ON l.cat_id = c.id
                            WHERE l.id = 4;


-- получаем список ставок для лота по его id (например id = 6)с сортировкой по дате.
SELECT * FROM bets
         WHERE lot_id = 6
         ORDER BY bets.date_add;
-- тоже получаем список ставок для лота по его id (например id = 3)
-- с сортировкой по дате с использованием JOIN.
SELECT b.* FROM bets b
    INNER JOIN lots l ON l.id = b.lot_id
           WHERE l.id = 6
           ORDER BY b.date_add;
