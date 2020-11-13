Se estiver usando PHP7.0+ pode executar o projeto rodando o comando:

```bash
php -S localhost:8000
```

Para rodar o projeto você precisa ter 2 variáveis de ambiente:
- PAGSEGURO_MAIL: email de vendedor junto ao PagSeguro
- PAGSEGURO_S_TOKEN: token deste vendedor junto ao PagSeguro


Para executar o projeto enviando as variáveis de ambiente você pode:
```bash
PAGSEGURO_MAIL=email@email.com  PAGSEGURO_S_TOKEN=token php -S localhost:8000
```