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
            <a href="listar.html" class="active"><i class="fa-solid fa-location-dot"></i> Localização</a>
            <a href="../garantcontrato/listar.html"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
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

        <h1 class="mb-4">Inserir Localização</h1>

        <form class="shadow p-4 rounded" style="max-width: 850px;">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Edifício</label>
                    <input type="text" class="form-control" name="edificio" placeholder="Ex: Edifício A">
                </div>

                <div class="col">
                    <label class="form-label">Piso</label>
                    <input type="text" class="form-control" name="piso" placeholder="Ex: Piso 2">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço / Departamento</label>
                <input type="text" class="form-control" name="servico" placeholder="Ex: Cardiologia">
            </div>

            <div class="mb-3">
                <label class="form-label">Sala / Gabinete</label>
                <input type="text" class="form-control" name="sala" placeholder="Ex: Sala 203">
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="3" name="observacoes"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.html" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Guardar
                </button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>