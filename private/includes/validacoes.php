<?php

function validar_nome(string $nome): array {
    $erros = [];

    if (empty(trim($nome))) {
        $erros[] = "O campo Nome é obrigatório.";
    } elseif (preg_match('/\d/', $nome)) {
        $erros[] = "O campo Nome não pode conter números.";
    }

    return $erros;
}

function validar_designacao(string $designacao): array {
    $erros = [];

    if (empty(trim($designacao))) {
        $erros[] = "A Designação do equipamento é obrigatória.";
    } elseif (preg_match('/\d/', $designacao)) {
        $erros[] = "A Designação não pode conter números.";
    }

    return $erros;
}

function validar_marca(string $marca): array {
    $erros = [];

    if (empty(trim($marca))) {
        $erros[] = "A Marca é obrigatória.";
    }

    return $erros;
}

function validar_modelo(string $modelo): array {
    $erros = [];

    if (empty(trim($modelo))) {
        $erros[] = "O Modelo é obrigatório.";
    }

    return $erros;
}

function validar_numero_serie(string $numeroSerie): array {
    $erros = [];

    if (empty(trim($numeroSerie))) {
        $erros[] = "O Número de Série é obrigatório.";
    }

    return $erros;
}

// Valida unicidade do número de série entre equipamentos ativos (exclui o próprio registo em edição)
function validar_numero_serie_unico(string $numeroSerie, ?int $idExcluir = null): array {
    $erros = [];

    if (empty(trim($numeroSerie))) {
        return $erros; // já validado por validar_numero_serie
    }

    try {
        $pdo = get_pdo();
        if ($idExcluir !== null) {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM equipamento WHERE num_serie = ? AND equipamento_ativo = 1 AND id != ?"
            );
            $stmt->execute([trim($numeroSerie), $idExcluir]);
        } else {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM equipamento WHERE num_serie = ? AND equipamento_ativo = 1"
            );
            $stmt->execute([trim($numeroSerie)]);
        }
        if ((int)$stmt->fetchColumn() > 0) {
            $erros[] = "Já existe um equipamento ativo com este Número de Série.";
        }
    } catch (PDOException $e) {
        // Se a BD falhar, ignora a verificação de unicidade (não bloqueia o utilizador)
    }

    return $erros;
}

// Valida unicidade do código interno entre todos os equipamentos (exclui o próprio registo em edição)
function validar_codigo_interno_unico(string $codigo, ?int $idExcluir = null): array {
    $erros = [];

    if (empty(trim($codigo))) {
        return $erros; // campo obrigatório validado noutro sítio se necessário
    }

    try {
        $pdo = get_pdo();
        if ($idExcluir !== null) {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM equipamento WHERE codigo_interno = ? AND id != ?"
            );
            $stmt->execute([strtoupper(trim($codigo)), $idExcluir]);
        } else {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM equipamento WHERE codigo_interno = ?"
            );
            $stmt->execute([strtoupper(trim($codigo))]);
        }
        if ((int)$stmt->fetchColumn() > 0) {
            $erros[] = "Já existe um equipamento com o Código Interno \"" . strtoupper(trim($codigo)) . "\". Escolha outro código.";
        }
    } catch (PDOException $e) {
        // Se a BD falhar, não bloqueia
    }

    return $erros;
}

function validar_fabricante(string $fabricante): array {
    $erros = [];

    if (empty(trim($fabricante))) {
        $erros[] = "O Fabricante é obrigatório.";
    }

    return $erros;
}

function validar_select_obrigatorio(string $valor, string $nomeCampo): array {
    $erros = [];

    if (empty(trim($valor))) {
        $erros[] = "O campo $nomeCampo é obrigatório.";
    }

    return $erros;
}

function validar_tipo_documento(string $tipo): array {
    $erros = [];

    if (empty(trim($tipo))) {
        $erros[] = "O Tipo de Documento é obrigatório.";
    }

    return $erros;
}

function validar_data(string $data, string $nomeCampo): array {
    $erros = [];

    if (empty(trim($data))) {
        $erros[] = "O campo $nomeCampo é obrigatório.";
    }

    return $erros;
}

