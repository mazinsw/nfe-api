<?php
namespace Example;

use NFe\Common\Ajuste;
use NFe\Common\Util;
use NFe\Core\SEFAZ;
use NFe\Entity\Emitente;

class Processador extends Ajuste
{
    /**
     * Inicializa o processamento das notas
     * @return self
     */
    public function init($info)
    {
        // informa à instância global que essa será a configuração usada
        SEFAZ::getInstance()->setConfiguracao($this);
        // informa que as notas serão obtidas desse gerador
        $this->setBanco(new Gerador());

        $storage = dirname(dirname(__DIR__)) . '/storage';
        // informa a pasta onde serão salvos os arquivos XML
        $pasta_xml = $storage . '/xml';
        Util::createDirectory($pasta_xml);
        $this->setPastaXmlBase($pasta_xml);
        // informa onde estão os arquivos do certificado
        $this->setArquivoChavePublica($storage . '/certs/public.pem');
        $this->setArquivoChavePrivada($storage . '/certs/private.pem');
        // informa o token e o CSC
        $this->setToken($info['contrib']['token']);
        $this->setCSC($info['contrib']['csc']);

        /* Informações do emitente */
        $this->getEmitente()->setRazaoSocial($info['emitente']['razao_social']);
        $this->getEmitente()->setFantasia($info['emitente']['fantasia']);
        $this->getEmitente()->setCNPJ($info['emitente']['cnpj']);
        $this->getEmitente()->setIE($info['emitente']['ie']);
        $this->getEmitente()->setRegime(Emitente::REGIME_SIMPLES);

        /* Endereço do emitente */
        $endereco = $this->getEmitente()->getEndereco();
        $endereco->setCEP($info['emitente']['endereco']['cep']);
        $endereco->getMunicipio()
                 ->setNome($info['emitente']['endereco']['cidade'])
                 ->getEstado()
                 ->setNome($info['emitente']['endereco']['estado'])
                 ->setUF($info['emitente']['endereco']['uf']);
        $endereco->setBairro($info['emitente']['endereco']['bairro']);
        $endereco->setLogradouro($info['emitente']['endereco']['logradouro']);
        $endereco->setNumero($info['emitente']['endereco']['numero']);
        return $this;
    }

    /**
     * Processa as notas e tarefas
     * @return int quantidade de notas processadas
     */
    public function processa()
    {
        return SEFAZ::getInstance()->processa();
    }
}
