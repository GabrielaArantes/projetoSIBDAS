<?php
require_once __DIR__ . '/../../config/config.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Inserir Fornecedor</title>

     <link rel="stylesheet" href="../../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/1241094.css">
    <link rel="stylesheet" href="../../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/png" href="../../assets/img/logHospital.png">

</head>

<body class="pagprivada">

    <aside class="sidebar">
        
        <nav>
            <a href="../dashboard/dashboard.html"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="../equipamentos/listar.html"><i class="fa-solid fa-stethoscope"></i> Equipamentos</a>
            <a href="listar.html" class="active"><i class="fa-solid fa-truck"></i> Fornecedores</a>
            <a href="../localizacao/listar.html"><i class="fa-solid fa-location-dot"></i> Localização</a>
            <a href="../garantcontrato/listar.html"><i class="fa-solid fa-file-contract"></i> Garantias/Contratos</a>
            <a href="../documentacao/listar.html"><i class="fa-solid fa-folder-open"></i> Documentação</a>
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

        <h1 class="mb-4">Inserir Fornecedor</h1>

        <form class="shadow p-4 rounded" style="max-width: 800px;">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome da Empresa</label>
                    <input type="text" class="form-control" placeholder="Ex: Medstock Portugal">
                </div>

                <div class="col">
                    <label class="form-label">NIF</label>
                    <input type="number" class="form-control" placeholder="Ex: 501234657">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" placeholder="Ex: 912 345 678">
                </div>

            <div class="col">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Ex: suporte@medstock.pt">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Morada</label>
                <input type="text" class="form-control" placeholder="Ex:Rua da Saúde 120, 4200-450 Porto">
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" placeholder="Ex: https://www.medstock.com">
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Pessoa de Contacto</label>
                    <input type="text" class="form-control" placeholder="Ex: Gabriela Arantes">
                </div>
                
                <div class="col">
                    <label class="form-label">Telefone da Pessoa de Contacto</label>
                    <input type="text" class="form-control" placeholder="Ex: 934 567 890">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Fornecedor</label>
                <select class="form-select">
                    <option value="">Selecione...</option>
                    <option>Fabricante</option>
                    <option>Distribuidor / Fornecedor Comercial</option>
                    <option>Assistência Técnica</option>
                    <option>Fornecedor de Consumíveis</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="4"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.html" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Guardar Fornecedor
                </button>
            </div>

        </form>

    </main>

</body>
</html>