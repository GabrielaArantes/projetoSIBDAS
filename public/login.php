<?php
require_once __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Iniciar Sessão</title>

    <link rel="stylesheet" href="../assets/css/1241094.css">
    <link rel="shortcut icon" href="../assets/img/logHospital.png" type="image/png">
    
</head>

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

</body>
</html>