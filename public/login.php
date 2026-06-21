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

            <!-- Botões de preenchimento automático (Fase de Testes) -->
            <div class="mt-3 text-center" style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                <button type="button" id="preencher_adm" class="btn btn-outline-success btn-sm btn-teste" style="width:auto; padding:6px 12px;">
                    Admin
                </button>
                <button type="button" id="preencher_tec" class="btn btn-outline-success btn-sm btn-teste" style="width:auto; padding:6px 12px;">
                    Técnico
                </button>
                <button type="button" id="preencher_saude" class="btn btn-outline-success btn-sm btn-teste" style="width:auto; padding:6px 12px;">
                    Prof. Saúde
                </button>
            </div>

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

    <style>
        .btn-teste-ativo,
        .btn-teste-ativo:hover {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #fff !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const formulario = document.forms['formulario'];
            const botoesTeste = document.querySelectorAll('.btn-teste');

            function marcarSelecionado(botaoClicado) {
                botoesTeste.forEach(btn => btn.classList.remove('btn-teste-ativo'));
                botaoClicado.classList.add('btn-teste-ativo');
            }

            function preencherEmail(email, botao) {
                formulario['text_username'].value = email;
                formulario['text_password'].value = '';
                formulario['text_password'].focus();
                marcarSelecionado(botao);
            }

            document.querySelector('#preencher_adm').addEventListener('click', function () {
                preencherEmail('admin*@medstock.pt', this);
            });

            document.querySelector('#preencher_tec').addEventListener('click', function () {
                preencherEmail('tecnico*@medstock.pt', this);
            });

            document.querySelector('#preencher_saude').addEventListener('click', function () {
                preencherEmail('saude*@medstock.pt', this);
            });

        });
    </script>

    <?php include __DIR__ . '/../private/includes/footer.php'; ?>