// Valida data de aquisição: obrigatória, formato correto e não pode ser futura
function validar_data_aquisicao(string $data): array {
    $erros = [];

    if (empty(trim($data))) {
        return $erros; // campo opcional
    }

    $dt = DateTime::createFromFormat('Y-m-d', $data);
    if (!$dt || $dt->format('Y-m-d') !== $data) {
        $erros[] = "A Data de Aquisição tem um formato inválido.";
        return $erros;
    }

    if ($dt > new DateTime('today')) {
        $erros[] = "A Data de Aquisição não pode ser uma data futura.";
    }

    return $erros;
}

// Valida ano de fabrico: entre 1900 e o ano atual
function validar_ano_fabrico(string $ano): array {
    $erros = [];

    if (empty(trim($ano))) {
        return $erros; // campo opcional
    }

    if (!ctype_digit($ano)) {
        $erros[] = "O Ano de Fabrico deve ser um número inteiro.";
        return $erros;
    }

    $anoInt = (int)$ano;
    $anoAtual = (int)date('Y');

    if ($anoInt < 1900 || $anoInt > $anoAtual) {
        $erros[] = "O Ano de Fabrico deve estar entre 1900 e $anoAtual.";
    }

    return $erros;
}

// Valida custo de aquisição: obrigatório se tipo_entrada for Comprar ou Alugar, e deve ser positivo
function validar_custo_aquisicao(string $custo, string $nomeTipoEntrada): array {
    $erros = [];

    $tiposComCusto = ['Comprar', 'Alugar'];
    $obrigatorio = in_array(trim($nomeTipoEntrada), $tiposComCusto);

    if ($obrigatorio && trim($custo) === '') {
        $erros[] = "O Custo de Aquisição é obrigatório quando o Tipo de Entrada é \"$nomeTipoEntrada\".";
        return $erros;
    }

    if (trim($custo) !== '' && (!is_numeric($custo) || (float)$custo < 0)) {
        $erros[] = "O Custo de Aquisição deve ser um valor positivo.";
    }

    return $erros;
}

// Valida data de fim de garantia: deve ser posterior à data de início
function validar_data_fim_garantia(string $inicio, string $fim): array {
    $erros = [];

    if (empty(trim($inicio)) || empty(trim($fim))) {
        return $erros; // ambos opcionais individualmente
    }

    $dtInicio = DateTime::createFromFormat('Y-m-d', $inicio);
    $dtFim    = DateTime::createFromFormat('Y-m-d', $fim);

    if (!$dtInicio || !$dtFim) {
        return $erros; // formatos inválidos — já validados noutro sítio
    }

    if ($dtFim <= $dtInicio) {
        $erros[] = "A data de fim da garantia deve ser posterior à data de início.";
    }

    return $erros;
}

// Valida nome do documento: obrigatório e não pode conter números
function validar_nome_documento(string $nome): array {
    $erros = [];

    if (empty(trim($nome))) {
        $erros[] = "O Nome do Documento é obrigatório.";
    } elseif (preg_match('/\d/', $nome)) {
        $erros[] = "O Nome do Documento não pode conter números.";
    }

    return $erros;
}

// Valida extensão do ficheiro enviado: só aceita PDF, JPG, PNG, DOC, DOCX
function validar_ficheiro_documento(array $file): array {
    $erros = [];

    if (empty($file['name'])) {
        $erros[] = "O Ficheiro é obrigatório.";
        return $erros;
    }

    $extensoesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas)) {
        $erros[] = "Apenas são aceites ficheiros PDF, JPG, PNG, DOC ou DOCX.";
    }

    return $erros;
}

function validar_edificio(string $edificio): array {
    $erros = [];

    if (empty(trim($edificio))) {
        $erros[] = "O Edifício é obrigatório.";
    }

    return $erros;
}

function validar_piso(string $piso): array {
    $erros = [];

    if (empty(trim($piso))) {
        $erros[] = "O Piso é obrigatório.";
    }

    return $erros;
}

function validar_servico(string $servico): array {
    $erros = [];

    if (empty(trim($servico))) {
        $erros[] = "O Serviço / Departamento é obrigatório.";
    }

    return $erros;
}