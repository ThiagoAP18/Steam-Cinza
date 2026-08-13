## Descrição
Esse é um sistema web focado em gerenciamento (aluguel, compra e venda) de licenças de jogos digitais.
O sistema vai além de um e-commerce comum, incluindo lógicas complexas de economia virtual, flutuação de preços, mercado de revenda entre usuários, sistema de locação com restrição de tempo e taxas dinâmicas de troca.
Este projeto foi desenvolvido com foco em arquitetura de Back-End, segurança de dados e modelagem de regras de negócio estritas.

## Features e Regras de Negócios
- **Economia de lançamento e escassez**: No lançamento de um jogo, a distribuidora libera um número de licenças específicas do jogo. Após esgotamento, o mercado será movido exclusivamente por revendas dos usuários.
- **Mercado de compra e venda**: Usuários, após certo período pós compra definido pela desenvolvedora/publicadora, podem vender as suas licenças para os outros usuários, que, por sua vez, podem comprá-las desses. O sistema aplicará um limite dinâmico de preços (teto e piso).
- **Sistema de locação**: Empréstimo de licenças com período máximo pré-estabelecido pela desenvolvedora/publicadora. O usuário, dono da licença, escolhe o período de tempo do aluguel, assim, estando disponível na loja. Juntamente ao mercado de compra e venda, o sistema automaticamente calcula os lucros de participação da plataforma, usuário e distribuidora do software.
- **Controle de Concorrência e Estados:** Bloqueios lógicos rigorosos no banco de dados para impedir que um usuário compre, alugue ou anuncie licenças que já estão em transição ou bloqueadas por outras operações.

## Atuação do projeto
- Arquitetura de sistemas.
- Desenvolvimento Back-End.
- Modelagem, Normalização e Integração de Banco de Dados.
- Mapeamento de Requisitos.
- QA e Testes para resolução de problemas técnicos.

## Stack Tecnológica
- Back-End: PHP
- Framework: Laravel
- Banco de Dados: MySQL
- Gerenciamento de Dependências: Composer/NPM

## Manual de Instalação
### **Recursos/Componentes Necessários**
  - PHP e Composer
  - Servidor Web (Apache/Nginx) e MySQL (exemplo: XAMPP).
  - Node.js (apenas para build de front-end e pacote de autenticação)

### **Guia de Instalação**
1. Clone o Repositório:
   
```bash
git clone https://github.com/ThiagoAP18/Steam-Cinza.git
cd steam-cinza
```

   
2. Instale as dependências do Back-End e Front-End:

```bash
composer install
npm install
```
   
3. Configure as variáveis de ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure seu banco de dados no arquivo .env e rode as migrations para construir as tabelas:

```bash
php artisan migrate
```

5. Inicie o servidor local e o build de assets:

```bash
php artisan serve
npm run dev
```

A aplicação estará rodando em http://127.0.0.1:8000.
