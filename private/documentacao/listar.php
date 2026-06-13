<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>


<body class="pagprivada">

    <aside class="sidebar">
        <nav>
            <a href="../dashboard/dashboard.html"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="../equipamentos/listar.html"><i class="fa-solid fa-stethoscope"></i> Equipamentos</a>
            <a href="../fornecedores/listar.html"><i class="fa-solid fa-truck"></i> Fornecedores</a>
            <a href="../localizacao/listar.html"><i class="fa-solid fa-location-dot"></i> Localização</a>
            <a href="../garantcontrato/listar.html"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
            <a href="listar.html" class="active"><i class="fa-solid fa-folder-open"></i> Documentação</a>
            <a href="../gestaoconteudo/gestao.html"><i class="fa-solid fa-pen-to-square"></i> Gestão de Conteúdos Públicos</a>
        </nav>
    </aside>

    <header class="topbar">
        <div class="logo-topbar">
            <img src="../../assets/img/logHospital.png" alt="Logo MedStock">
            <h1>MedStock</h1>
        </div>

        <div class="user-button">
            <i class="fa-regular fa-user"></i>
            <span>Utilizador</span>
            <i class="fa-solid fa-chevron-down seta"></i>

            <ul class="user-dropdown">
                <li><a href="#">Mudar password</a></li>
                <li><a href="../public/login.html">Sair</a></li>
            </ul>
        </div>
    </header>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Documentação</h1>

            <a href="inserir.html" class="btn btn-success">
                <i class="fa-solid fa-plus"></i> Adicionar
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">

            <input type="text" class="form-control" style="width: 250px;"
                placeholder="Pesquisar documentação..." name="pesquisa">

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

        <table class="table table-striped table-bordered shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Equipamento</th>
                    <th>Data</th>
                    <th>Ficheiro</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody id="tabelaDocumentacao">
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <a href="detalhes.html" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="editar.html" class="btn btn-warning btn-sm">
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
                            Eliminar Documento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Deseja apagar este documento?</p>
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
