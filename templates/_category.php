<?php
declare(strict_types=1);
/**
 * @var array<int,array{id: string, name: string, code: string} $categories список категорий лотов
 */
?>

<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
               <!--- <a href="/?page=<?=$category['id']; ?>"><?=htmlspecialchars($category['name']); ?></a> -->
                <a href="<?=create_new_url('', ['page' => $category['id']])?>"><?=htmlspecialchars($category['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>