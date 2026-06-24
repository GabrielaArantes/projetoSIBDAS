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

function validar_nif(string $nif): array {
    $erros = [];
    if (empty(trim($nif))) {
        $erros[] = "O NIF é obrigatório.";
    } elseif (!preg_match('/^\d{9}$/', trim($nif))) {
        $erros[] = "O NIF deve ter exatamente 9 dígitos numéricos. Ex.: 123456789";
    }
    return $erros;
}

function validar_telefone(string $telefone, string $nomeCampo = 'Telefone'): array {
    $erros = [];
    if (empty(trim($telefone))) {
        $erros[] = "O $nomeCampo é obrigatório.";
    } elseif (!preg_match('/^[29]\d{8}$/', trim($telefone))) {
        $erros[] = "O $nomeCampo deve ter 9 dígitos e começar por 9 ou 2, sem indicativo. Ex.: 912345678";
    }
    return $erros;
}

function validar_telefone_opcional(string $telefone, string $nomeCampo = 'Telefone'): array {
    $erros = [];
    if (!empty(trim($telefone)) && !preg_match('/^[29]\d{8}$/', trim($telefone))) {
        $erros[] = "O $nomeCampo deve ter 9 dígitos e começar por 9 ou 2, sem indicativo. Ex.: 912345678";
    }
    return $erros;
}

function validar_email(string $email): array {
    $erros = [];
    if (empty(trim($email))) {
        $erros[] = "O Email é obrigatório.";
    } elseif (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O endereço de email não é válido. Ex.: nome@dominio.pt";
    }
    return $erros;
}

function validar_data_aquisicao(string $data): array {
    $erros = [];
    if (!empty(trim($data))) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $erros[] = "Formato de Data de Aquisição inválido. Use AAAA-MM-DD.";
        } else {
            $partes = explode('-', $data);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros[] = "Data de Aquisição inválida.";
            } elseif (strtotime($data) > time()) {
                $erros[] = "A Data de Aquisição não pode ser uma data futura.";
            }
        }
    }
    return $erros;
}

function validar_ano_fabrico(string $ano): array {
    $erros = [];
    if (!empty(trim($ano))) {
        if (!preg_match('/^\d{4}$/', $ano) || (int)$ano < 1900 || (int)$ano > (int)date('Y')) {
            $erros[] = "O Ano de Fabrico deve ser um ano válido entre 1900 e " . date('Y') . ".";
        }
    }
    return $erros;
}

function validar_custo(string $custo, string $tipo_entrada): array {
    $erros = [];
    $tipos_obrigatorios = ['Compra', 'Aluguer'];
    if (in_array($tipo_entrada, $tipos_obrigatorios) && empty(trim($custo))) {
        $erros[] = "O Custo de Aquisição é obrigatório para o tipo de entrada '$tipo_entrada'.";
    } elseif (!empty(trim($custo)) && (!is_numeric($custo) || (float)$custo < 0)) {
        $erros[] = "O Custo de Aquisição deve ser um valor numérico positivo. Ex.: 1500.00";
    }
    return $erros;
}