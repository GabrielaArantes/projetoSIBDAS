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
        $stmt = $ligacao->prepare("DELETE FROM localizacao WHERE id = ?");
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
    $resultados = $ligacao->query("SELECT * FROM localizacao")->fetchAll(PDO::FETCH_OBJ);
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
            <h1>Localizações</h1>
            <a href="inserir.php" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar localização..." name="pesquisa">

            <div class="menu-wrapper">
                <button class="btn btn-outline-success">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <div class="menu-box">
                    <div>
                        <label>Edifício</label>
                        <input type="text" class="form-control" name="edificio">
                    </div>
                    <div>
                        <label>Piso</label>
                        <input type="text" class="form-control" name="piso">
                    </div>
                    <div>
                        <label>Serviço / Departamento</label>
                        <input type="text" class="form-control" name="servico">
                    </div>
                    <div>
                        <label>Sala / Gabinete</label>
                        <input type="text" class="form-control" name="sala">
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
            <p class="text-muted">Não existem localizações registadas.</p>
        <?php else : ?>
            <table id="tabela-localizacao" class="table table-striped table-bordered shadow-sm">
                <thead class="table-success">
                    <tr>
                        <th>Edifício</th>
                        <th>Piso</th>
                        <th>Serviço / Departamento</th>
                        <th>Sala / Gabinete</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $loc) : ?>
                        <tr>
                            <td><?= $loc->edificio ?></td>
                            <td><?= $loc->piso ?></td>
                            <td><?= $loc->servico ?></td>
                            <td><?= $loc->sala ?></td>
                            <td>
                                <a href="detalhes.php?id=<?= $loc->id ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php?id=<?= aes_encrypt($loc->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminar"
                                    data-id="<?= $loc->id ?>">
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
                            Eliminar Localização
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja apagar esta localização?</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalEliminar');
            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const id = btn.getAttribute('data-id');
                document.getElementById('btnConfirmarEliminar').href = 'listar.php?apagar=' + id;
            });
        });
    </script>

    <script>
        //nota
        $(document).ready(function() {
            $('#tabela-localizacao').DataTable({
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