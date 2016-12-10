## API para geração e envio de notas fiscais eletrônicas brasileiras (Beta)

###### Essa biblioteca permite a geração de notas fiscais de consumidor, serviço e outras

### Vantagens
- Código bem estruturado e bem reaproveitado que permite a fácil manutenção
- Estrutura de suporte para vários modelos de notas
- Fácil configuração e usabilidade
- Não escreve nenhum arquivo temporário nas pastas do servidor

### Motivo do projeto
As biliotecas de código aberto encontradas até o momento (2016) não fornecem uma estrutura sólida e de fácil utilização/manutenção, dessa forma surgiu a necessidade de criar uma bilioteca capaz de gerar notas fiscais em diversos modelos e que seja de fácil utilização 

### Ideia do projeto
A ideia é criar uma bilioteca em que as entidades da nota sejam implementadas em classes separadas, cada uma gerando seu próprio nó XML, no final da geração todos os nós são unificados assim gerando o XML por completo, dessa forma fica fácil a manutenção pois parte da ideia da divisão e conquista

### Expansão do projeto
Como o projeto é grande, precisamos da colaboração de desenvolvedores para melhoria do projeto, para isso envie suas implementações para o E-mail: suporte@mzsw.com.br ou faça um Pull request

### Outras bibliotecas
Segue na lista abaixo outras billiotecas para geração de boleto em que algumas delas este projeto se baseia
- NFePHP - https://github.com/nfephp-org/nfephp
 
### Exemplo básico de geração de nota fiscal
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
/* outras informações */
$emitente->setRegime(EmitenteRegime::SIMPLES);

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->setUF('PR');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->setCodigo(4118402);
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$emitente->setEndereco($endereco);
$nfce->setEmitente($emitente);

/* Destinatário */
$cliente = new Cliente();
$cliente->setNome('Fulano da Silva');
$cliente->setCPF('12345678912');
$cliente->setEmail('fulano@site.com.br');
$cliente->setTelefone('11988220055');

$cliente->setEndereco($endereco);
$nfce->setCliente($cliente);

/* Produtos */
$produto = new Produto();
$produto->setItem(1);
$produto->setCodigo(123456);
$produto->setCodigoBarras('7894900011517');
$produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
$produto->setUnidade(ProdutoUnidade::UNIDADE);
$produto->setPreco(4.50);
$produto->setQuantidade(2);
$produto->setNCM('2202.10.00');
$produto->setCEST('03.007.00');
$nfce->addProduto($produto);

/* Pagamentos */
$pagamento = new Pagamento();
$pagamento->setForma(PagamentoForma::DINHEIRO);
$pagamento->setValor(4.00);
$nfce->addPagamento($pagamento);

$xml = $nfce->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML();
```

### Solução de problemas
O código foi implementado e testado com PHP 5.4, verifique sua versão do PHP em caso de falhas na execução

### Dependências
- PHP 5.3 ou superior
- Extensão openssl para assinatura da nota
- Extensão curl para envio da nota

### Limitações
- Apenas para o modelo NFC-e foi implementado a geração da nota, mas ainda não foi testado
 
### Licença
Por favor veja o [arquivo de licença](/LICENSE.txt) para mais informações.
