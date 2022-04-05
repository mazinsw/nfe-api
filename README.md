# NFe-API
## Bliblioteca para geração, transmissão e tratamento de eventos de notas fiscais eletrônicas

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

###### Essa biblioteca permite a geração, transmissão e tratamento de eventos de notas fiscais eletrônicas do Brasil

## Vantagens
- Código bem estruturado e bem reaproveitado que permite a fácil manutenção
- Estrutura de código expansível para vários modelos de notas
- Fácil configuração e usabilidade (Só precisa implementar 2 classes para integração)
- Testes e cobertura de código que garantem melhor estabilidade
- Estrutura desvinculada do XML, atualizações quase não afetam em produção

## Motivo do projeto
As biliotecas de código aberto encontradas até o momento (2016) não fornecem uma estrutura sólida e de fácil utilização/manutenção, dessa forma surgiu a necessidade de criar uma bilioteca capaz de gerar notas fiscais em diversos modelos e que seja de fácil utilização

## Ideia do projeto
A ideia é criar uma bilioteca em que as entidades da nota sejam implementadas em classes separadas, cada uma gerando seu próprio nó XML, no final da geração todos os nós são unificados assim gerando o XML por completo, dessa forma fica fácil a manutenção pois parte da ideia da divisão e conquista

## Instalação

Você precisará do [Composer][link-composer] para instalar essa biblioteca.

Execute o comando abaixo na pasta do seu projeto

```sh
composer require mazinsw/nfe-api
```

## Manutenção
Atualizar tabelas IBPT
```sh
# linux
./utils/update_IBPT.sh
```
```cmd
REM Windows
utils\update_IBPT.bat
```

## Documentação

Acesse [aqui](../../wiki) para ver a documentação

## Colaboração no projeto
Para melhoria do projeto envie suas implementações por meio de um Pull request

## Solução de problemas
Caso tenha problemas ao utilizar a biblioteca, acesse o grupo no Discord: https://discord.gg/XGU2Y77

## Dependências
- PHP 7.3 ou superior
- Extensão openssl para assinatura da nota
- Extensão curl para envio da nota

## Limitações
- Apenas o modelo NFC-e foi implementado e testado
- Só funciona com certificado digital modelo A1
- Não suporta duas versões da NF-e ao mesmo tempo

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
[link-composer]: https://getcomposer.org
