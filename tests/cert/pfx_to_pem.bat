@echo off
openssl pkcs12 -in cert.pfx -out public.pem -clcerts -nokeys -nodes -passin pass:associacao
openssl x509 -in public.pem -out public.pem

openssl pkcs12 -in cert.pfx -out private.pem -nocerts -nodes -passin pass:associacao
openssl rsa -in private.pem -out private.pem 

pause