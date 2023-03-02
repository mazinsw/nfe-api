Ler certificados pfx antigos

ou can enable legacy option for Openssl 3:

Find and open the file at /etc/ssl/openssl.cnf

At the [default_sect] section change it to the following:

[default_sect]
activate = 1
[legacy_sect]
activate = 1
Then find the [provider_sect] and change it to the following:

[provider_sect]
default = default_sect
legacy = legacy_sect
