<?php
/**
 * @var array $items List of problems
 * @var int   $page Current page number
 * @var int   $totalPages Total number of pages
 */
$pageTitle = 'PlantUML Server';
$needsEditor = false;
$bodyClass = 'bg-light';
require __DIR__ . '/partials/header.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <i class="bi bi-diagram-2" style="font-size: 3rem; color: #0d6efd;"></i>
        <h1 class="mt-2">PlantUML Server</h1>
        <p class="text-muted">Create UML diagrams from your browser</p>
    </div>

    <table class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th class="w-50">Title</th>
            <th>Theme</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr onclick="window.location.href = '/problems?id=<?= urlencode($item->getID()) ?>'">
                <td><?= htmlspecialchars($item->getID(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($item->getTitle(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($item->getTheme(), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <!-- Page numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <!-- Next button -->
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>