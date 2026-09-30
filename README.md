# Atividade Prática - API e Consultas

#### A Atividade:

Uma indústria precisa melhorar o controle das peças de reposição guardadas no almoxarifado. Hoje, as entradas e saídas são anotadas em cadernos e planilhas soltas. Por isso, é difícil saber quantas peças existem e quanto dinheiro está parado no estoque.

Você foi contratado para desenvolver uma API em PHP que permita registrar e gerenciar essas peças. Para isso, crie um banco de dados chamado almoxarifado e uma tabela chamada pecas. A tabela deverá ter os campos id, nome, categoria, fornecedor, quantidade e preco_unitario. O campo id deverá ser a chave primária e ter incremento automático. O campo quantidade deverá ser do tipo inteiro e o campo preco_unitario, do tipo decimal.

A API deverá usar PDO, receber e devolver os dados em JSON e implementar as quatro operações básicas de um CRUD:
o POST deverá cadastrar uma nova peça;
o GET deverá listar todas as peças cadastradas;
o PUT deverá atualizar uma peça pelo id;
o DELETE deverá excluir uma peça.
A categoria deverá aceitar os valores eletrica, mecanica ou hidraulica. A quantidade não poderá ser negativa e o preço unitário deverá ser maior que zero. Antes de executar qualquer operação, o sistema deverá verificar se os dados obrigatórios foram informados. Ao concluir uma operação, a API deverá retornar uma mensagem em JSON com o resultado.

Com a API pronta, cadastre entre 10 e 15 peças pelo POST da própria API, e não diretamente no banco de dados. Os registros deverão incluir as três categorias e ter quantidades e preços variados.

Por fim, a gerência quer usar esses dados para tomar decisões. Com as funções SUM, MAX, MIN e AVG, apelidos com AS e, quando necessário, a cláusula WHERE, escreva uma consulta SQL que responda a cada pergunta abaixo:

Quantas unidades de peças existem no almoxarifado, somando todas?
Qual é o valor total do estoque, em reais? (Lembre-se: cada peça pode ter várias unidades.)
Qual é o preço unitário da peça mais cara?
Qual é o preço unitário da peça mais barata?
Qual é o preço médio das peças, com duas casas decimais? (Pesquise a função ROUND.)
Qual é o valor total em estoque só das peças da categoria eletrica?

## Primeiro criamos o Banco de Dados chamado Almoxarifado:

```sql
CREATE DATABASE almoxarifado;
```

## Depois criamos a tabela chamada pecas:

```sql
CREATE TABLE pecas(
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    fornecedor VARCHAR(100) NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL
);
```

## Conexão com o Banco de Dados (conexao.php):

```php
<?php

$host = "192.168.10.96";
$usuario = "postgres";
$senha = "1524";
$banco = "almoxarifado";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=5432;dbname=$banco",
        $usuario,
        $senha
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        "Mensagem" => "Erro na conexão: " . $e->getMessage()
    ]);
    exit;
}
```

## Estrutura da API (pecas.php):

```php
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
```

## Simulação de Inserção via POST (12 Peças):

```text
[
  {
    "nome": "Máquina",
    "categoria": "mecanica",
    "fornecedor": "Mecanica",
    "quantidade": 9,
    "preco_unitario": 199.99
  },
  {
    "nome": "Painel",
    "categoria": "eletrica",
    "fornecedor": "Soluções e Energia",
    "quantidade": 5,
    "preco_unitario": 1299.00
  },
  {
    "nome": "Bomba Hidráulica",
    "categoria": "hidraulica",
    "fornecedor": "HidroTech",
    "quantidade": 12,
    "preco_unitario": 450.00
  },
  {
    "nome": "Motor Elétrico",
    "categoria": "eletrica",
    "fornecedor": "EletroMais",
    "quantidade": 7,
    "preco_unitario": 899.90
  },
  {
    "nome": "Engrenagem",
    "categoria": "mecanica",
    "fornecedor": "MecParts",
    "quantidade": 20,
    "preco_unitario": 75.50
  },
  {
    "nome": "Válvula de Pressão",
    "categoria": "hidraulica",
    "fornecedor": "HidroFlex",
    "quantidade": 15,
    "preco_unitario": 185.75
  },
  {
    "nome": "Disjuntor",
    "categoria": "eletrica",
    "fornecedor": "Soluções Elétricas",
    "quantidade": 30,
    "preco_unitario": 68.90
  },
  {
    "nome": "Correia Industrial",
    "categoria": "mecanica",
    "fornecedor": "Mecanica Forte",
    "quantidade": 18,
    "preco_unitario": 120.00
  },
  {
    "nome": "Tubo de PVC",
    "categoria": "hidraulica",
    "fornecedor": "HidroMateriais",
    "quantidade": 40,
    "preco_unitario": 32.50
  },
  {
    "nome": "Fusível",
    "categoria": "eletrica",
    "fornecedor": "EletroCenter",
    "quantidade": 50,
    "preco_unitario": 15.00
  },
  {
    "nome": "Rolamento",
    "categoria": "mecanica",
    "fornecedor": "Rolamais",
    "quantidade": 25,
    "preco_unitario": 89.90
  },
  {
    "nome": "Mangueira Hidráulica",
    "categoria": "hidraulica",
    "fornecedor": "HidroParts",
    "quantidade": 22,
    "preco_unitario": 210.00
  }
]
```

## Consultas SQL para Tomada de Decisão:

### 1. Quantas unidades de peças existem no almoxarifado, somando todas?
```sql
SELECT SUM(quantidade) AS total_unidades FROM pecas;
```

### 2. Qual é o valor total do estoque, em reais?
```sql
SELECT SUM(quantidade * preco_unitario) AS valor_total_estoque FROM pecas;
```

### 3. Qual é o preço unitário da peça mais cara?
```sql
SELECT MAX(preco_unitario) AS peca_mais_cara FROM pecas;
```

### 4. Qual é o preço unitário da peça mais barata?
```sql
SELECT MIN(preco_unitario) AS peca_mais_barata FROM pecas;
```

### 5. Qual é o preço médio das peças, com duas casas decimais?
```sql
SELECT ROUND(CAST(AVG(preco_unitario) AS NUMERIC), 2) AS preco_medio FROM pecas;
```

### 6. Qual é o valor total em estoque só das peças da categoria eletrica?
```sql
SELECT SUM(quantidade * preco_unitario) AS valor_total_eletrica 
FROM pecas 
WHERE categoria = 'eletrica';
```