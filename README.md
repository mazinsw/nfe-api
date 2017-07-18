# NFe-API
## API para geração e envio de notas fiscais eletrônicas brasileiras (Beta)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

###### Essa biblioteca permite a geração de notas fiscais de consumidor, serviço e outras

## Vantagens
- Código bem estruturado e bem reaproveitado que permite a fácil manutenção
- Estrutura de suporte para vários modelos de notas
- Fácil configuração e usabilidade

## Motivo do projeto
As biliotecas de código aberto encontradas até o momento (2016) não fornecem uma estrutura sólida e de fácil utilização/manutenção, dessa forma surgiu a necessidade de criar uma bilioteca capaz de gerar notas fiscais em diversos modelos e que seja de fácil utilização 

## Ideia do projeto
A ideia é criar uma bilioteca em que as entidades da nota sejam implementadas em classes separadas, cada uma gerando seu próprio nó XML, no final da geração todos os nós são unificados assim gerando o XML por completo, dessa forma fica fácil a manutenção pois parte da ideia da divisão e conquista

## Instalação

Clone o repositório com `git clone https://github.com/mazinsw/nfe-api.git` ou [baixe a última versão](https://github.com/mazinsw/nfe-api/archive/master.zip).

Execute o comando abaixo na pasta do projeto

```sh
composer install
```

## Exemplo básico de geração de nota fiscal
```php
$nfce = new NFCe();
$nfce->setCodigo('123456');
$nfce->setSerie('1');
$nfce->setNumero('73');
$nfce->setDataEmissao(time());
/* outras informações */

/* Emitente */
$emitente = new Emitente();
$emitente->setRazaoSocial('Empresa LTDA');
$emitente->setFantasia('Minha Empresa');
$emitente->setCNPJ('08120787000152');
$emitente->setIE('123456789');
$emitente->setRegime(Emitente::REGIME_SIMPLES);

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->getMunicipio()
         ->setNome('Paranavaí')
         ->getEstado()
         ->setUF('PR');
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$emitente->setEndereco($endereco);
$nfce->setEmitente($emitente);

/* Destinatário */
$destinatario = new Destinatario();
$destinatario->setNome('Fulano da Silva');
$destinatario->setCPF('12345678912');

$destinatario->setEndereco($endereco);
$nfce->setDestinatario($destinatario);

/* Produtos */
$produto = new Produto();
$produto->setCodigo(123456);
$produto->setCodigoBarras('7894900011531');
$produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
$produto->setUnidade(Produto::UNIDADE_UNIDADE);
$produto->setPreco(4.99);
$produto->setQuantidade(1);
$produto->setNCM('22021000');
$produto->setCEST('0300700');
$produto->setCFOP('5405');
$nfce->addProduto($produto);

/* Pagamentos */
$pagamento = new Pagamento();
$pagamento->setForma(Pagamento::FORMA_DINHEIRO);
$pagamento->setValor(9.49);
$nfce->addPagamento($pagamento);

$sefaz = SEFAZ::getInstance();
$sefaz->addNota($nfce)
	  ->autoriza();
```

## Expansão do projeto
Como o projeto é grande, precisamos da colaboração de desenvolvedores para melhoria do projeto, para isso envie suas implementações para o E-mail: desenvolvimento@mzsw.com.br ou faça um Pull request

## Solução de problemas
O código foi implementado e testado com PHP 5.6, verifique sua versão do PHP em caso de falhas na execução

## Dependências
- PHP 5.3 ou superior
- Extensão openssl para assinatura da nota
- Extensão curl para envio da nota

## Limitações
- Apenas para o modelo NFC-e foi implementado a geração da nota, mas ainda não foi testado
 
## Licença
Por favor veja o [arquivo de licença](/LICENSE.txt) para mais informações.

[ico-version]: https://poser.pugx.org/mazinsw/nfe-api/version
[ico-travis]: https://api.travis-ci.org/mazinsw/nfe-api.svg
[ico-scrutinizer]: https://scrutinizer-ci.com/g/mazinsw/nfe-api/badges/coverage.png
[ico-code-quality]: https://scrutinizer-ci.com/g/mazinsw/nfe-api/badges/quality-score.png
[ico-downloads]: https://poser.pugx.org/mazinsw/nfe-api/d/total.svg

[link-packagist]: https://packagist.org/packages/mazinsw/nfe-api
[link-travis]: https://travis-ci.org/mazinsw/nfe-api
[link-scrutinizer]: https://scrutinizer-ci.com/g/mazinsw/nfe-api/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/mazinsw/nfe-api
[link-downloads]: https://packagist.org/packages/mazinsw/nfe-api