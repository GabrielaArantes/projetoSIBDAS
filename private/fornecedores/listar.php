<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
if (isset($_GET['apagar'])) {
    $id_apagar = (int)$_GET['apagar'];
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $ligacao->prepare("DELETE FROM fornecedor WHERE id = ?");
        $stmt->execute([$id_apagar]);
        $ligacao = null;
    } catch (PDOException $err) {
    }
    header("Location: listar.php");
    exit;
}
?>

<?php
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query("SELECT * FROM fornecedor")->fetchAll(PDO::FETCH_OBJ);
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
            <h1>Fornecedores</h1>
            <a href="inserir.php" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar Fornecedor
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar fornecedor..." name="pesquisa">

            <div class="menu-wrapper">
                <button class="btn btn-outline-success">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <div class="menu-box">
                    <div>
                        <label>Nome da Empresa</label>
                        <input type="text" class="form-control" name="nome">
                    </div>
                    <div>
                        <label>NIF</label>
                        <input type="text" class="form-control" name="nif">
                    </div>
                    <div>
                        <label>Telefone</label>
                        <input type="text" class="form-control" name="telefone">
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div>
                        <label>Tipo de Fornecedor</label>
                        <select class="form-select" name="tipo">
                            <option value="">Todos</option>
                            <option>Fabricante</option>
                            <option>Distribuidor / Fornecedor Comercial</option>
                            <option>Assistência Técnica</option>
                            <option>Fornecedor de Consumíveis</option>
                        </select>
                    </div>
                    <div>
                        <label>Pessoa de Contacto</label>
                        <input type="text" class="form-control" name="pessoa_contacto">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="reset" class="btn btn-outline-secondary btn-sm">Limpar</button>
                        <button type="submit" class="btn btn-success btn-sm">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <p class="text-center text-danger"><?= $erro ?></p>
        <?php elseif (count($resultados) == 0) : ?>
            <p class="text-muted">Não existem fornecedores registados.</p>
        <?php else : ?>
            <div class="table-responsive">
            <table id="tabela-fornecedores" class="table table-striped table-bordered shadow-sm">
                <thead class="table-success">
                    <tr>
                        <th>Nome</th>
                        <th>NIF</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Pessoa de Contacto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $forn) : ?>
                        <tr>
                            <td><?= $forn->nome ?></td>
                            <td><?= $forn->nif ?></td>
                            <td><?= $forn->telefone ?></td>
                            <td><?= $forn->email ?></td>
                            <td><?= $forn->tipo ?></td>
                            <td><?= $forn->pessoa_contacto ?></td>
                            <td>
                                <a href="detalhes.php?id=<?= $forn->id ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php?id=<?= $forn->id ?>" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarFornecedor"
                                    data-id="<?= $forn->id ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>


        <div class="col">
            <p class="mb-5">Total: <strong><?= count($resultados) ?></strong></p>
        </div>

        <div class="modal fade" id="modalEliminarFornecedor" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Eliminar Fornecedor
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja apagar este fornecedor?</p>
                        <p class="text-muted">Esta ação é irreversível.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a id="btnConfirmarEliminarFornecedor" href="#" class="btn btn-danger">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalEliminarFornecedor');
            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const id = btn.getAttribute('data-id');
                document.getElementById('btnConfirmarEliminarFornecedor').href = 'listar.php?apagar=' + id;
            });
        });
    </script>
    <script>
        //nota
        $(document).ready(function() {
            $('#tabela-fornecedores').DataTable({
                pageLength: 5,
                pagingType: "full_numbers",
                language: {
                    decimal: "",
                    emptyTable: "Sem dados disponíveis na tabela.",
                    info: "Mostrando _START_ até _END_ de _TOTAL_ registos",
                    infoEmpty: "Mostrando 0 até 0 de 0 registos",
                    infoFiltered: "(Filtrando _MAX_ total de registos)",
                    infoPostFix: "",
                    thousands: ",",
                    lengthMenu: "Mostrando _MENU_ registos por página.",
                    loadingRecords: "Carregando...",
                    processing: "Processando...",
                    search: "Filtrar:",
                    zeroRecords: "Nenhum registo encontrado.",
                    paginate: {
                        first: "Primeira",
                        last: "Última",
                        next: "Seguinte",
                        previous: "Anterior"
                    }
                }
            });
        });
    </script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>