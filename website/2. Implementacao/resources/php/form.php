<?php
class Form
{
    public static $current_step = 0;

    function __construct()
    {
        
    }

    public function registrar_condicao($post_name, $func)
    {
        if(isset($_POST[$post_name]))
        {
            $func();
        }
    }

    public function registrar_sessao($step, $func)
    {
        if(Form::$current_step === $step)
        {
            $func();
        }
    }

	public static function form_notafiscal()
	{
		return "
			<h3>Nota Fiscal</h3>
            <p class='form-input'>Data (*)</p>
            <input type='date' name='nf_data' required>
            <p class='form-input'>Desconto (*)</p>
            <input type='number' name='nf_desconto' placeholder='R$000,00' min='0.00' max='999.999' step='0.01' required>
            <p class='form-input'>Pessoa associada (*)</p>
            <select name='pessoa_associada' required>
                <option value='novo-pj'>CADASTRAR NOVA PESSOA JURIDICA</option>
                <option value='novo-pf'>CADASTRAR NOVA PESSOA FISICA</option>
                <option value='enxuto'>ENXUTO</option>
                <option value='santa-rita'>SANTA RITA</option>
            </select>" .
            "<p class='form-input'>Utilizar o mesmo endereço da pessoa cadastrada na nota? (*)</p>
            <div class='radio-option' id='radio-endereco-sim'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='sim' required><p>Sim</p></div>
            <div class='radio-option' id='radio-endereco-nao'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='nao'><p>Não</p></div>" .
            "<div id='form-append'></div>";
	}

    public static function form_endereco()
    {
        return "
            <h3>Endereço</h3>
            <p class='form-input'>Estado (*)</p>
            <input type='text' name='estado' placeholder='SP' size='2' maxlength='2' required>
            <p class='form-input'>Município (*)</p>
            <input type='text' name='municipio' placeholder='São Paulo' size='20' maxlength='20' required>
            <p class='form-input'>Bairro (*)</p>
            <input type='text' name='bairro' placeholder='Centro' size='30' maxlength='30' required>
            <p class='form-input'>Logradouro (*)</p>
            <input type='text' name='logradouro' placeholder='Av. Paulista, 123' size='40' maxlength='40' required>";
    }

	public static function form_pessoajuridica()
	{
		return "
			<h3>Pessoa Jurídica</h3>
            <p class='form-input'>Nome Jurídico (*)</p>
            <input type='text' name='pj_nome' placeholder='Nome' size='60' maxlength='60' required>
            <p class='form-input'>Nome Fantasia</p>
            <input type='text' name='pj_nomefantasia' placeholder='Nome Fantasia' size='50' maxlength='50'>
            <p class='form-input'>CNPJ (*)</p>
            <input type='text' name='pj_cnpj' placeholder='00000000000000' pattern='[0-9]{3}[0-9]{3}[0-9]{3}[0-9]{5}' size='14' maxlength='14' required>";
	}

    public static function form_pessoafisica()
    {

    }

    public static function show_itemnotafiscal()
    {
        return "
            <h3>Item da Nota Fiscal</h3>
            <p class='form-input'>Código (*)</p>
            <input type='text' name='inf_cod' placeholder='0000000000000000' size='16' maxlength='16' required>
            <p class='form-input'>Unidade (*)</p>
            <select name='inf_unidade' required>
              <option value='unitario'>UNITÁRIO</option>
              <option value='quilograma'>KG</option>
            </select>
            <p class='form-input'>Quantidade (*)</p>
            <input type='number' name='inf_quantidade' placeholder='000,000' min='0.000' max='999.999' step='0.001' required>
            <p class='form-input'>Desconto (*)</p>
            <input type='number' name='inf_desconto' placeholder='R$000,00' min='0.00' max='999.999' step='0.01' required>
            <p class='form-input'>Descrição (*)</p>
            <input type='text' name='inf_descricao' placeholder='Aveia' size='100' maxlength='100' required>
            <p class='form-input'>Valor Unitário (*)</p>
            <input type='number' name='inf_valorunitario' placeholder='R$00000,00' min='0.00' max='99999.99' step='0.01' required>" .
            "<p class='form-input'>Há mais itens? (*)</p>
            <div class='radio-option'><input type='radio' class='radio-input' name='itemnotafiscal' onclick='' value='sim' required><p>Sim</p></div>
            <div class='radio-option'><input type='radio' class='radio-input' name='itemnotafiscal' onclick='' value='nao'><p>Não</p></div>";
    }

    public static function show_fatura()
    {
        return "
            <h3>Fatura</h3>
            <p class='form-input'>Forma de Pagamento (*)</p>
            <select name='ft_pagamento' required>
              <option value='unitario'>DÉBITO</option>
              <option value='quilograma'>CRÉDITO</option>
              <option value='quilograma'>À VISTA</option>
              <option value='quilograma'>PIX</option>
            </select>
            <p class='form-input'>Data de Vencimento (*)</p>
            <input type='date' name='ft_dtvencimento' required>
            <p class='form-input'>Data de Pagamento</p>
            <input type='date' name='ft_dtpagamento'>
            <p class='form-input'>Valor (*)</p>
            <input type='number' name='ft_valor' placeholder='R$000000,00' min='0.00' max='999999.99' step='0.01' required>" .
            "<p class='form-input'>Há mais faturas? (*)</p>
            <div class='radio-option'><input type='radio' class='radio-input' name='maisfaturas' onclick='' value='sim' required><p>Sim</p></div>
            <div class='radio-option'><input type='radio' class='radio-input' name='maisfaturas' onclick='' value='nao'><p>Não</p></div>";
    }
}
?>