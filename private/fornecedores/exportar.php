<?php
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
        "SELECT nome, nif, telefone, email, morada, website,
                pessoa_contacto, telefone_contacto, tipo, observacoes
         FROM fornecedor"
    )->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;
} catch (PDOException $e) {
    die('Erro ao exportar dados.');
}

// ---- CSV ----
if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="fornecedores.csv"');

    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($out, [
        'Nome', 'NIF', 'Telefone', 'Email', 'Morada', 'Website',
        'Pessoa de Contacto', 'Telefone de Contacto', 'Tipo', 'Observações'
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
    header('Content-Disposition: attachment; filename="fornecedores.json"');
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
        <title>Fornecedores — MedStock</title>
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
            @media print { .btn-imprimir { display: none; } }
        </style>
    </head>
    <body>
        <button class="btn-imprimir" onclick="window.print()">
            Imprimir / Guardar como PDF
        </button>
        <h2>MedStock — Lista de Fornecedores</h2>
        <p class="info">
            Exportado em: <?= date('d/m/Y H:i') ?> &nbsp;|&nbsp;
            Total: <?= count($dados) ?> fornecedores
        </p>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>NIF</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Morada</th>
                    <th>Website</th>
                    <th>Pessoa de Contacto</th>
                    <th>Telefone Contacto</th>
                    <th>Tipo</th>
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