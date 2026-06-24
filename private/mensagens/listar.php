<?php
// Lista as mensagens de contacto recebidas pelo formulário público
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador'], '/private/dashboard/dashboard.php');
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
    $resultados = $ligacao->query("SELECT * FROM mensagem_contacto ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
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
            <h1>Mensagens de Contacto</h1>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <input type="text" class="form-control" id="filtro-pesquisa" style="width: 250px;" placeholder="Pesquisar mensagem..." name="pesquisa">

            <div class="menu-wrapper">
                <button type="button" class="btn btn-outline-success">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <div class="menu-box">
                    <div>
                        <label>Estado</label>
                        <select class="form-select" id="filtro-estado" name="estado">
                            <option value="">Todos</option>
                            <option value="Não Lida">Não Lida</option>
                            <option value="Lida">Lida</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" id="filtro-limpar" class="btn btn-outline-secondary btn-sm">Limpar</button>
                        <button type="button" id="filtro-aplicar" class="btn btn-success btn-sm">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <p class="text-center text-danger"><?= $erro ?></p>
        <?php elseif (count($resultados) == 0) : ?>
            <p class="text-muted">Não existem mensagens de contacto registadas.</p>
        <?php else : ?>
            <div class="table-responsive">
            <table id="tabela-mensagens" class="table table-striped table-bordered shadow-sm">
                <thead class="table-success">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telemóvel</th>
                        <th>Mensagem</th>
                        <th>Data</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $msg) : ?>
                        <tr class="<?= $msg->mensagem_lida == 0 ? 'fw-bold' : '' ?>">
                            <td><?= htmlspecialchars($msg->nome) ?></td>
                            <td><?= htmlspecialchars($msg->email) ?></td>
                            <td><?= htmlspecialchars($msg->telemovel) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($msg->mensagem, 0, 60, '...')) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($msg->created_at)) ?></td>
                            <td>
                                <?php if ($msg->mensagem_lida == 1) : ?>
                                    <span class="badge bg-secondary">Lida</span>
                                <?php else : ?>
                                    <span class="badge bg-success">Não Lida</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="detalhes.php?id=<?= aes_encrypt($msg->id) ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($msg->mensagem_lida == 1) : ?>
                                    <a href="marcar_lida.php?id=<?= aes_encrypt($msg->id) ?>" class="btn btn-outline-secondary btn-sm" title="Marcar como não lida">
                                        <i class="fa-solid fa-envelope"></i>
                                    </a>
                                <?php else : ?>
                                    <a href="marcar_lida.php?id=<?= aes_encrypt($msg->id) ?>" class="btn btn-success btn-sm" title="Marcar como lida">
                                        <i class="fa-solid fa-envelope-open"></i>
                                    </a>
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

    </main>

    <script>
        //nota
        $(document).ready(function() {
            const tabela = $('#tabela-mensagens').DataTable({
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

            $('#tabela-mensagens_filter').hide();

            $('#filtro-pesquisa').on('keyup', function() {
                tabela.search(this.value).draw();
            });

            $('#filtro-aplicar').on('click', function() {
                const estado = $('#filtro-estado').val();

                // Coluna 5 = Estado
                tabela.column(5).search(estado);

                tabela.draw();
            });

            $('#filtro-limpar').on('click', function() {
                $('#filtro-estado').val('');
                tabela.column(5).search('');
                tabela.draw();
            });
        });
    </script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>