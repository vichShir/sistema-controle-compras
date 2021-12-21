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
        $db = new Database();
        $all_pessoas = $db->getAllRowsFromQuery("SELECT p.codpessoa, p.nome, j.nomefantasia
                                                FROM pessoa p FULL JOIN pessoa_juridica j
                                                    ON p.codpessoa = j.codpessoa
                                                    FULL JOIN pessoa_fisica f
                                                    ON p.codpessoa = f.codpessoa");
        $db->close();
        
		$output =  "
			<h3>Nota Fiscal</h3>
            <p class='form-input'>Data (*)</p>
            <input type='datetime-local' name='nf_data' required>
            <p class='form-input'>Desconto (*)</p>
            <input type='number' name='nf_desconto' placeholder='R$000,00' min='0.00' max='999.999' step='0.01' required>
            <p class='form-input'>Pessoa associada (*)</p>
            <select name='pessoa_associada' required>
                <option value='novo-pj'>CADASTRAR NOVA PESSOA JURIDICA</option>
                <option value='novo-pf'>CADASTRAR NOVA PESSOA FISICA</option>";

        foreach ($all_pessoas as $pessoa)
        {
            $nome = empty($pessoa['nomefantasia']) === false ? $pessoa['nomefantasia'] : $pessoa['nome'];
            $output .= "<option value='" . $pessoa['codpessoa'] . "'>" . $nome . "</option>";
        }

        $output .= "</select>" .
            "<p class='form-input'>Utilizar o mesmo endereço da pessoa cadastrada na nota? (*)</p>
            <div class='radio-option' id='radio-endereco-sim'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='sim' required><p>Sim</p></div>
            <div class='radio-option' id='radio-endereco-nao'><input type='radio' class='radio-input' name='msmendereco' onclick='' value='nao'><p>Não</p></div>" .
            "<div id='form-append'></div>";
        return $output;
	}

    public static function form_endereco()
    {
        return "
            <h3>Endereço</h3>
            <p class='form-input'>Estado (*)</p>
            <input type='text' name='ps_estado' placeholder='SP' size='2' maxlength='2' required>
            <p class='form-input'>Município (*)</p>
            <input type='text' name='ps_municipio' placeholder='São Paulo' size='20' maxlength='20' required>
            <p class='form-input'>Bairro (*)</p>
            <input type='text' name='ps_bairro' placeholder='Centro' size='30' maxlength='30' required>
            <p class='form-input'>Logradouro (*)</p>
            <input type='text' name='ps_logradouro' placeholder='Av. Paulista, 123' size='40' maxlength='40' required>";
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
              <option value='UN'>UNITÁRIO</option>
              <option value='KG'>KG</option>
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
            <select id='ft_pagamento' name='ft_pagamento' required>
              <option value='DB'>DÉBITO</option>
              <option value='CR'>CRÉDITO</option>
              <option value='VS'>À VISTA</option>
              <option value='PX'>PIX</option>
            </select>
            <div id='ft-cartao'></div>
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

    public static function show_cartao()
    {
        return "
            <h3>Cartão</h3>
            <p class='form-input'>Últimos 4 dígitos do cartão (*)</p>
            <input type='text' name='ct_digitos' placeholder='1234' size='4' minlength='4' maxlength='4' required>
            <p class='form-input'>Bandeira (*)</p>
            <input type='text' name='ct_bandeira' placeholder='Visa Electron' size='16' maxlength='16' required>
            <p class='form-input'>Banco emissor do cartão (*)</p>
            <input type='text' name='ct_banco' placeholder='Bradesco' size='30' maxlength='30' required>
            <p class='form-input'>Data de Validade (*)</p>
            <input type='date' name='ct_dtvalidade' required>";
    }
}

class Cartao
{
    public $lastdigits;
    public $bandeira;
    public $bancoemissor;
    public $dtvalidade;

    function __construct($lastdigits, $bandeira, $bancoemissor, $dtvalidade)
    {
        $this->lastdigits = $lastdigits;
        $this->bandeira = $bandeira;
        $this->bancoemissor = $bancoemissor;
        $this->dtvalidade = $dtvalidade;
    }
}

