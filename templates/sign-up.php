<?php
declare(strict_types=1);
/**
 * @var string[] $categories список категорий лотов
 * @var string[] $form заполненные пользователем поля формы
 * @var string[] $errors все ошибки заполнения формы пользователем
 */
?>

<main>
    <?=include_template('_category.php', ['categories' => $categories]); ?>
    <?php $class_form = isset($errors) ? 'form--invalid' : ''; ?>
    <form class="form container <?=$class_form; ?>" action="" method="post" autocomplete="off"> <!-- form
    --invalid -->
        <h2>Регистрация нового аккаунта</h2>
        <div class="form__item <?=isset($errors['email']) ? 'form__item--invalid' : ''?>"> <!-- form__item--invalid -->
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$form['email'] ?? ''; ?>">
            <span class="form__error"><?=$errors['email'] ?? ''; ?></span>
        </div>
        <div class="form__item <?=isset($errors['password']) ? 'form__item--invalid' : ''?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=$form['password'] ?? ''; ?>">
            <span class="form__error"><?=$errors['password'] ?? ''; ?></span>
        </div>
        <div class="form__item <?=isset($errors['name']) ? 'form__item--invalid' : ''?>">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=$form['name'] ?? ''; ?>">
            <span class="form__error"><?=$errors['name'] ?? ''; ?></span>
        </div>
        <div class="form__item <?=isset($errors['message']) ? 'form__item--invalid' : ''?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?=$form['message'] ?? ''; ?></textarea>
            <span class="form__error"><?=$errors['message'] ?? ''; ?></span>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="<?=create_new_url('login.php'); ?>">Уже есть аккаунт</a>
    </form>
</main>

