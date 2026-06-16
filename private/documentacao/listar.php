<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query("SELECT d.*, e.nome AS nome_equipamento FROM documento d LEFT JOIN equipamento e ON d.id_equipamento = e.id")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = [];
}
$ligacao = null;
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Documentação</h1>
            <a href="inserir.php" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar documentação..." name="pesquisa">

            <div class="menu-wrapper">
                <button class="btn btn-outline-success">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <div class="menu-box">
                    <div>
                        <label>Tipo</label>
                        <input type="text" class="form-control" name="tipo">
                    </div>
                    <div>
                        <label>Equipamento</label>
                        <input type="text" class="form-control" name="equipamento">
                    </div>
                    <div>
                        <label>Data</label>
                        <input type="date" class="form-control" name="data">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2" style="grid-column: span 2;">
                        <button type="reset" class="btn btn-outline-secondary btn-sm">Limpar</button>
                        <button type="submit" class="btn btn-success btn-sm">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <p class="text-center text-danger"><?= $erro ?></p>
        <?php elseif (count($resultados) == 0) : ?>
            <p class="text-muted">Não existem documentos registados.</p>
        <?php else : ?>
            <table class="table table-striped table-bordered shadow-sm">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th>Equipamento</th>
                        <th>Data</th>
                        <th>Validade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaDocumentacao">
                    <?php foreach ($resultados as $doc) : ?>
                    <tr>
                        <td><?= $doc->id ?></td>
                        <td><?= $doc->tipo ?></td>
                        <td><?= $doc->nome ?></td>
                        <td><?= $doc->nome_equipamento ?></td>
                        <td><?= $doc->data_documento ?></td>
                        <td><?= $doc->data_validade ?></td>
                        <td>
                            <a href="detalhes.php" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="editar.php" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="col">
            <p class="mb-5">Total: <strong><?= count($resultados) ?></strong></p>
        </div>

        <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Eliminar Documento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja apagar este documento?</p>
                        <p class="text-muted">Esta ação é irreversível.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a id="btnConfirmarEliminar" href="#" class="btn btn-danger">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>