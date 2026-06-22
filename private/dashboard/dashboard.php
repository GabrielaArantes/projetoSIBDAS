<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$total = 0;
$ativos = 0;
$em_manutencao = 0;
$inativos = 0;
$sem_documentacao = 0;
$garantias_expiradas = 0;
$por_estado = [];
$por_servico = [];
$por_categoria = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total de equipamentos
    $total = $ligacao->query("SELECT COUNT(*) FROM equipamento")->fetchColumn();

    // Ativos
    $ativos = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE estado = 'Ativo'")->fetchColumn();

    // Em manutenção
    $em_manutencao = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE estado = 'Em manutenção'")->fetchColumn();

    // Inativos
    $inativos = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE estado = 'Inativo'")->fetchColumn();

    // Sem documentação
    $sem_documentacao = $ligacao->query("SELECT COUNT(*) FROM equipamento e WHERE NOT EXISTS (SELECT 1 FROM documento d WHERE d.id_equipamento = e.id)")->fetchColumn();

    // Garantias expiradas
    $garantias_expiradas = $ligacao->query("SELECT COUNT(*) FROM garantia_contrato WHERE data_fim < CURDATE()")->fetchColumn();

    // Por estado (para gráfico)
    $stmt = $ligacao->query("SELECT estado, COUNT(*) as total FROM equipamento GROUP BY estado");
    $por_estado = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Por serviço (para gráfico)
    $stmt = $ligacao->query("SELECT l.servico, COUNT(*) as total FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id GROUP BY l.servico");
    $por_servico = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Por categoria (para gráfico)
    $stmt = $ligacao->query("SELECT categoria, COUNT(*) as total FROM equipamento GROUP BY categoria");
    $por_categoria = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Dados completos para o 1241094.js (estado, serviço, categoria, criticidade, garantia, documentação)
    $stmt = $ligacao->query(
        "SELECT e.estado, l.servico, e.categoria, e.criticidade,
                gc.data_fim AS garantia_fim,
                (SELECT COUNT(*) FROM documento d WHERE d.id_equipamento = e.id) > 0 AS temDoc
         FROM equipamento e
         LEFT JOIN localizacao l ON e.id_localizacao = l.id
         LEFT JOIN garantia_contrato gc ON gc.id_equipamento = e.id"
    );
    $dados_js = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;
} catch (PDOException $err) {
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo p-4">

        <h1 class="mb-4">Dashboard</h1>

        <!-- Cards de indicadores -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Total de Equipamentos</h6>
                        <h2><?= $total ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Equipamentos Ativos</h6>
                        <h2><?= $ativos ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Em Manutenção</h6>
                        <h2><?= $em_manutencao ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 text-white bg-secondary">
                    <div class="card-body">
                        <h6 class="card-title">Inativos</h6>
                        <h2><?= $inativos ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 text-white bg-danger">
                    <div class="card-body">
                        <h6 class="card-title">Garantias Expiradas</h6>
                        <h2><?= $garantias_expiradas ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card shadow-sm border-0 text-white bg-dark">
                    <div class="card-body">
                        <h6 class="card-title">Sem Documentação</h6>
                        <h2><?= $sem_documentacao ?></h2>
                    </div>
                </div>
            </div>
        </div>

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

    <script>
        // Dados reais da BD passados ao 1241094.js (carregado no footer)
        var dadosEquipamentos = <?= json_encode(array_map(function($r) {
            return [
                'estado'       => $r['estado'] ?? '',
                'servico'      => $r['servico'] ?? 'Sem serviço',
                'categoria'    => $r['categoria'] ?? 'Sem categoria',
                'criticidade'  => $r['criticidade'] ?? '',
                'garantia_fim' => $r['garantia_fim'] ?? '',
                'temDoc'       => (bool)$r['temDoc']
            ];
        }, $dados_js)) ?>;
    </script>
    <script>
        const dadosEstado = <?= json_encode(array_map(fn($r) => ['estado' => $r->estado, 'total' => $r->total], $por_estado)) ?>;
        const dadosServico = <?= json_encode(array_map(fn($r) => ['servico' => $r->servico ?? 'Sem serviço', 'total' => $r->total], $por_servico)) ?>;
        const dadosCategoria = <?= json_encode(array_map(fn($r) => ['categoria' => $r->categoria ?? 'Sem categoria', 'total' => $r->total], $por_categoria)) ?>;

        const cores = ['#198754', '#0d6efd', '#ffc107', '#6c757d', '#dc3545', '#212529', '#0dcaf0', '#fd7e14'];

        new Chart(document.getElementById('graficoEstado'), {
            type: 'doughnut',
            data: {
                labels: dadosEstado.map(d => d.estado),
                datasets: [{
                    data: dadosEstado.map(d => d.total),
                    backgroundColor: cores
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('graficoServico'), {
            type: 'bar',
            data: {
                labels: dadosServico.map(d => d.servico),
                datasets: [{
                    label: 'Equipamentos',
                    data: dadosServico.map(d => d.total),
                    backgroundColor: '#198754'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('graficoCategoria'), {
            type: 'bar',
            data: {
                labels: dadosCategoria.map(d => d.categoria),
                datasets: [{
                    label: 'Equipamentos',
                    data: dadosCategoria.map(d => d.total),
                    backgroundColor: '#0d6efd'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>