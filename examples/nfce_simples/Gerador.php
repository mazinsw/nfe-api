<?php
namespace Example;

use NFe\Database\Estatico;
use NFe\Core\NFCe;

class Gerador extends Estatico
{
    /**
     * Numero da nota a ser usado
     * @var int
     */
    public $nfce_numero = 1;

    /**
     * Obtém as notas pendentes de envio, em contingência e corrigidas após
     * rejeitadas
     */
    public function getNotasAbertas($inicio = null, $quantidade = null)
    {
        $notas = [];
        $config = \NFe\Core\SEFAZ::getInstance()->getConfiguracao();
        // para cada nota executar o bloco abaixo
        try {
            /** Envio de Notas em contingência **/
            // Só envia o mesmo XML se não tiver ocorrido rejeição
            // if ($nota->isContingencia() && $nota->getEstado() == Nota::ESTADO_ASSINADO) {
            //     // não tenta enviar notas em contingência quando estiver offline
            //     if ($config->isOffline()) {
            //         return [];
            //     }
            //     // a nota entrou em contingência, mas nunca foi enviada
            //     $xmlfile = self::getCaminhoXmlAtual($nota);
            //     $nfce = new \NFe\Core\NFCe();
            //     $nfce->load($xmlfile);
            //     $notas[] = $nfce;
            //     return [];
            // }
            /** Novos envios e correções **/
            /** Preenchimento da nota **/
            $nfce = new NFCe();
            $nfce->setEmitente($config->getEmitente());
            $nfce->setCodigo(123); // código do pedido
            $nfce->setSerie(1);
            $nfce->setNumero($this->nota_numero); // número da nota incremental
            // obter essa flag da nota do banco
            $contingencia = false;
            // $contingencia = $nota->isContingencia();
            if ($contingencia) {
                $nfce->setEmissao(\NFe\Core\Nota::EMISSAO_CONTINGENCIA);
                // $nfce->setDataEmissao($nota->getDataLancamento());
                // $nfce->setDataContingencia($nota->getDataLancamento());
                // $nfce->setJustificativa($nota->getMotivo());
            } else {
                $nfce->setEmissao(\NFe\Core\Nota::EMISSAO_NORMAL);
                $nfce->setDataEmissao(time());
            }
            $delivery = false;
            // $delivery = $pedido->isDelivery();
            if ($delivery) {
                $nfce->setPresenca(\NFe\Core\Nota::PRESENCA_ENTREGA);
            } else {
                $nfce->setPresenca(\NFe\Core\Nota::PRESENCA_PRESENCIAL);
            }
            $operador = 'Operador';
            // $operador = $pedido->getNomeOperador();
            $nfce->addObservacao('Operador', $operador);
            $nfce->setAmbiente(NFCe::AMBIENTE_HOMOLOGACAO);
            // $nfce->setAmbiente($nota->getAmbiente());
            // $nota->setDataEmissao($nfce->getDataEmissao());
            // $nota->update();
            /* Destinatário */
            $destinatario = null;
            // $destinatario = new \NFe\Entity\Destinatario();
            // if ($cliente->getTipo() == Cliente::TIPO_FISICA) {
            //     $destinatario->setNome($cliente->getNomeCompleto());
            //     $destinatario->setCPF($cliente->getCPF());
            // } else {
            //     $destinatario->setRazaoSocial($cliente->getRazaoSocial());
            //     $destinatario->setCNPJ($cliente->getCNPJ());
            // }
            // $destinatario->setEmail($cliente->getEmail());
            // $destinatario->setTelefone($cliente->getTelefone());
            /* Endereço do destinatário */
            if ($delivery) {
                // $endereco = new \NFe\Entity\Endereco();
                // $endereco->setCEP($endereco->getCEP());
                // $endereco->getMunicipio()
                //          ->setNome($endereco->getCidade()))
                //          ->getEstado()
                //          ->setNome($endereco->getEstado()))
                //          ->setUF($endereco->getUF());
                // $endereco->setBairro($endereco->getBairro()));
                // $endereco->setLogradouro($endereco->getLogradouro());
                // $endereco->setNumero($endereco->getNumero());
                // $endereco->setComplemento($endereco->getComplemento());
                // $destinatario->setEndereco($endereco);
            }
            $nfce->setDestinatario($destinatario);
            /* Transporte */
            if ($delivery) {
                $transportador = new \NFe\Entity\Transporte\Transportador();
                $transportador->setRazaoSocial($nfce->getEmitente()->getRazaoSocial());
                $transportador->setCNPJ($nfce->getEmitente()->getCNPJ());
                $transportador->setIE($nfce->getEmitente()->getIE());
                $transportador->setEndereco($nfce->getEmitente()->getEndereco());
                $nfce->getTransporte()
                    ->setFrete(\NFe\Entity\Transporte::FRETE_REMETENTE)
                    ->setRetencao(null)
                    ->setVeiculo(null)
                    ->setReboque(null)
                    ->setTransportador($transportador);
            } else {
                $nfce->getTransporte()
                    ->setFrete(\NFe\Entity\Transporte::FRETE_NENHUM);
            }
            /* Produtos */
            $produto = new \NFe\Entity\Produto();
            $produto->setPedido(123);
            $produto->setCodigo(10);
            $produto->setCodigoBarras('SEM GTIN');
            $produto->setCodigoTributario($produto->getCodigoBarras());
            $produto->setDescricao('Coca Cola 350mL');
            $produto->setUnidade(\NFe\Entity\Produto::UNIDADE_UNIDADE);
            $produto->setPreco(3.50);
            $produto->setDespesas(0);
            $produto->setDesconto(0);
            $produto->setQuantidade(1);
            $produto->setNCM('22021000');
            $produto->setCEST(null);
            $produto->setCFOP('5405');
            /* Impostos */
            $imposto = \NFe\Entity\Imposto::criaPeloNome('ICMSSN500', false);
            $imposto->setTributacao('500');
            if ($imposto instanceof \NFe\Entity\Imposto\ICMS\Base) {
                $imposto->setOrigem(0); // origem da mercadoria
            }
            $produto->addImposto($imposto);
            $nfce->addProduto($produto);
            
            $pagamento = new \NFe\Entity\Pagamento();
            $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_DINHEIRO);
            $pagamento->setValor(5.00);
            // $pagamento->setCredenciadora('60889128000422');
            // if ($forma_pagto->getTipo() == \NFe\Entity\Pagamento::FORMA_CREDITO) {
            //     $pagamento->setBandeira($cartao->getBandeira());
            // }
            // $pagamento->setAutorizacao('110011');
            $nfce->addPagamento($pagamento);
            
            $troco = new \NFe\Entity\Pagamento();
            $troco->setValor(-1.50);
            $nfce->addPagamento($troco);
            $notas[] = $nfce;
        } catch (\Exception $e) {
            // marcar aqui a nota no banco como não corrigida
        }
        return $notas;
    }

    /**
     * Obtém as notas em processamento para consulta e possível protocolação
     */
    public function getNotasPendentes($inicio = null, $quantidade = null)
    {
        $tarefas = [];
        // $config = \NFe\Core\SEFAZ::getInstance()->getConfiguracao();
        // // não processa notas se estiver offline
        // if ($config->isOffline()) {
        //     return $tarefas;
        // }
        // // para cada nota executar o bloco abaixo
        // try {
        //     // carrega o XML da NFC-e que está em processamento
        //     $xmlfile = self::getCaminhoXmlAtual($nota);
        //     $nfce = new \NFe\Core\NFCe();
        //     $dom = $nfce->load($xmlfile);

        //     // Consulta pelo número do recibo
        //     $recibo = new \NFe\Task\Recibo();
        //     $recibo->setNumero($nota->getRecibo());
        //     $recibo->setAmbiente($nfce->getAmbiente());
        //     $recibo->setModelo($nfce->getModelo());
            
        //     // Cria a tarefa para consultar
        //     $tarefa = new \NFe\Task\Tarefa();
        //     $tarefa->setID($nota->getID()); // salva o ID da nota para posterior uso
        //     $tarefa->setAcao(\NFe\Task\Tarefa::ACAO_CONSULTAR);
        //     $tarefa->setNota($nfce);
        //     $tarefa->setAgente($recibo);
        //     $tarefa->setDocumento($dom);
            
        //     $tarefas[] = $tarefa;
        // } catch (\Exception $e) {
        //     // marcar aqui a nota no banco como não corrigida
        // }
        return $tarefas;
    }

    /**
     * Obtém as tarefas de inutilização, cancelamento e consulta de notas
     * pendentes que entraram em contingência
     */
    public function getNotasTarefas($inicio = null, $quantidade = null)
    {
        $tarefas = [];
        // $config = \NFe\Core\SEFAZ::getInstance()->getConfiguracao();
        // if ($config->isOffline()) {
        //     return $tarefas;
        // }
        // $emitente = $config->getEmitente();
        // $estado = $emitente->getEndereco()->getMunicipio()->getEstado();
        // // para cada nota executar o bloco abaixo
        // try {
        //     $nfce = new \NFe\Core\NFCe();
        //     $tarefa = new \NFe\Task\Tarefa();
        //     $tarefa->setID($nota->getID()); // salva o ID da nota para posterior uso
        //     switch ($nota->getAcao()) {
        //         case Nota::ACAO_AUTORIZAR:
        //             $xmlfile = self::getCaminhoXmlAtual($nota);
        //             $dom = $nfce->load($xmlfile);
        //             // Notas em contingência podem precisar de consultas quando não se sabe o status
        //             $tarefa->setAcao(\NFe\Task\Tarefa::ACAO_CONSULTAR);
        //             $tarefa->setNota($nfce);
        //             $tarefa->setDocumento($dom);
        //             $tarefas[] = $tarefa;
        //             break;
        //         case Nota::ACAO_CANCELAR:
        //             $xmlfile = self::getCaminhoXmlAtual($nota);
        //             $dom = $nfce->load($xmlfile);

        //             // cancelamento sem protocolo significa:
        //             // consulta para posterior cancelamento ou inutilização
        //             if (is_null($nota->getProtocolo()) || $nota->getEstado() == Nota::ESTADO_REJEITADO) {
        //                 $tarefa->setAcao(\NFe\Task\Tarefa::ACAO_CONSULTAR);
        //             } else {
        //                 $tarefa->setAcao(\NFe\Task\Tarefa::ACAO_CANCELAR);
        //                 $nfce->setJustificativa($nota->getMotivo());
        //             }
        //             $tarefa->setNota($nfce);
        //             $tarefa->setDocumento($dom);
        //             $tarefas[] = $tarefa;
        //             break;
        //         case Nota::ACAO_INUTILIZAR:
        //             // a inutilização nem sempre é originada de uma contingência equivocada
        //             // por isso ela não é criada a partir de um XML como as tarefas acima
        //             $inutilizacao = new \NFe\Task\Inutilizacao();
        //             $inutilizacao->setUF($estado->getUF());
        //             $inutilizacao->setCNPJ($emitente->getCNPJ());
        //             $inutilizacao->setAmbiente($nota->getAmbiente());
        //             $inutilizacao->setAno(date('Y', strtotime($nota->getDataLancamento())));
        //             $inutilizacao->setModelo($nfce->getModelo());
        //             $inutilizacao->setSerie($nota->getSerie());
        //             $inutilizacao->setInicio($nota->getNumeroInicial());
        //             $inutilizacao->setFinal($nota->getNumeroFinal());
        //             $inutilizacao->setJustificativa($nota->getMotivo());

        //             $tarefa->setAcao(\NFe\Task\Tarefa::ACAO_INUTILIZAR);
        //             $tarefa->setAgente($inutilizacao);
        //             $tarefas[] = $tarefa;
        //             break;
        //     }
        // } catch (\Exception $e) {
        //     // marcar aqui a nota no banco como não corrigida
        // }
        return $tarefas;
    }
}
