<?php
require_once '../config/database.php';

if ($_SESSION['rol'] !== 'administrator') {
    header("Location: /gara/public/index.php");
    exit;
}

$trenuri = $pdo->query("SELECT * FROM trains")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <?php include '../templates/header.php'; ?>
    <title>Panou Admin</title>
</head>
<body>
    <div class="container">
        <h2>Gestionare Trenuri</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Număr Tren</th>
                    <th>Tip</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trenuri as $tren): ?>
                <tr>
                    <td><?= htmlspecialchars($tren['train_id']) ?></td>
                    <td><?= htmlspecialchars($tren['numar_tren']) ?></td>
                    <td><?= htmlspecialchars($tren['tip_tren']) ?></td>
                    <td>
                        <a href="edit-train.php?id=<?= $tren['train_id'] ?>">Editează</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>