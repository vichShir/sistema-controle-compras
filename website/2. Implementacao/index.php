<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Cadastrar Nota Fiscal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="resources/css/main-style.css" rel="stylesheet" type="text/css"/>
    <link href="resources/css/form-style.css" rel="stylesheet" type="text/css"/>
    <link href="resources/css/footer-style.css" rel="stylesheet" type="text/css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </head>

  <body>

    <!-- Cabeçalho -->
    <header style="height: 90px"><h1>Sistema de Controle de Compras</h1></header>

    <!-- Formulário de Login -->
    <section class="sec-panel sec-form">
        <h2>Cadastrar Nota Fiscal</h2>
        <hr>
        
        <form name="formulario" action="index.php" method="POST">
            <?php
                require("resources/php/database.php");
                require("resources/php/form.php");

                $form = new Form();
                Form::$current_step = 0;

                if(isset($_SESSION['enviado']) === 1)
                {
                    session_unset();
                }

                /* CONDICOES */
                $form->registrar_condicao('pessoa_associada', function() {
                    if($_POST['pessoa_associada'] === "novo-pj")
                    {
                        Form::$current_step = 1;
                    }
                    else
                    {
                        Form::$current_step = 2;
                    }
                });

                $form->registrar_condicao('pj_nome', function() {
                    Form::$current_step = 2;
                });

                $form->registrar_condicao('inf_cod', function() {
                    if($_POST['itemnotafiscal'] === 'sim')
                    {
                        Form::$current_step = 2;
                    }
                    else
                    {
                        Form::$current_step = 3;
                    }
                });

                $form->registrar_condicao('ft_pagamento', function() {
                    if($_POST['maisfaturas'] === 'sim')
                    {
                        Form::$current_step = 3;
                    }
                    else
                    {
                        Form::$current_step = 4;
                        $_SESSION['submeter'] = 'sim';
                    }
                });

                /* SESSOES */
                $form->registrar_sessao(0, (function() {
                    echo Form::form_notafiscal();
                }));

                $form->registrar_sessao(1, (function() {
                    # Armazenar os valores de NF
                    try_to_store_nf();

                    if($_POST['pessoa_associada'] === "novo-pj")
                        echo Form::form_pessoajuridica();
                    else
                        echo Form::form_pessoafisica();

                    echo Form::form_endereco();
                }));

                $form->registrar_sessao(2, (function() {
                    # Armazenar os valores de NF
                    try_to_store_nf();

                    # Armazenar os valores de pessoa juridica
                    try_to_store_pj();

                    # Armazenar os valores do item de nota fiscal
                    try_to_store_inf();

                    echo Form::show_itemnotafiscal();
                }));

                $form->registrar_sessao(3, (function() {
                    # Armazenar os valores do item de nota fiscal
                    try_to_store_inf();

                    # Armazenar os valores de fatura
                    try_to_store_ft();

                    echo Form::show_fatura();
                }));

                $form->registrar_sessao(4, (function() {
                    # Armazenar os valores de fatura
                    try_to_store_ft();

                    $nota = unserialize($_SESSION['nota_fiscal']);
                    $pj = unserialize($_SESSION['pessoa_juridica']);
                    $all_items = $_SESSION['all_items'];
                    $all_faturas = $_SESSION['all_faturas'];
                    
                    # Print nota fiscal
                    print_nf($nota, $pj);

                    $db = new Database();

                    /*  Pegar:
                    codpessoa
                    numnota
                    codcartao
                    */

                    // Pessoa Juridica
                    exec_pj_stored_procedure($db, $pj->pj_nome, $pj->pj_cnpj, $pj->pj_nomefantasia, $pj->pj_estado, $pj->pj_municipio, $pj->pj_bairro, $pj->pj_logradouro);

                    // Nota Fiscal
                    $nota->set_codpessoa(1);
                    exec_nf_stored_procedure($db, $nota->nf_data, $nota->nf_desconto, '1', $nota->nf_estado, $nota->nf_municipio, $nota->nf_bairro, $nota->nf_logradouro);

                    // Fatura
                    foreach ($all_faturas as $fatura_un) 
                    {
                        $fatura = unserialize($fatura_un);
                        exec_ft_stored_procedure($db, $fatura->ft_dtvencimento, $fatura->ft_dtpagamento, $fatura->ft_valor, 1, $fatura->ft_pagamento, 1);
                    }

                    // Itens de nota fiscal
                    foreach ($all_items as $item_un) 
                    {
                        $item = unserialize($item_un);
                        exec_inf_stored_procedure($db, 1, 0, $item->unidade, $item->quantidade, $item->desconto, $item->cod, $item->descricao, $item->valorunitario);
                    }

                    #while($result = $sth->fetch(PDO::FETCH_ASSOC)) {
                    #    var_dump($result);
                    #}

                    $db->close();

                    $_SESSION['enviado'] = 1;
                }));

                function retrieve_array($index)
                {
                    return (isset($_SESSION[$index]) ? $_SESSION[$index] : array());
                }

                function array_push_object($array, $obj)
                {
                    return array_push($array, serialize($obj));
                }

                function is_empty($array)
                {
                    foreach($array as $field)
                    {
                        if($field === '')
                            return true;
                    }
                    return false;
                }

                function try_to_store_nf()
                {
                    # Armazenar os valores de NF
                    if(!isset($_SESSION['nota_fiscal']))
                    {
                        $nota = new NotaFiscal(
                            $_POST['nf_data'] ?? '',
                            $_POST['nf_desconto'] ?? '',
                            $_POST['nf_estado'] ?? '',
                            $_POST['nf_municipio'] ?? '',
                            $_POST['nf_bairro'] ?? '',
                            $_POST['nf_logradouro'] ?? ''
                        );
                        $_SESSION['nota_fiscal'] = serialize($nota);
                    }
                }

                function try_to_store_pj()
                {
                    if(!isset($_SESSION['pessoa_juridica']))
                    {
                        $pj = new PessoaJuridica(
                            $_POST['pj_nome'] ?? '',
                            $_POST['pj_nomefantasia'] ?? '',
                            $_POST['pj_cnpj'] ?? '',
                            $_POST['ps_estado'] ?? '',
                            $_POST['ps_municipio'] ?? '',
                            $_POST['ps_bairro'] ?? '',
                            $_POST['ps_logradouro'] ?? ''
                        );
                        $_SESSION['pessoa_juridica'] = serialize($pj);
                    }
                }

                function try_to_store_inf()
                {
                    $item = new ItemNotaFiscal(
                        $_POST['inf_cod'] ?? '',
                        $_POST['inf_unidade'] ?? '',
                        $_POST['inf_quantidade'] ?? '',
                        $_POST['inf_desconto'] ?? '',
                        $_POST['inf_descricao'] ?? '',
                        $_POST['inf_valorunitario'] ?? ''
                    );

                    if(!is_empty($item))
                    {
                        $all_items = retrieve_array('all_items');
                        array_push($all_items, serialize($item));
                        $_SESSION['all_items'] = $all_items;
                    }
                }

                function try_to_store_ft()
                {
                    $fatura = new Fatura(
                        $_POST['ft_pagamento'] ?? '',
                        $_POST['ft_dtvencimento'] ?? '',
                        $_POST['ft_dtpagamento'] ?? '',
                        $_POST['ft_valor'] ?? ''
                    );

                    if(isset($_POST['ft_pagamento']))
                    {
                        $all_faturas = retrieve_array('all_faturas');
                        array_push($all_faturas, serialize($fatura));
                        $_SESSION['all_faturas'] = $all_faturas;
                    }
                }

                function print_nf($nota, $pj)
                {
                    echo "<h3>" . $pj->pj_nome . "</h3>
                    <p>CNPJ: " . $pj->pj_cnpj . "</p>
                    <p>" . $pj->pj_logradouro . ", " . $pj->pj_bairro . ", " . $pj->pj_municipio . ", " . $pj->pj_estado . "</p>
                    <p>" . $nota->nf_data . "</p>
                    <table class='table table-dark table-striped table-hover'>
                        <thead>
                            <tr>
                                <th scope='col'>#</th>
                                <th scope='col'>Código</th>
                                <th scope='col'>Descrição</th>
                                <th scope='col'>Quantidade</th>
                                <th scope='col'>Unidade</th>
                                <th scope='col'>Valor Unitário</th>
                                <th scope='col'>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>";
                        $itens_total = array();
                        $itens_descontos = array();
                        for($i = 0; $i < sizeof($_SESSION['all_items']); $i++)
                        {
                            $item = $_SESSION['all_items'][$i];
                            $item_nf = unserialize($item);
                            $total = round($item_nf->quantidade * $item_nf->valorunitario, 2);
                            $desconto = $item_nf->desconto;
                            array_push($itens_total, $total);
                            array_push($itens_descontos, $desconto);
                            echo "<tr>";
                            echo "
                            <th scope='row'>" . ($i+1) . "</th>
                            <td>" . $item_nf->cod . "</td>
                            <td>" . $item_nf->descricao . "</td>
                            <td>" . $item_nf->quantidade . "</td>
                            <td>" . $item_nf->unidade . "</td>
                            <td>R$" . $item_nf->valorunitario . "</td>
                            <td>R$" . $total . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>
                    </table>";
                    
                    $itens_total = floatval(array_sum($itens_total));
                    $itens_descontos = floatval(array_sum($itens_descontos));
                    echo "<p>Qtd. total de itens: " . sizeof($_SESSION['all_items']) . "</p>";
                    echo "<p>Valor total R$: " . $itens_total . "</p>";
                    echo "<p>Descontos R$: " . $itens_descontos . "</p>";
                    echo "<p>Valor a pagar R$: " . ($itens_total - $itens_descontos) . "</p>";

                    echo "
                    <table class='table table-dark table-striped table-hover'>
                        <thead>
                            <tr>
                                <th scope='col'>#</th>
                                <th scope='col'>Forma de pagamento</th>
                                <th scope='col'>Data Vencimento</th>
                                <th scope='col'>Data Pagamento</th>
                                <th scope='col'>Valor</th>
                            </tr>
                        </thead>
                        <tbody>";
                        for($i = 0; $i < sizeof($_SESSION['all_faturas']); $i++)
                        {
                            $fatura_serialize = $_SESSION['all_faturas'][$i];
                            $fatura = unserialize($fatura_serialize);
                            echo "<tr>";
                            echo "
                            <th scope='row'>" . ($i+1) . "</th>
                            <td>" . $fatura->ft_pagamento . "</td>
                            <td>" . $fatura->ft_dtvencimento . "</td>
                            <td>" . $fatura->ft_dtpagamento . "</td>
                            <td>R$" . $fatura->ft_valor . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>
                    </table>";
                }

                function exec_pj_stored_procedure($db, $pj_nome, $pj_cnpj, $pj_nomefantasia, $pj_estado, $pj_municipio, $pj_bairro, $pj_logradouro)
                {
                    $sth = $db->conn->prepare("SET NOCOUNT ON; EXEC ins_pessoa_juridica ?, ?, ?, ?, ?, ?, ?;");
                    $sth->bindParam(1, $pj_nome);
                    $sth->bindParam(2, $pj_cnpj);
                    $sth->bindParam(3, $pj_nomefantasia);
                    $sth->bindParam(4, $pj_estado);
                    $sth->bindParam(5, $pj_municipio);
                    $sth->bindParam(6, $pj_bairro);
                    $sth->bindParam(7, $pj_logradouro);
                    $sth->execute();
                    $sth->nextRowset();
                }

                function exec_nf_stored_procedure($db, $nf_data, $nf_desconto, $codpessoa, $nf_estado, $nf_municipio, $nf_bairro, $nf_logradouro)
                {
                    $sth = $db->conn->prepare("SET NOCOUNT ON; EXEC ins_notafiscal ?, ?, ?, ?, ?, ?, ?, ?;");
                    $valortotal = 0;

                    $date = new DateTime($nf_data);
                    $date = $date->format('Y-m-d H:i:s');

                    $sth->bindParam(1, $valortotal);
                    $sth->bindParam(2, $date);
                    $sth->bindParam(3, $nf_desconto);
                    $sth->bindParam(4, $codpessoa);
                    $sth->bindParam(5, $nf_estado);
                    $sth->bindParam(6, $nf_municipio);
                    $sth->bindParam(7, $nf_bairro);
                    $sth->bindParam(8, $nf_logradouro);
                    $sth->execute();
                    $sth->nextRowset();
                }

                function exec_ft_stored_procedure($db, $dtvencimento, $dtpagamento, $valor, $numnota, $forma, $codcartao)
                {
                    $sth = $db->conn->prepare("SET NOCOUNT ON; EXEC ins_fatura ?, ?, ?, ?, ?, ?;");
                    $sth->bindParam(1, $dtvencimento);
                    $sth->bindParam(2, $dtpagamento);
                    $sth->bindParam(3, $valor);
                    $sth->bindParam(4, $numnota);
                    $sth->bindParam(5, $forma);
                    $sth->bindParam(6, $codcartao);
                    $sth->execute();
                    $sth->nextRowset();
                }

                function exec_inf_stored_procedure($db, $numnota, $valortotal, $unidademedida, $quantidade, $desconto, $codigo, $descricao, $valorunitario)
                {
                    $sth = $db->conn->prepare("SET NOCOUNT ON; EXEC ins_itemnotafiscal ?, ?, ?, ?, ?, ?, ?, ?;");
                    $sth->bindParam(1, $numnota);
                    $sth->bindParam(2, $valortotal);
                    $sth->bindParam(3, $unidademedida);
                    $sth->bindParam(4, $quantidade);
                    $sth->bindParam(5, $desconto);
                    $sth->bindParam(6, $codigo);
                    $sth->bindParam(7, $descricao);
                    $sth->bindParam(8, $valorunitario);
                    $sth->execute();
                    $sth->nextRowset();
                }
            ?>

            <!--h3>pj_nome</h3>
            <p>CNPJ: pj_cnpj</p>
            <p>pj_logradouro, pj_bairro, pj_municipio, pj_estado</p>

            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Código</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Quantidade</th>
                        <th scope="col">Unidade</th>
                        <th scope="col">Valor Unitário</th>
                        <th scope="col">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>cod</td>
                        <td>descricao</td>
                        <td>quantidade</td>
                        <td>unidade</td>
                        <td>valorunitario</td>
                        <td>quantidade * valorunitario</td>
                    </tr>
                </tbody>
            </table-->

            <script>
                $(document).ready(function() {
                    $("#radio-endereco-nao").on('change', function () { 
                        $('#form-append').append("<div class='form-endereco'>\
                        <h3>Endereço na Nota</h3>\
                        <p class='form-input'>Estado (*)</p>\
                        <input type='text' name='nf_estado' placeholder='SP' size='2' maxlength='2' required>\
                        <p class='form-input'>Município (*)</p>\
                        <input type='text' name='nf_municipio' placeholder='São Paulo' size='20' maxlength='20' required>\
                        <p class='form-input'>Bairro (*)</p>\
                        <input type='text' name='nf_bairro' placeholder='Centro' size='30' maxlength='30' required>\
                        <p class='form-input'>Logradouro (*)</p>\
                        <input type='text' name='nf_logradouro' placeholder='Av. Paulista, 123' size='40' maxlength='40' required></div>");  
                    });

                    $("#radio-endereco-sim").on('change', function () { 
                        $('.form-endereco').remove();  
                    });
                });
            </script>

            <p><input id='form-button' type='submit' value=<?php echo isset($_SESSION['submeter']) ? 'Enviar' : 'Próximo' ?>></p>
        </form>
    </section>

    <!-- Rodapé -->
    <footer>
        <!-- Rodapé principal -->
        <div class="ft-topics">
            <!-- About -->
            <section class="ft-about">
                <h3>SOBRE</h3>
                <p>Website para preenchimento manual do banco de dados das informações da nota fiscal.</p>
            </section>
            <!-- Devs -->
            <section class="ft-devs">
                <h3>DESENVOLVEDOR</h3>
                <ul>
                    <li>vichShir</li>
                </ul>
            </section>
        </div>
      
        <!-- Rodapé inferior -->
        <div class="ft-info">
            <p>SCC - Sistema de Controle de Compras - 2021</p>
        </div>
    </footer>

  </body>
</html>