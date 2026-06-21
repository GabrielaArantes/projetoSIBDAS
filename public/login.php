<?php
require_once __DIR__ . '/../config/config.php';
?>

<?php
session_start();

$validation_errors = [];
if (!empty($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']);
}

$server_error = [];
if (!empty($_SESSION['server_error'])) {
    $server_error = $_SESSION['server_error'];
    unset($_SESSION['server_error']);
}
?>

<?php include __DIR__ . '/../private/includes/header.php'; ?>

<body class="login-page">

    <div class="login-card">

        <img src="../assets/img/logHospital.png">
        <h2><strong>MedStock</strong></h2>

        <form name="formulario" action="../private/processa_login.php" method="post">

            <label for="email">Utilizador</label>
            <input type="email" name="text_username" id="email" required>
            <label for="password">Password</label>
            <input type="password" name="text_password" id="password" required>

            <button type="submit">
                Entrar <i class="fa-solid fa-right-to-bracket ms-2"></i>
            </button>

            <?php if (!empty($validation_errors)) : ?>
                <div class="alert alert-danger p-2 text-center">
                    <?php foreach ($validation_errors as $error) : ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($server_error)) : ?>
                <div class="alert alert-danger p-2 text-center">
                    <div><?= htmlspecialchars($server_error) ?></div>
                </div>
            <?php endif; ?>

        </form>

    </div>

    <?php include __DIR__ . '/../private/includes/footer.php'; ?>