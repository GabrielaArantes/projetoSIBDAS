<?php
// Exporta a listagem de equipamentos em CSV, JSON ou PDF
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

$formato = $_GET['formato'] ?? '';

if (!in_array($formato, ['csv', 'json', 'pdf'])) {
    header('Location: listar.php');
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dados = $ligacao->query(
        "SELECT e.codigo_interno, e.nome, e.categoria, e.marca, e.modelo,
                e.num_serie, e.fabricante, e.data_aquisicao, e.ano_fabrico,
                e.custo, e.tipo_entrada, e.estado, e.criticidade,
                e.observacoes, l.servico AS localizacao
         FROM equipamento e
         LEFT JOIN localizacao l ON e.id_localizacao = l.id"
    )->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;
} catch (PDOException $e) {
    die('Erro ao exportar dados.');
}

// ---- CSV ----
if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="equipamentos.csv"');

    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8 para Excel

    fputcsv($out, [
        'Código Interno', 'Nome', 'Categoria', 'Marca', 'Modelo',
        'Nº Série', 'Fabricante', 'Data Aquisição', 'Ano Fabrico',
        'Custo (€)', 'Tipo Entrada', 'Estado', 'Criticidade',
        'Observações', 'Localização'
    ], ';');

    foreach ($dados as $row) {
        fputcsv($out, $row, ';');
    }

    fclose($out);
    exit;
}

// ---- JSON ----
if ($formato === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="equipamentos.json"');
    echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// ---- PDF ----
if ($formato === 'pdf') {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <title>Equipamentos — MedStock</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
            h2 { color: #2d6a4f; margin-bottom: 6px; }
            p.info { font-size: 9px; color: #555; margin-bottom: 15px; }
            table { width: 100%; border-collapse: collapse; }
            th { background-color: #2d6a4f; color: white; padding: 5px; text-align: left; font-size: 9px; }
            td { padding: 4px 5px; border-bottom: 1px solid #ddd; font-size: 9px; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .btn-imprimir {
                display: inline-block;
                margin-bottom: 15px;
                padding: 6px 14px;
                background-color: #2d6a4f;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13px;
            }
            @media print {
                .btn-imprimir { display: none; }
            }
        </style>
    </head>
    <body>
        <button class="btn-imprimir" onclick="window.print()">
            Imprimir / Guardar como PDF
        </button>
        <h2>MedStock — Lista de Equipamentos</h2>
        <p class="info">
            Exportado em: <?= date('d/m/Y H:i') ?> &nbsp;|&nbsp;
            Total: <?= count($dados) ?> equipamentos
        </p>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Nº Série</th>
                    <th>Fabricante</th>
                    <th>Data Aquisição</th>
                    <th>Ano Fabrico</th>
                    <th>Custo (€)</th>
                    <th>Tipo Entrada</th>
                    <th>Estado</th>
                    <th>Criticidade</th>
                    <th>Localização</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados as $row) : ?>
                    <tr>
                        <?php foreach ($row as $val) : ?>
                            <td><?= htmlspecialchars((string)$val) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}