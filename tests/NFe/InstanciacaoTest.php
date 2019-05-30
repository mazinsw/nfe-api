<?php
namespace NFe;

class InstanciacaoTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testNewEntity()
    {
        $responsavel = new \NFe\Entity\Responsavel();
        $destinatario = new \NFe\Entity\Destinatario();
        $emitente = new \NFe\Entity\Emitente();
        $endereco = new \NFe\Entity\Endereco();
        $estado = new \NFe\Entity\Estado();
        $lacre = new \NFe\Entity\Lacre();
        $municipio = new \NFe\Entity\Municipio();
        $pagamento = new \NFe\Entity\Pagamento();
        $pais = new \NFe\Entity\Pais();
        $peso = new \NFe\Entity\Peso();
        $produto = new \NFe\Entity\Produto();
        $transporte = new \NFe\Entity\Transporte();
        $volume = new \NFe\Entity\Volume();
    }

    public function testNewEntityImposto()
    {
        /* COFINS */
        $cofins_aliquota = new \NFe\Entity\Imposto\COFINS\Aliquota();
        $cofins_generico = new \NFe\Entity\Imposto\COFINS\Generico();
        $cofins_isento = new \NFe\Entity\Imposto\COFINS\Isento();
        $cofins_quantidade = new \NFe\Entity\Imposto\COFINS\Quantidade();
        /* COFINSST */
        $cofins_aliquota = new \NFe\Entity\Imposto\COFINS\ST\Aliquota();
        $cofins_quantidade = new \NFe\Entity\Imposto\COFINS\ST\Quantidade();
        /* ICMS */
        $icms_cobrado = new \NFe\Entity\Imposto\ICMS\Cobrado();
        $icms_cobranca = new \NFe\Entity\Imposto\ICMS\Cobranca();
        $icms_diferido = new \NFe\Entity\Imposto\ICMS\Diferido();
        $icms_generico = new \NFe\Entity\Imposto\ICMS\Generico();
        $icms_integral = new \NFe\Entity\Imposto\ICMS\Integral();
        $icms_isento = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_mista = new \NFe\Entity\Imposto\ICMS\Mista();
        $icms_parcial = new \NFe\Entity\Imposto\ICMS\Parcial();
        $icms_partilha = new \NFe\Entity\Imposto\ICMS\Partilha();
        $icms_reducao = new \NFe\Entity\Imposto\ICMS\Reducao();
        $icms_substituto = new \NFe\Entity\Imposto\ICMS\Substituto();
        /* Simples Nacional */
        $icms_simples_cobrado = new \NFe\Entity\Imposto\ICMS\Simples\Cobrado();
        $icms_simples_cobranca = new \NFe\Entity\Imposto\ICMS\Simples\Cobranca();
        $icms_simples_generico = new \NFe\Entity\Imposto\ICMS\Simples\Generico();
        $icms_simples_isento = new \NFe\Entity\Imposto\ICMS\Simples\Isento();
        $icms_simples_normal = new \NFe\Entity\Imposto\ICMS\Simples\Normal();
        $icms_simples_parcial = new \NFe\Entity\Imposto\ICMS\Simples\Parcial();
        /* IPI */
        $ipi_aliquota = new \NFe\Entity\Imposto\IPI\Aliquota();
        $ipi_isento = new \NFe\Entity\Imposto\IPI\Isento();
        $ipi_quantidade = new \NFe\Entity\Imposto\IPI\Quantidade();
        /* PIS */
        $pis_aliquota = new \NFe\Entity\Imposto\PIS\Aliquota();
        $pis_quantidade = new \NFe\Entity\Imposto\PIS\Quantidade();
        $pis_isento = new \NFe\Entity\Imposto\PIS\Isento();
        $pis_generico = new \NFe\Entity\Imposto\PIS\Generico();
        /* PISST */
        $pisst_aliquota = new \NFe\Entity\Imposto\PIS\ST\Aliquota();
        $pisst_quantidade = new \NFe\Entity\Imposto\PIS\ST\Quantidade();
        /* II */
        $ii = new \NFe\Entity\Imposto\II();
        /* IPI */
        $ipi = new \NFe\Entity\Imposto\IPI();
        /* Total */
        $ipi = new \NFe\Entity\Imposto\Total();
    }

    public function testNewEntityTransporte()
    {
        $transportador = new \NFe\Entity\Transporte\Transportador();
        $tributo = new \NFe\Entity\Transporte\Tributo();
        $veiculo = new \NFe\Entity\Transporte\Veiculo();
    }

    public function testNewCommon()
    {
        $ajuste = new \NFe\Common\Ajuste();
        $configuracao = new \NFe\Common\Configuracao();
        $curl_soap = new \NFe\Common\CurlSoap();
        $util = new \NFe\Common\Util();
    }

    public function testNewDatabase()
    {
        $estatico = new \NFe\Database\Estatico();
        $ibpt = new \NFe\Database\IBPT();
    }

    public function testNewCore()
    {
        $nfce = new \NFe\Core\NFCe();
        // $nfe = new \NFe\Core\NFe(); // abstract
        // $nfse = new \NFe\Core\NFSe(); // abstract
        $sefaz = new \NFe\Core\SEFAZ();
    }

    public function testNewTask()
    {
        $autorizacao = new \NFe\Task\Autorizacao();
        $envio = new \NFe\Task\Envio();
        $evento = new \NFe\Task\Evento();
        $inutilizacao = new \NFe\Task\Inutilizacao();
        $protocolo = new \NFe\Task\Protocolo();
        $recibo = new \NFe\Task\Recibo();
        $retorno = new \NFe\Task\Retorno();
        $situacao = new \NFe\Task\Situacao();
        $status = new \NFe\Task\Status();
        $tarefa = new \NFe\Task\Tarefa();
    }

    public function testLog()
    {
        $log = new \NFe\Logger\Log();
    }

    public function testException()
    {
        $validation_exception = new \NFe\Exception\ValidationException();
        $validation_exception->getErrors();
    }

    public function testOthersUtil()
    {
        $xml_sec_enc = new \RobRichards\XMLSecLibs\XMLSecEnc();
        $xml_seclibs_adapter = new \FR3D\XmlDSig\Adapter\XmlseclibsAdapter();
        $xml_security_d_sig = new \RobRichards\XMLSecLibs\XMLSecurityDSig();
        $xml_security_key = new \RobRichards\XMLSecLibs\XMLSecurityKey(
            \RobRichards\XMLSecLibs\XMLSecurityKey::TRIPLEDES_CBC
        );

        $curl = new \Curl\Curl();
        // $multi_curl = new \Curl\MultiCurl(); // error code -1073741819
        $case_insensitive_array = new \Curl\CaseInsensitiveArray();
    }
}
