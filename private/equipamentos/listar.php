<?php
// Lista todos os equipamentos com pesquisa, filtros e paginação via DataTables
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
require_once __DIR__ . '/../includes/validacoes.php';
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
    $resultados = $ligacao->query("SELECT e.*, l.servico FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id")->fetchAll(PDO::FETCH_OBJ);
    $categorias_filtro = $ligacao->query("SELECT nome FROM categorias_equipamento ORDER BY nome")->fetchAll(PDO::FETCH_COLUMN);
    $servicos_filtro   = $ligacao->query("SELECT DISTINCT servico FROM localizacao WHERE localizacao_ativo = 1 AND servico IS NOT NULL ORDER BY servico")->fetchAll(PDO::FETCH_COLUMN);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = [];
    $categorias_filtro = [];
    $servicos_filtro = [];
}
$ligacao = null;
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Equipamentos</h1>
            <div class="d-flex gap-2">
                <?php if ($pode_gerir) : ?>
                    <a href="inserir.php" class="btn btn-success">
                        <i class="fa-solid fa-plus"></i> Adicionar Equipamento
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">

                <input type="text" class="form-control" id="filtro-pesquisa" style="width: 250px;" placeholder="Pesquisar..." name="pesquisa">

                <div class="menu-wrapper">
                    <button type="button" class="btn btn-outline-success">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <div class="menu-box">
                        <div>
                            <label>Estado</label>
                            <select class="form-select" id="filtro-estado" name="estado">
                                <option value="">Todos</option>
                                <option>Ativo</option>
                                <option>Inativo</option>
                                <option>Em manutenção</option>
                                <option>Em calibração</option>
                                <option>Em quarentena</option>
                                <option>Abatido</option>
                            </select>
                        </div>
                        <div>
                            <label>Categoria</label>
                            <select class="form-select" id="filtro-categoria" name="categoria">
                                <option value="">Todas</option>
                                <?php foreach ($categorias_filtro as $cat) : ?>
                                    <option><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Localização</label>
                            <select class="form-select" id="filtro-localizacao" name="localizacao">
                                <option value="">Todas</option>
                                <?php foreach ($servicos_filtro as $srv) : ?>
                                    <option><?= htmlspecialchars($srv) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="button" id="filtro-limpar" class="btn btn-outline-secondary btn-sm">Limpar</button>
                            <button type="button" id="filtro-aplicar" class="btn btn-success btn-sm">Aplicar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <p class="text-center text-danger"><?= $erro ?></p>
        <?php elseif (count($resultados) == 0) : ?>
            <p class="text-muted">Não existem equipamentos registados.</p>
        <?php else : ?>

            <div class="table-responsive">
            <table id="tabela-equipamentos" class="table table-striped table-bordered shadow-sm">
                <thead class="table-success">
                    <tr>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Localização</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $eq) : ?>
                        <tr>
                            <td><?= $eq->nome ?></td>
                            <td><?= $eq->categoria ?></td>
                            <td><?= $eq->servico ?></td>
                            <td>
                                <?= $eq->estado ?>
                                <?php if ($eq->equipamento_ativo == 0) : ?>
                                    <span class="badge bg-secondary ms-1">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="detalhes.php?id=<?= aes_encrypt($eq->id) ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($pode_gerir) : ?>
                                    <a href="editar.php?id=<?= aes_encrypt($eq->id) ?>" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <?php if ($eq->equipamento_ativo == 0) : ?>
                                        <a href="confirmar_apagar.php?id=<?= aes_encrypt($eq->id) ?>" class="btn btn-success btn-sm" title="Reativar">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </a>
                                    <?php else : ?>
                                        <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEliminarEquipamento"
                                            data-id="<?= aes_encrypt($eq->id) ?>"
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
            </div>

        <?php endif; ?>

        <div class="col">
            <p class="mb-5">Total: <strong><?= count($resultados) ?></strong></p>
        </div>

        <div class="modal fade" id="modalEliminarEquipamento" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Eliminar Equipamento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja apagar este equipamento?</p>
                        <p class="text-muted">O equipamento não será apagado da base de dados, apenas ficará marcado como inativo.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a id="btnConfirmarEliminarEquipamento" href="#" class="btn btn-danger">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalEliminarEquipamento');
            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const id = btn.getAttribute('data-id');
                document.getElementById('btnConfirmarEliminarEquipamento').href = 'confirmar_apagar.php?id=' + encodeURIComponent(id);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const tabela = $('#tabela-equipamentos').DataTable({
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
                    },
                    aria: {
                        sortAscending: ": ative para classificar em ordem crescente.",
                        sortDescending: ": ative para classificar em ordem decrescente."
                    }
                }
            });

            $('#tabela-equipamentos_filter').hide();

            $('#filtro-pesquisa').on('keyup', function() {
                tabela.search(this.value).draw();
            });

            $('#filtro-aplicar').on('click', function() {
                const categoria = $('#filtro-categoria').val();
                const localizacao = $('#filtro-localizacao').val();
                const estado = $('#filtro-estado').val();

                tabela.column(1).search(categoria);
                tabela.column(2).search(localizacao);
                tabela.column(3).search(estado);

                tabela.draw();
            });

            $('#filtro-limpar').on('click', function() {
                $('#filtro-estado, #filtro-categoria, #filtro-localizacao').val('');
                tabela.column(1).search('');
                tabela.column(2).search('');
                tabela.column(3).search('');
                tabela.draw();
            });
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>