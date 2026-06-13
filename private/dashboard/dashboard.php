<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <aside class="sidebar">
        <nav>
            <a href="../dashboard/dashboard.html" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="../equipamentos/listar.html"><i class="fa-solid fa-stethoscope"></i> Equipamentos</a>
            <a href="../fornecedores/listar.html"><i class="fa-solid fa-truck"></i> Fornecedores</a>
            <a href="../localizacao/listar.html"><i class="fa-solid fa-location-dot"></i> Localização</a>
            <a href="../garantcontrato/listar.html"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
            <a href="../documentacao/listar.html"><i class="fa-solid fa-folder-open"></i>
                Documentação</a>
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

    <main class="conteudo p-4">

        <h1 class="mb-4">Dashboard</h1>

        <div id="cardsDashboard"></div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Equipamentos por Estado</h5>
                        <div style="position: relative; height: 250px;">
                            <canvas id="graficoEstado"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Número de Equipamentos por Serviço</h5>
                        <div style="position: relative; height: 250px;">
                            <canvas id="graficoServico"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Distribuição por Categoria</h5>
                <div style="position: relative; height: 250px;">
                    <canvas id="graficoCategoria"></canvas>
                </div>
            </div>
        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
