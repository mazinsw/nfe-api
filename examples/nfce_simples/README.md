### Nessa página será abordado como estruturar seu projeto para emitir nota fiscal usando a bilioteca NFe-API

- Informações necessárias para emissão de notas
1. Certificado digital A1 e a senha do mesmo
2. Token e CSC do contribuinte
3. Informações da empresa que irá emitir as notas (Razão Social, CNPJ, Inscrição Estadual, Endereço, Regime tributário)

### Funcionamento
Essa biblioteca usa o conceito de eventos para executar ações, logo terá evento para nota autorizada, cancelada e outros.
Em cada evento você poderá salvar o XML e atualizar o status da nota no banco de dados.

Há dois tipos de objetos processados pela biblioteca, a Nota e a Tarefa. As notas devem ser criadas nos eventos ```\NFe\Database\Estatico::getNotasAbertas``` e ```\NFe\Database\Estatico::getNotasPendentes```, já as tarefas devem ser criadas no evento ```\NFe\Database\Estatico::getNotasTarefas``` e são responsáveis por inutilizar numerações, realizar cancelamento e consultar notas.

### Emitindo uma NFC-e
Para emitir uma NFC-e, iremos usar o exemplo da pasta [examples/nfce_simples](/mazinsw/nfe-api/tree/master/examples/nfce_simples)
1. Faça o clone dessa biblioteca pelo comando ```git clone https://github.com/mazinsw/nfe-api.git``` ou baixe o ZIP e faça a descompactação.
2. Entre na pasta clonada ou descompactada e rode o comando ```composer install```
3. Crie a pasta ```storage/certs``` e cole seu certificado nela com o nome ```certificado.pfx```
4. Entre na pasta ```examples/nfce_simples``` e altere o arquivo ```index.php``` preenchendo os campos ```$senha_certificado```, ```$contribuinte```, ```$endereco```, ```$emitente``` e salve o arquivo.
5. Na pasta ```examples/nfce_simples``` execute o comando ```php index.php```

No terminal deve ser exibido a mensagem ```1 notas processadas``` ou ```0 notas processadas``` em caso de falhas.

Aumente o número da nota no passo 4 para emitir outra NFC-e.

Para ver o XML autorizado, acesse a pasta ```storage/xml/homologacao/autorizado``` e abra o XML com o Google Chrome

Para ver os logs, acesse a pasta ```storage/logs```
