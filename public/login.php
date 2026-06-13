<?php
require_once __DIR__ . '/../config/config.php';
?>

<?php include __DIR__ . '/../private/includes/header.php'; ?>

<body class="login-page">

    <div class="login-card">

        <img src="../assets/img/logHospital.png">
        <h2><strong>MedStock</strong></h2>

        <form action="../private/equipamentos/listar.html" method="GET">

            <label for="email">Utilizador</label>
            <input type="email" id="email" required>
            <label for="password">Password</label>
            <input type="password" id="password" required>

            <button type="submit">
                Entrar <i class="fa-solid fa-right-to-bracket ms-2"></i>
            </button>

        </form>

    </div>

    <?php include __DIR__ . '/../private/includes/footer.php'; ?>