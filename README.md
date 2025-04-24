# API Simulação de Crédito

## Descrição

Esta API REST desenvolvida em Laravel permite simular empréstimos com base em diferentes instituições e convênios. Os dados para simulação são obtidos de arquivos JSON, e a API não utiliza banco de dados para persistência.

## Requisitos

* PHP >= 8.2
* Composer
* Node.js (para dependências de front-end, se houver)

## Instalação

1.  **Clone o repositório:**

    ```bash
    git clone <seu_repositorio_aqui>
    cd api-simulacao
    ```

2.  **Instale as dependências do Composer:**

    ```bash
    composer install
    ```

3.  **Copie o arquivo `.env.example` para `.env`:**

    ```bash
    cp .env.example .env
    ```

    * Configure as variáveis de ambiente no arquivo `.env` (se necessário).  Para este projeto, você provavelmente não precisará alterar as configurações de banco de dados, mas certifique-se de que `APP_KEY` esteja definido. Se não estiver, execute `php artisan key:generate`.

4.  **Crie o arquivo do banco de dados SQLite (necessário para sessões, mesmo que a API não o use diretamente):**

    * ```bash
        touch database/database.sqlite
        ```

5.  **Execute as migrations do banco de dados (para as tabelas de sessão):**

    ```bash
    php artisan migrate
    ```

6.  **Inicie o servidor de desenvolvimento:**

    ```bash
    php artisan serve
    ```

    A API estará disponível em `http://127.0.0.1:8000`.

## Uso da API

### Rotas

* **GET /api/instituicoes**

    * Retorna a lista de instituições disponíveis.
    * Exemplo de Resposta:

        ```json
        [
            {
                "chave": "PAN",
                "valor": "Pan"
            },
            {
                "chave": "OLE",
                "valor": "Ole"
            },
            {
                "chave": "BMG",
                "valor": "Bmg"
            }
        ]
        ```

* **GET /api/convenios**

    * Retorna a lista de convênios disponíveis.
    * Exemplo de Resposta:

        ```json
        [
            {
                "chave": "INSS",
                "valor": "INSS"
            },
            {
                "chave": "FEDERAL",
                "valor": "Federal"
            },
            {
                "chave": "SIAPE",
                "valor": "Siape"
            }
        ]
        ```

* **POST /api/simulacao**

    * Realiza a simulação de crédito.
    * **Payload (JSON):**

        ```json
        {
            "valor_emprestimo": 1000.00,  //  Obrigatório, float
            "instituicoes": ["BMG", "PAN"], //  Obrigatório, array de strings
            "convenios": ["INSS"],         //  Opcional, array de strings
            "parcela": 72                 //  Opcional, número inteiro
        }
        ```

    * **Exemplo de Resposta:**

        ```json
        {
            "BMG": [
                {
                    "taxa": 2.05,
                    "parcelas": 72,
                    "valor_parcela": 305.97,
                    "convenio": "INSS"
                },
                //  ... mais simulações para BMG ...
            ],
            "PAN": [
                {
                    "taxa": 2.05,
                    "parcelas": 72,
                    "valor_parcela": 310.50,
                    "convenio": "INSS"
                }
                //  ... mais simulações para PAN ...
            ]
        }
        ```

## Observações

* Os arquivos JSON com os dados (instituições, convênios e taxas) devem estar localizados na pasta `storage/app/data/`.
* A aplicação foi desenvolvida seguindo os requisitos de não utilizar banco de dados para a lógica principal, mas o SQLite é usado internamente pelo Laravel para o gerenciamento de sessões.
* Para testar a API, utilize uma ferramenta como Postman ou Insomnia.