class NotaFiscal
{
    public $data;
    public $desconto;
    public $endereco;
    public $codpessoa;
    public $msmendereco;

    function __construct($data, $desconto, $estado, $municipio, $bairro, $logradouro, $msmendereco)
    {
        $this->data = $data;
        $this->desconto = $desconto;
        $this->msmendereco = $msmendereco;
        $this->setEndereco($estado, $municipio, $bairro, $logradouro);
    }

    private function setEndereco($estado, $municipio, $bairro, $logradouro)
    {
        $this->endereco = new Endereco($estado, $municipio, $bairro, $logradouro);
    }

    public function set_codpessoa($codpessoa)
    {
        $this->codpessoa = $codpessoa;
    }
}

class PessoaJuridica
{
    public $codpessoa;
    public $nome;
    public $nomefantasia;
    public $cnpj;
    public $endereco;

    function __construct($codpessoa, $nome, $nomefantasia, $cnpj, $estado, $municipio, $bairro, $logradouro)
    {
        $this->codpessoa = $codpessoa;
        $this->nome = $nome;
        $this->nomefantasia = $nomefantasia;
        $this->cnpj = $cnpj;
        $this->setEndereco($estado, $municipio, $bairro, $logradouro);
    }

    private function setEndereco($estado, $municipio, $bairro, $logradouro)
    {
        $this->endereco = new Endereco($estado, $municipio, $bairro, $logradouro);
    }
}

class PessoaFisica
{
    public $codpessoa;
    public $nome;
    public $cpf;
    public $email;
    public $telefone;
    public $estado;
    public $municipio;
    public $bairro;
    public $logradouro;

    function __construct($codpessoa, $nome, $cpf, $email, $telefone, $estado, $municipio, $bairro, $logradouro)
    {
        $this->codpessoa = $codpessoa;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->estado = $estado;
        $this->municipio = $municipio;
        $this->bairro = $bairro;
        $this->logradouro = $logradouro;
    }
}

class Endereco
{
    private $estado;
    private $municipio;
    private $bairro;
    private $logradouro;

    function __construct($estado, $municipio, $bairro, $logradouro)
    {
        $this->estado = $estado;
        $this->municipio = $municipio;
        $this->bairro = $bairro;
        $this->logradouro = $logradouro;
    }

    public function setEstado($estado) { $this->estado = $estado; }
    public function setMunicipio($municipio) { $this->municipio = $municipio; }
    public function setBairro($bairro) { $this->bairro = $bairro; }
    public function setLogradouro($logradouro) { $this->logradouro = $logradouro; }

    public function getEstado() { return $this->estado; }
    public function getMunicipio() { return $this->municipio; }
    public function getBairro() { return $this->bairro; }
    public function getLogradouro() { return $this->logradouro; }
}

class ItemNotaFiscal
{
    public $cod;
    public $unidade;
    public $quantidade;
    public $desconto;
    public $descricao;
    public $valorunitario;

    function __construct($cod, $unidade, $quantidade, $desconto, $descricao, $valorunitario)
    {
        $this->cod = $cod;
        $this->unidade = $unidade;
        $this->quantidade = $quantidade;
        $this->desconto = $desconto;
        $this->descricao = $descricao;
        $this->valorunitario = $valorunitario;
    }
}

class Fatura
{
    public $ft_pagamento;
    public $ft_dtvencimento;
    public $ft_dtpagamento;
    public $ft_valor;
    public $ft_cartao;

    function __construct($ft_pagamento, $ft_dtvencimento, $ft_dtpagamento, $ft_valor, $ft_cartao)
    {
        $this->ft_pagamento = $ft_pagamento;
        $this->ft_dtvencimento = $ft_dtvencimento;
        $this->ft_dtpagamento = $ft_dtpagamento;
        $this->ft_valor = $ft_valor;
        $this->ft_cartao = $ft_cartao;
    }
}

?>