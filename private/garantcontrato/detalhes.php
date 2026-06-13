<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <aside class="sidebar">

        <nav>
            <a href="../dashboard/dashboard.html"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="../equipamentos/listar.html"><i class="fa-solid fa-stethoscope"></i> Equipamentos</a>
            <a href="../fornecedores/listar.html"><i class="fa-solid fa-truck"></i> Fornecedores</a>
            <a href="../localizacao/listar.html"><i class="fa-solid fa-location-dot"></i> Localização</a>
            <a href="listar.html" class="active"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
            <a href="../documentacao/listar.html"><i class="fa-solid fa-folder-open"></i> Documentação</a>
            <a href="../gestaoconteudo/gestao.html"><i class="fa-solid fa-pen-to-square"></i> Gestão de Conteúdos Públicos</a>
        </nav>
    </aside>

    <header class="topbar">
        <div class="logo-topbar">
            <img src="../../assets/img/logHospital.png" alt="Logo MedStock">
            <h1>MedStock</h1>
        </div>

        <div class="user-button">
            <i class="fa-regular fa-user"></i>
            <span>Utilizador</span>
            <i class="fa-solid fa-chevron-down seta"></i>

            <ul class="user-dropdown">
                <li><a href="#">Mudar password</a></li>
                <li><a href="../public/login.html">Sair</a></li>
            </ul>
        </div>
    </header>

    <main class="conteudo">

        <h1 class="mb-4">Detalhes da Garantia e Contrato</h1>

        <div class="shadow p-4 rounded" style="max-width: 850px;">

            <div class="mb-3">
                <strong>ID:</strong>
            </div>

            <div class="mb-3">
                <strong>Tipo de contrato:</strong>
            </div>

            <div class="mb-3">
                <strong>Entidade responsável:</strong>
            </div>

            <div class="mb-3">
                <strong>Periodicidade:</strong>
            </div>

            <div class="mb-3">
                <strong>Data de início:</strong>
            </div>

            <div class="mb-3">
                <strong>Data de fim:</strong>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.html" class="btn btn-secondary">Voltar</a>
                <a href="editar.html" class="btn btn-warning">Editar</a>
            </div>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
