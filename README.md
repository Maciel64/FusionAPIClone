<p align="center">
  <img width="460" height="300" src="https://user-images.githubusercontent.com/32068444/190035647-c00e8c36-c370-41f1-a99b-e48bfd09c7ce.svg">
</p>

# Fusion backend api

- [diagnostico_fusion_app-mobile_001](https://docs.google.com/document/d/1nb7A20pv0iOwYcp0tKkHy62Avvu2hZimjhDV4vqJCo0/edit#heading=h.3oakqlcnrn94) 
- [diagnostico_fusion_sistema-web_001](https://docs.google.com/document/d/1415bKxBIy7jnfkjSu-huKOCWlKzDUXG1zGeoPYN48eY/edit#heading=h.3oakqlcnrn94) 


## Obrigatório para rodar o projeto

- [docker](https://docs.docker.com/get-docker/) 20.10.17

## Descrição

No projeto foi desenvolvido com as seguintes técnologias

- [docker](https://docs.docker.com/get-docker/) 20.10.17
- [docker-compose](https://docs.docker.com/compose/install/) 1.29.2
- [php](https://www.php.net/releases/8.1/en.php) 8.1.0
- [laravel](https://laravel.com/docs/8.x/releases) 8.61.0
- [mysql](https://dev.mysql.com/doc/relnotes/mysql/8.0/en/) 8.0.26
- [PHPunit](https://phpunit.readthedocs.io/en/9.5/) 9.5.10
- [composer](https://getcomposer.org/doc/00-intro.md) 2.1.9

- [git](https://git-scm.com/doc) 2.33.1
- [github](https://docs.github.com/pt) 1.0.0
- [git actions](https://docs.github.com/pt/actions) 1.0.0

# Executando o projeto

## Para saber os comandos artisan disponíveis

Use o comando docker `./docker/bin/fusion artisan help` para executar listar os comandos laravel artisan ou  acesse docker bash shell com `./docker/bin/fusion bash` e execute o comando `php artisan help`.

## Para rodar o projeto execute o script Shell

``` bash
sh start.sh
```

## Ou  Execute os comandos manualmente para rodar o projeto

01. Execute o comando `cp .env.example .env` para cria o arquivo de configuração do projeto.
02. Execute o comando `./docker/bin/fusion build` para construir a imagem do projeto.
03. Execute o comando `./docker/bin/fusion up -d`  para subir os containers do projeto.
04. Execute o comando `./docker/bin/fusion compose install` para instalar as dependências do projeto.
05. Execute o comando `./docker/bin/fusion artisan key:generate` para gerar a chave do projeto.
06. Execute o comando `./docker/bin/fusion artisan migrate:fresh --seed` para criar as tabelas e popular o banco de dados.
07. Execute o comando `./docker/bin/fusion artisan storage:link` para criar o link simbólico para o diretório de armazenamento do projeto.
08. Execute o comando `./docker/bin/fusion artisan test`  para executar os testes do projeto.
09. Execute o comando `./docker/bin/fusion bash` para acessar o bash do container do projeto.

## Documentação da api

... 10. Com o projeto já em execução, execute o comando `./docker/bin/fusion php artisan scribe:generate` para gerar a documentação da api.

## Commands Billing

... 11. Execute o comando `./docker/bin/fusion artisan generate:orders` para gerar a lista de ordens de pagamento para todos os customers.

## Para acessar o projeto 

Para acessar a documentação use a rota "/docs" ou clique [aqui](http://localhost:9000/docs).
