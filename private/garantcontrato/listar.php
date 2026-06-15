<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Garantias e Contratos</h1>
            <a href="inserir.php" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">

            <input type="text" class="form-control" style="width: 250px;" placeholder="Pesquisar..." name="pesquisa">

            <div class="menu-wrapper">
                <button class="btn btn-outline-success">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>

                <div class="menu-box">

                    <div>
                        <label>Tipo de contrato</label>
                        <select class="form-select" name="tipo">
                            <option value="">Todos</option>
                            <option>Garantia</option>
                            <option>Contrato de Manutenção</option>
                            <option>Assistência Técnica</option>
                        </select>
                    </div>

                    <div>
                        <label>Entidade responsável</label>
                        <input type="text" class="form-control" name="entidade">
                    </div>

                    <div>
                        <label>Periodicidade</label>
                        <select class="form-select" name="periodicidade">
                            <option value="">Todas</option>
                            <option>Mensal</option>
                            <option>Trimestral</option>
                            <option>Semestral</option>
                            <option>Anual</option>
                        </select>
                    </div>

                    <div>
                        <label>Data início (mín.)</label>
                        <input type="date" class="form-control" name="inicio_min">
                    </div>

                    <div>
                        <label>Data fim (máx.)</label>
                        <input type="date" class="form-control" name="fim_max">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2" style="grid-column: span 2;">
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
                            <option value="inicio_asc">Data de início (crescente)</option>
                            <option value="inicio_desc">Data de início (decrescente)</option>
                            <option value="fim_asc">Data de fim (crescente)</option>
                            <option value="fim_desc">Data de fim (decrescente)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
                    </div>

                </div>
            </div>

        </div>

        <table class="table table-striped table-bordered shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Entidade</th>
                    <th>Periodicidade</th>
                    <th>Início</th>
                    <th>Fim</th>
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

        <div class="modal fade" id="modalEliminar" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Eliminar</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Deseja apagar?</p>
                        <p class="text-muted">Esta ação é irreversível.</p>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" class="btn btn-danger">Eliminar</a>
                    </div>

                </div>
            </div>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
