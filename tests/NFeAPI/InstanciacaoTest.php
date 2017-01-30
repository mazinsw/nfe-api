<?php

class InstanciacaoTest extends PHPUnit_Framework_TestCase
{
    protected function setUp() {}

    public function testNewClasses()
    {
        $cliente = new Cliente();
        $emitente = new Emitente();
        $endereco = new Endereco();
        $estado = new Estado();
        $lacre = new Lacre();
        $municipio = new Municipio();
        $pagamento = new Pagamento();
        $pais = new Pais();
        $peso = new Peso();
        $produto = new Produto();
        $transporte = new Transporte();
        $volume = new Volume();
    }

    public function testNewImposto()
    {
        /* COFINS */
        $cofins_aliquota = new Imposto\COFINS\Aliquota();
        $cofins_generico = new Imposto\COFINS\Generico();
        $cofins_isento = new Imposto\COFINS\Isento();
        $cofins_quantidade = new Imposto\COFINS\Quantidade();
        /* COFINSST */
        $cofins_aliquota = new Imposto\COFINSST\Aliquota();
        $cofins_quantidade = new Imposto\COFINSST\Quantidade();
        /* ICMS */
        $icms_cobrado = new Imposto\ICMS\Cobrado();
        $icms_cobranca = new Imposto\ICMS\Cobranca();
        $icms_diferido = new Imposto\ICMS\Diferido();
        $icms_generico = new Imposto\ICMS\Generico();
        $icms_integral = new Imposto\ICMS\Integral();
        $icms_isento = new Imposto\ICMS\Isento();
        $icms_mista = new Imposto\ICMS\Mista();
        $icms_parcial = new Imposto\ICMS\Parcial();
        $icms_partilha = new Imposto\ICMS\Partilha();
        $icms_reducao = new Imposto\ICMS\Reducao();
        $icms_substituto = new Imposto\ICMS\Substituto();
        /* Simples Nacional */
        $icms_simples_cobrado = new Imposto\ICMS\Simples\Cobrado();
        $icms_simples_cobranca = new Imposto\ICMS\Simples\Cobranca();
        $icms_simples_generico = new Imposto\ICMS\Simples\Generico();
        $icms_simples_isento = new Imposto\ICMS\Simples\Isento();
        $icms_simples_normal = new Imposto\ICMS\Simples\Normal();
        $icms_simples_parcial = new Imposto\ICMS\Simples\Parcial();
        /* IPI */
        $ipi_aliquota = new Imposto\IPI\Aliquota();
        $ipi_isento = new Imposto\IPI\Isento();
        $ipi_quantidade = new Imposto\IPI\Quantidade();
        /* PIS */
        $pis_aliquota = new Imposto\PIS\Aliquota();
        $pis_quantidade = new Imposto\PIS\Quantidade();
        $pis_isento = new Imposto\PIS\Isento();
        $pis_generico = new Imposto\PIS\Generico();
        /* PISST */
        $pisst_aliquota = new Imposto\PISST\Aliquota();
        $pisst_quantidade = new Imposto\PISST\Quantidade();
        /* II */
        $ii = new Imposto\II();
        /* IPI */
        $ipi = new Imposto\IPI();
        /* Total */
        $ipi = new Imposto\Total();
    }

    public function testNewTransporte()
    {
        $transportador = new Transporte\Transportador();
        $tributo = new Transporte\Tributo();
        $veiculo = new Transporte\Veiculo();
    }

    public function testNewCommon()
    {
        $ajuste = new Ajuste();
        $configuracao = new Configuracao();

        $estatico = new BD\Estatico();
    }

    public function testNewLibrary()
    {
        $ibpt = new IBPT();
        $nfce = new NFCe();
        $nfe = new NFe();
        $nfse = new NFSe();
        $sefaz = new SEFAZ();

        $autorizacao = new NF\Autorizacao();
        $envio = new NF\Envio();
        $evento = new NF\Evento();
        $inutilizacao = new NF\Inutilizacao();
        $protocolo = new NF\Protocolo();
        $recibo = new NF\Recibo();
        $retorno = new NF\Retorno();
        $situacao = new NF\Situacao();
        $status = new NF\Status();
        $tarefa = new NF\Tarefa();
    }

    public function testUtil()
    {
        $curl_soap = new CurlSoap();
        $log = new Log();
        $util = new Util();
        $validation_exception = new ValidationException();
        $xml_sec_enc = new RobRichards\XMLSecLibs\XMLSecEnc();
        $xml_seclibs_adapter = new FR3D\XmlDSig\Adapter\XmlseclibsAdapter();
        $xml_security_d_sig = new RobRichards\XMLSecLibs\XMLSecurityDSig();
        $xml_security_key = new RobRichards\XMLSecLibs\XMLSecurityKey(RobRichards\XMLSecLibs\XMLSecurityKey::TRIPLEDES_CBC);

        $curl = new Curl\Curl();
        // $multi_curl = new Curl\MultiCurl(); // error code -1073741819
        $case_insensitive_array = new Curl\CaseInsensitiveArray();
    }

}
