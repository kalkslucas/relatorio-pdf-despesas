<?php

ob_start();

  include 'config.php';
  require_once 'vendor/autoload.php';

  //Receber da URL o termo usado para pesquisar
  $pesquisa = filter_input(INPUT_GET, 'pesquisa', FILTER_DEFAULT);
  $nome = "%$pesquisa%";

  $sql = "SELECT *
                FROM contas
                ORDER BY id ASC";
  $query = $pdo->prepare($sql);
  $query->execute();

  $sqlMontanteAluguel = "SELECT SUM(VALOR) AS MONTANTE FROM contas WHERE TIPO = 'ALUGUEL'";
  $queryMontanteAluguel = $pdo->prepare($sqlMontanteAluguel);
  $queryMontanteAluguel->execute();
  $linhaMontanteAluguel = $queryMontanteAluguel->fetch(PDO::FETCH_ASSOC);
  extract($linhaMontanteAluguel);

  $sqlPagoAluguel = "SELECT SUM(VALOR) AS PAGO FROM contas WHERE TIPO = 'ALUGUEL' AND SITUACAO = 1";
  $queryPagoAluguel = $pdo->prepare($sqlPagoAluguel);
  $queryPagoAluguel->execute();
  $linhaPagoAluguel = $queryPagoAluguel->fetch(PDO::FETCH_ASSOC);
  extract($linhaPagoAluguel);

  $sqlDevedorAluguel = "SELECT SUM(VALOR) AS SALDO_DEVEDOR FROM contas WHERE TIPO = 'ALUGUEL' AND SITUACAO = 0";
  $queryDevedorAluguel = $pdo->prepare($sqlDevedorAluguel);
  $queryDevedorAluguel->execute();
  $linhaDevedorAluguel = $queryDevedorAluguel->fetch(PDO::FETCH_ASSOC);
  extract($linhaDevedorAluguel);


  use Dompdf\Dompdf;
  use Dompdf\Options;
  //Definindo fonte no ato de inicialização
  $options = new Options();
  $options->set('defaultFont', 'Helvetica');
  $options->set('isRemoteEnabled', true);

  //Inicializando a aplicação de PDF com a configuração de fonte
  $dompdf = new Dompdf($options);
  
  $html = "<!DOCTYPE html>
            <html lang='pt-br'>
            <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Gerar PDF</title>
              <link rel='stylesheet' href='http://localhost:81/relatorioPdf/css/estilo.css'></link>
            </head>
            <body>
              <div>
                <p>Aluguel Total: $MONTANTE</p>
                <p>Total Pago: $PAGO</p>
                <p>Saldo Devedor: $SALDO_DEVEDOR</p>
              </div>";

  if($query){
    if($linha = $query->rowCount() > 0){
      $html .= "<div class='container-tabela'>
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
      while($linha = $query->fetch(PDO::FETCH_ASSOC)){
        $html .= "<tr>
                    <td>$linha[id]</td>
                    <td>$linha[TIPO]</td>
                    <td>$linha[VALOR]</td>
                    <td>$linha[PARCELA]</td>
                    <td>$linha[VENCIMENTO]</td>
                    <td>$linha[SITUACAO]</td>
                    <td>$linha[DATA_PAGAMENTO]</td>
                  </tr>";
      }
    } else {
      $html .= "<tr>
                  <td colspan='5'>Nenhum dado encontrado</td>
                </tr>";
    }
    $html .= "</tbody>
            </table>
          </div>
        </body>
      </html>";
  }

  //Inserindo o conteúdo a ser impresso
  $dompdf ->loadHtml($html);
  
  //Definindo papel e orientação
  $dompdf->setPaper('A4', 'portrait');

  //Renderiza o HTML em PDF
  $dompdf->render();

  //Gera o arquivo PDF
  $dompdf->stream();


