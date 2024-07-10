<?php include_once 'config.php' ?>
<!DOCTYPE html>
<html lang="pt-br">
<st>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerar PDF com imagem</title>
  <link rel="stylesheet" href="style.css">
  <style>
    html {
      width: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
  </style>
</st>
<body>
  <h1>Gerar PDF com PHP</h1>

  <?php
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
  ?>
  <br><br>

  
  <form action="" method="post">
    <?php 
      $pesquisa = "";
      if(isset($dados['pesquisa'])){
        $pesquisa = $dados['pesquisa'];
      } 
    ?>

    <a href="gerarPdf.php">Gerar PDF</a>
    <br><br>

    <label>Pesquisar</label>
    <input type="text" name="pesquisa" id="pesquisa" placeholder="Pesquise o termo" value="<?=$pesquisa?>">
    <br><br>
    <input type="submit" value="Pesquisar" name="pesqUsuario">
  </form>
  
  <?php
    if(!empty($dados['pesqUsuario'])){
      $nome = "%$dados[pesquisa]%";
      $query = "SELECT *
                FROM contas
                WHERE TIPO LIKE :nome
                ORDER BY id ASC";
      $resQuery = $pdo->prepare($query);
      $resQuery->bindParam(':nome', $nome, PDO::PARAM_STR);
      $resQuery->execute();

      

      if($resQuery and $resQuery->rowCount() > 0){
        echo "<div style='margin-top: 10px;'>
                <table border='1'>
                  <thead>
                    <tr>
                      <td>ID</td>
                      <td>TIPO</td>
                      <td>VALOR</td>
                      <td>PARCELA</td>
                      <td>VENCIMENTO</td>
                      <td>SITUAÇÃO</td>
                      <td>DATA DE PAGAMENTO</td>
                    </tr>
                  </thead>
                  <tbody>";
        while($linha = $resQuery->fetch(PDO::FETCH_ASSOC)){
          extract($linha);
          echo "<tr>
                    <td>$id</td>
                    <td>$TIPO</td>
                    <td>$VALOR</td>
                    <td>$PARCELA</td>
                    <td>$VENCIMENTO</td>
                    <td>$SITUACAO</td>
                    <td>$DATA_PAGAMENTO</td>
                  </tr>";
        }
      } else {
        echo "<p>Erro: Nenhum resultado encontrado!</p>";
      }
      echo "</tbody>
          </table>
        </div>";
    }
  ?>

</body>
</html>