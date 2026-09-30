<?php

header("Content-Type: application/json");

require "conexao.php";

$metodo = $_SERVER["REQUEST_METHOD"];

if($metodo == "POST"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    if(
        !isset($dados["nome"]) ||
        !isset($dados["categoria"]) ||
        !isset($dados["fornecedor"]) ||
        !isset($dados["quantidade"]) ||
        !isset($dados["preco_unitario"]) ||
        $dados["nome"] == "" ||
        $dados["categoria"] == "" ||
        $dados["fornecedor"] == ""
    ){
        echo json_encode([
            "Mensagem"=>"Preencha todos os campos!"
        ]);
        exit;
    }

    if(
        $dados["categoria"] != "eletrica" &&
        $dados["categoria"] != "mecanica" &&
        $dados["categoria"] != "hidraulica"
    ){
        echo json_encode([
            "Mensagem"=>"Categoria inválida! Selecione apenas: eletrica, mecanica ou hidraulica"
        ]);
        exit;
    }

    if($dados["quantidade"] < 0){
        echo json_encode([
            "Mensagem"=>"Quantidade inválida!"
        ]);
        exit;
    }

    if($dados["preco_unitario"] <= 0){
        echo json_encode([
            "Mensagem"=>"Preço unitário inválido!"
        ]);
        exit;
    }

    $sql = "INSERT INTO pecas
    (nome,categoria,fornecedor,quantidade,preco_unitario)
    VALUES (?,?,?,?,?)";

    $comando = $pdo->prepare($sql);

    $comando->execute([
        $dados["nome"],
        $dados["categoria"],
        $dados["fornecedor"],
        $dados["quantidade"],
        $dados["preco_unitario"]
    ]);

    echo json_encode([
        "Mensagem"=>"Peças cadastrada com sucesso! 😊"
    ]);
    exit;
}


if($metodo == "GET"){

    $sql = "SELECT * FROM pecas ORDER BY id";

    $comando = $pdo->query($sql);

    $pecas = $comando->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pecas);
    exit;
}


if($metodo == "PUT"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    if(
        !isset($dados["id"]) ||
        !isset($dados["nome"]) ||
        !isset($dados["categoria"]) ||
        !isset($dados["fornecedor"]) ||
        !isset($dados["quantidade"]) ||
        !isset($dados["preco_unitario"]) ||
        $dados["nome"] == "" ||
        $dados["categoria"] == "" ||
        $dados["fornecedor"] == ""
    ){
        echo json_encode([
            "Mensagem"=>"Preencha todos os campos!"
        ]);
        exit;
    }

    if(
        $dados["categoria"] != "eletrica" &&
        $dados["categoria"] != "mecanica" &&
        $dados["categoria"] != "hidraulica"
    ){
        echo json_encode([
            "Mensagem"=>"Categoria inválida!"
        ]);
        exit;
    }

    if($dados["quantidade"] < 0){
        echo json_encode([
            "Mensagem"=>"Quantidade inválida!"
        ]);
        exit;
    }

    if($dados["preco_unitario"] <= 0){
        echo json_encode([
            "Mensagem"=>"Preço unitário inválido!"
        ]);
        exit;
    }

    $sql = "UPDATE pecas
    SET nome=?,categoria=?,fornecedor=?,quantidade=?,preco_unitario=?
    WHERE id=?";

    $comando = $pdo->prepare($sql);

    $comando->execute([
        $dados["nome"],
        $dados["categoria"],
        $dados["fornecedor"],
        $dados["quantidade"],
        $dados["preco_unitario"],
        $dados["id"]
    ]);

    echo json_encode([
        "Mensagem"=>"Peças atualizada com sucesso! 😊"
    ]);
    exit;
}


if($metodo == "DELETE"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    if(!isset($dados["id"])){
        echo json_encode([
            "Mensagem"=>"Informe o id da Peça!"
        ]);
        exit;
    }

    $sql = "DELETE FROM pecas WHERE id=?";

    $comando = $pdo->prepare($sql);

    $comando->execute([
        $dados["id"]
    ]);

    echo json_encode([
        "Mensagem"=>"Peça excluída com sucesso! 😊"
    ]);
    exit;
}
