<!-- Section des Tâches Archivées (cachée par défaut) -->
<div id="archivedTasksSection" class="task-category" data-status="2">
    <h2 class="text-center my-4">Tâches Archivées</h2>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th style="width: 33.33%;">Tâches</th>
                <th class="text-center">Archivé</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($archivedTasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['tasks_name']) ?></td>
                    <td class="text-center"><span class="badge bg-warning">Inactif</span></td>
                    <td class="text-end">
                        <a href="index.php?action=changeStatus&id=<?= $task['id_tasks'] ?>&status=1" class="btn btn-success btn-sm">Réactiver</a>
                        <a href="index.php?action=changeStatus&id=<?= $task['id_tasks'] ?>&status=0" class="btn btn-danger btn-sm">Terminer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Section des Tâches Terminées (cachée par défaut) -->
<div id="completedTasksSection" class="task-category" data-status="0">
    <h2 class="text-center my-4">Tâches Terminées</h2>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th style="width: 33.33%;">Tâches</th>
                <th class="text-center">Terminé</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($completedTasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['tasks_name']) ?></td>
                    <td class="text-center"><span class="badge bg-danger">Terminée</span></td>
                    <td class="text-end">
                        <a href="index.php?action=changeStatus&id=<?= $task['id_tasks'] ?>&status=1" class="btn btn-success btn-sm">Recommencer</a>
                        <a href="index.php?action=changeStatus&id=<?= $task['id_tasks'] ?>&status=2" class="btn btn-warning btn-sm">Archiver</a>
                        <a href="index.php?action=deleteForever&id=<?= $task['id_tasks'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>