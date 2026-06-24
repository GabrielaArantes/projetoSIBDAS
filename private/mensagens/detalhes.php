<?php
// Mostra o detalhe de uma mensagem de contacto
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador'], '/private/dashboard/dashboard.php');
start_session();
?>

<?php
$erro = '';
$mensagem = null;

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("SELECT * FROM mensagem_contacto WHERE id = ?");
    $stmt->execute([$id]);
    $mensagem = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$mensagem) {
        header("Location: listar.php");
        exit;
    }

    // Ao abrir os detalhes, marcar automaticamente como lida
    if ($mensagem->mensagem_lida == 0) {
        $stmt = $ligacao->prepare("UPDATE mensagem_contacto SET mensagem_lida = 1 WHERE id = ?");
        $stmt->execute([$id]);
        $mensagem->mensagem_lida = 1;
    }

    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar mensagem.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">
            Detalhes da Mensagem
            <?php if ($mensagem) : ?>
                <?php if ($mensagem->mensagem_lida == 1) : ?>
                    <span class="badge bg-secondary">Lida</span>
                <?php else : ?>
                    <span class="badge bg-success">Não Lida</span>
                <?php endif; ?>
            <?php endif; ?>
        </h1>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php else : ?>

        <div class="shadow p-4 rounded mb-4" style="max-width: 850px;">

            <h4 class="mb-3">
                <i class="fa-solid fa-envelope me-2"></i>
                Mensagem de Contacto
            </h4>

            <div class="mb-3">
                <strong>Nome:</strong>
                <p><?= htmlspecialchars($mensagem->nome) ?></p>
            </div>

            <div class="mb-3">
                <strong>Email:</strong>
                <p><?= htmlspecialchars($mensagem->email) ?></p>
            </div>

            <div class="mb-3">
                <strong>Telemóvel:</strong>
                <p><?= htmlspecialchars($mensagem->telemovel) ?></p>
            </div>

            <div class="mb-3">
                <strong>Mensagem:</strong>
                <p><?= nl2br(htmlspecialchars($mensagem->mensagem)) ?></p>
            </div>

            <div class="mb-3">
                <strong>Data de Envio:</strong>
                <p><?= date('d/m/Y H:i', strtotime($mensagem->created_at)) ?></p>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <a href="marcar_lida.php?id=<?= urlencode($idEncrypted) ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-envelope"></i> Marcar como Não Lida
                </a>
            </div>

        </div>

        <?php endif; ?>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>