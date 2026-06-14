<?php
require_once __DIR__ . '/../../private/includes/funcoes.php';
redirect_if_not_logged();
start_session();
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

            <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar localização..."
                name="pesquisa">

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

        <table class="table table-striped table-bordered shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Edifício</th>
                    <th>Piso</th>
                    <th>Serviço / Departamento</th>
                    <th>Sala / Gabinete</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

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
            </tbody>
        </table>

        <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Eliminar
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Deseja apagar?</p>
                        <p class="text-muted">Esta ação é irreversível.</p>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <a id="btnConfirmarEliminar" href="#" class="btn btn-danger">
                            Eliminar
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>