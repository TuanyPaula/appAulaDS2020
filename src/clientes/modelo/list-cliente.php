<?php

include('../../banco/conexao.php');

if($conexao){
    $requestData = $_REQUEST;

    $colunas = $requestData['columns'];

    $sql = "SELECT idcliente, nome, ativo, email, telefone, datamodificacao FROM 
    clientes WHERE 1=1";
    $resultado = mysqli_query($conexao, $sql);
    $qtddeLinhas = mysqli_num_rows($resultado);

    if(!empty($requestData['search']['value'])){

        $sql .= "AND (IDCLIENTE LIKE '$requestData[search][value]
        % OR NOME LIKE 'C%')";
        $sql .= " OR NOME LIKE' $requestData[seache][value]%";
    }

    $resultado = mysqli_query($conexao, $sql);
    $totalFiltrados = mysqli_num_rows($resultado);

    $colunaOrdem = $requestData['order'] [0]['column'];
    $ordem = $colunas[$colunaOrdem]['data'];
    $direcao = $requestData['order'][0]['dir'];

    $sql .="ORDER BY $ordem $direcao LIMIT {$requestData['start']}, 
    {$requestData['length']}";

    $resultado = mysqli_query($conexao, $sql);

    $dados = array();
    while($linha = mysqli_fetch_assoc($resultado)){
        $dados[] = array_map('utf8_encode', $linha);
    }

    $json_data = array(
        "draw" => intval($requestData['draw']),
        "recordsTotal" => intval($qtdeLinhas),
        "recordsFiltered" => intval($totalFiltrados),
        "data" => $dados
    );

    mysqli_close($conexao);

}else{
    $json_data = array(
       "draw" => 0,
       "recordsTotal" => 0,
       "recordsFiltered" => 0,
       "data" => array()
    );
}

echo json_encode($json_data);