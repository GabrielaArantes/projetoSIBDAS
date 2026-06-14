<?php
require_once __DIR__ . '/../../private/includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

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
