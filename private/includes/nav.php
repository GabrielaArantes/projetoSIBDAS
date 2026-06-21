<?php
require_once __DIR__ . '/funcoes.php';
redirect_if_not_logged();
start_session();
$nome = $_SESSION['utilizador'];
$perfil = $_SESSION['perfil'] ?? '';
?>

<aside class="sidebar">
        <nav>
            <a href="/projetoSIBDAS/private/dashboard/dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="/projetoSIBDAS/private/equipamentos/listar.php"><i class="fa-solid fa-stethoscope"></i> Equipamentos</a>

            <?php if ($perfil === 'Administrador' || $perfil === 'Técnico') : ?>
                <a href="/projetoSIBDAS/private/localizacao/listar.php"><i class="fa-solid fa-location-dot"></i> Localização</a>
                <a href="/projetoSIBDAS/private/fornecedores/listar.php"><i class="fa-solid fa-truck"></i> Fornecedores</a>
                <a href="/projetoSIBDAS/private/garantcontrato/listar.php"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
                <a href="/projetoSIBDAS/private/documentacao/listar.php"><i class="fa-solid fa-folder-open"></i> Documentação</a>
            <?php endif; ?>

            <?php if ($perfil === 'Administrador') : ?>
                <a href="/projetoSIBDAS/private/gestaoconteudo/gestao.php"><i class="fa-solid fa-pen-to-square"></i> Gestão de Conteúdos Públicos</a>
            <?php endif; ?>
        </nav>
    </aside>

    <header class="topbar">
        <div class="logo-topbar">
            <img src="/projetoSIBDAS/assets/img/logHospital.png" alt="Logo MedStock">
            <h1><?php echo APP_NAME; ?></h1>
        </div>

        <div class="user-button">
            <i class="fa-regular fa-user"></i>
            <span><?= htmlspecialchars($nome) ?></span>
            <span class="badge bg-light text-dark ms-2" title="Perfil"><?= htmlspecialchars($perfil) ?></span>
            <i class="fa-solid fa-chevron-down seta"></i>

            <ul class="user-dropdown">
                <li><a href="#">Mudar password</a></li>
                <li><a href="/projetoSIBDAS/public/logout.php">Sair</a></li>
            </ul>
        </div>
    </header>