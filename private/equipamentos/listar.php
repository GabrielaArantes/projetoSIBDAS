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
        $stmt = $ligacao->prepare("DELETE FROM equipamento WHERE id = ?");
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
    $resultados = $ligacao->query("SELECT e.*, l.servico FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id")->fetchAll(PDO::FETCH_OBJ);
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
            <h1>Equipamentos</h1>
            <a href="inserir.php" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar Equipamento
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">

                <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar..." name="pesquisa">

                <div class="menu-wrapper">
                    <button class="btn btn-outline-success">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <div class="menu-box">
                        <div>
                            <label>Código Interno</label>
                            <input type="text" class="form-control" name="codigo_interno">
                        </div>
                        <div>
                            <label>Designação</label>
                            <input type="text" class="form-control" name="designacao">
                        </div>
                        <div>
                            <label>Marca</label>
                            <input type="text" class="form-control" name="marca">
                        </div>
                        <div>
                            <label>Modelo</label>
                            <input type="text" class="form-control" name="modelo">
                        </div>
                        <div>
                            <label>Número de Série</label>
                            <input type="text" class="form-control" name="num_serie">
                        </div>
                        <div>
                            <label>Serviço</label>
                            <select class="form-select" name="servico">
                                <option value="">Todos</option>
                                <option>Urgência</option>
                                <option>Bloco Operatório</option>
                                <option>UCI</option>
                                <option>Internamento</option>
                                <option>Consultas</option>
                                <option>Armazém</option>
                            </select>
                        </div>
                        <div>
                            <label>Estado</label>
                            <select class="form-select" name="estado">
                                <option value="">Todos</option>
                                <option>Ativo</option>
                                <option>Inativo</option>
                                <option>Em manutenção</option>
                                <option>Abatido</option>
                            </select>
                        </div>
                        <div>
                            <label>Fornecedor</label>
                            <select class="form-select" name="fornecedor">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div>
                            <label>Categoria</label>
                            <select class="form-select" name="categoria">
                                <option value="">Todas</option>
                                <option>Monitorização</option>
                                <option>Imagiologia</option>
                                <option>Laboratório</option>
                                <option>Cirurgia</option>
                                <option>Suporte de Vida</option>
                                <option>Outros</option>
                            </select>
                        </div>
                        <div>
                            <label>Localização</label>
                            <select class="form-select" name="localizacao">
                                <option value="">Todas</option>
                                <option>Urgência</option>
                                <option>Bloco Operatório</option>
                                <option>UCI</option>
                                <option>Internamento</option>
                                <option>Consultas</option>
                                <option>Armazém</option>
                            </select>
                        </div>
                        <div>
                            <label>Criticidade</label>
                            <select class="form-select" name="criticidade">
                                <option value="">Todas</option>
                                <option>Alta</option>
                                <option>Média</option>
                                <option>Baixa</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="reset" class="btn btn-outline-secondary btn-sm">Limpar</button>
                            <button type="submit" class="btn btn-success btn-sm">Aplicar</button>
                        </div>
                    </div>
                </div>

                <div class="menu-wrapper">
                    <button class="btn btn-outline-primary">
                        <i class="fa-solid fa-arrow-down-wide-short"></i> Ordenar
                    </button>
                    <div class="menu-box">
                        <div>
                            <label>Ordenar por</label>
                            <select class="form-select" name="ordenar">
                                <option value="">Selecione...</option>
                                <option value="custo_asc">Custo de aquisição (crescente)</option>
                                <option value="custo_desc">Custo de aquisição (decrescente)</option>
                                <option value="data_asc">Data de aquisição (crescente)</option>
                                <option value="data_desc">Data de aquisição (decrescente)</option>
                                <option value="ano_asc">Ano de fabrico (crescente)</option>
                                <option value="ano_desc">Ano de fabrico (decrescente)</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
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
                            <td><?= $eq->estado ?></td>
                            <td>
                                <a href="detalhes.php?id=<?= $eq->id ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php?id=<?= aes_encrypt($eq->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarEquipamento"
                                    data-id="<?= $eq->id ?>">
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
                        <p class="text-muted">Esta ação é irreversível.</p>
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
                document.getElementById('btnConfirmarEliminarEquipamento').href = 'listar.php?apagar=' + id;
            });
        });
    </script>

    <script>
        //fazer sem o filtrar mais para a frente, para tentar usar o meu
        $(document).ready(function() {
            $('#tabela-equipamentos').DataTable({
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
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>