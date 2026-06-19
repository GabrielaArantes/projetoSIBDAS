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