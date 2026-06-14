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
            <h1>Fornecedores</h1>

            <a href="inserir.php" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar Fornecedor
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">

            <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar fornecedor..."
                name="pesquisa">

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

        <table class="table table-striped table-bordered shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Nome da Empresa</th>
                    <th>NIF</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Pessoa de Contacto</th>
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
                    <td></td>
                    <td></td>

                    <td>
                        <a href="detalhes.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="editar.php" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalEliminarFornecedor">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </td>
                </tr>
            </tbody>
        </table>

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
                        <p>Deseja apagar?</p>
                        <p class="text-muted">Esta ação é irreversível.</p>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <a id="btnConfirmarEliminarFornecedor" href="#" class="btn btn-danger">
                            Eliminar
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>