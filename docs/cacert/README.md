## Agrupar cadeia de certificados
Rodar comandos para aplicar novos arquivos adicionados

Entre na pasta
```sh
cd docs/cacert
```

Rode o comando (Linux apenas)
```sh
./append_cert.sh
```

Verificar conexão usando certificado
```sh
curl -v --cacert docs/cacert/cacert.pem --key storage/certs/private.pem --cert storage/certs/public.pem https://homologacao.nfce.sefa.pr.gov.br
```
