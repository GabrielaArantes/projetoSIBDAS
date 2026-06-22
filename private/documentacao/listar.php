<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
$perfil = $_SESSION['perfil'] ?? '';
$pode_gerir = in_array($perfil, ['Administrador', 'Técnico']);
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
            <div class="d-flex gap-2">
                <?php if ($pode_gerir) : ?>
                    <a href="inserir.php" class="btn btn-success">
                        <i class="fa-solid fa-plus"></i> Adicionar
                    </a>
                <?php endif; ?>
                <a href="exportar.php?formato=csv" class="btn btn-outline-success" title="Exportar CSV">
                    <i class="fa-solid fa-file-csv"></i> CSV
                </a>
                <a href="exportar.php?formato=json" class="btn btn-outline-success" title="Exportar JSON">
                    <i class="fa-solid fa-file-code"></i> JSON
                </a>
                <a href="exportar.php?formato=pdf" class="btn btn-outline-success" title="Exportar PDF" target="_blank">
                    <i class="fa-solid fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <input type="text" class="form-control" id="filtro-pesquisa" style="width: 250px;" placeholder="Pesquisar documentação..." name="pesquisa">

            <div class="menu-wrapper">
                <button type="button" class="btn btn-outline-success">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <div class="menu-box">
                    <div>
                        <label>Tipo</label>
                        <select class="form-select" id="filtro-tipo" name="tipo">
                            <option value="">Todos</option>
                            <option>Manual</option>
                            <option>Fatura</option>
                            <option>Certificado</option>
                            <option>Relatório Técnico</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2" style="grid-column: span 2;">
                        <button type="button" id="filtro-limpar" class="btn btn-outline-secondary btn-sm">Limpar</button>
                        <button type="button" id="filtro-aplicar" class="btn btn-success btn-sm">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <p class="text-center text-danger"><?= $erro ?></p>
        <?php elseif (count($resultados) == 0) : ?>
            <p class="text-muted">Não existem documentos registados.</p>
        <?php else : ?>
            <table id="tabela-documentacao" class="table table-striped table-bordered shadow-sm">
                <thead class="table-success">
                    <tr>
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
                            <td>
                                <?= $doc->tipo ?>
                                <?php if ($doc->documento_ativo == 0) : ?>
                                    <span class="badge bg-secondary ms-1">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $doc->nome ?></td>
                            <td><?= $doc->nome_equipamento ?></td>
                            <td><?= $doc->data_documento ?></td>
                            <td><?= $doc->data_validade ?></td>
                            <td>
                                <a href="detalhes.php?id=<?= aes_encrypt($doc->id) ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($pode_gerir) : ?>
                                    <a href="editar.php?id=<?= aes_encrypt($doc->id) ?>" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <?php if ($doc->documento_ativo == 0) : ?>
                                        <a href="confirmar_apagar.php?id=<?= aes_encrypt($doc->id) ?>" class="btn btn-success btn-sm" title="Reativar">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </a>
                                    <?php else : ?>
                                        <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEliminar"
                                            data-id="<?= aes_encrypt($doc->id) ?>"
                                            title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
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
                        <p class="text-muted">O documento não será apagado da base de dados, apenas ficará marcado como inativo.</p>
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
                document.getElementById('btnConfirmarEliminar').href = 'confirmar_apagar.php?id=' + encodeURIComponent(id);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const tabela = $('#tabela-documentacao').DataTable({
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
                    paginate: { first: "Primeira", last: "Última", next: "Seguinte", previous: "Anterior" }
                }
            });

            $('#tabela-documentacao_filter').hide();

            $('#filtro-pesquisa').on('keyup', function() { tabela.search(this.value).draw(); });

            $('#filtro-aplicar').on('click', function() {
                tabela.column(0).search($('#filtro-tipo').val());
                tabela.draw();
            });

            $('#filtro-limpar').on('click', function() {
                $('#filtro-tipo').val('');
                tabela.column(0).search('');
                tabela.draw();
            });
        });
    </script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>