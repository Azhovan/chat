### folder structure 
-----

~~~
├── bootstrap          // Configure Container
│  
├── database           // migrations are defined here 
│    
├── phpdocer           // php-fpm, nginx configurations
├── public             // entry point of the application
├── routes             // defined routes
│
├── src                   // The source codes folder
│   ├── Controllers       
|   ├── Database         // database driver
|   ├── Encryptions      // classes used to encrypt and decrypt messages 
|   ├── Entities         // defined value objects 
|   ├── Models           //  database models
|   ├── Transformers     //  data decorators
│   ├── Routes
├── tests
│   ├── Integrations 
│   ├── units 
├── .env
├── docker-compose.yml
~~~


### Solution
- Two or more person can send messages through a `conversation` to each other. this design supports group chat also.
- Every conversation is **secured** (using OpenSSL) by an `encryption key`, means that each conversation has different encryption key.
- encrypted messages are signed using a `message authentication code (MAC)` so that their underlying value can not be modified once encrypted.

- below is the sending message flow:
    - a conversation is initialized by a user(every user can do it, doesn't matter who)
    - user sends message to server
    - on server, message is encrypted by `OpenSSL` which provides `AES-256` encryption.
    - message is stored in database 

- below is the reading message flow:
    - user asks server to get messages (all or in specific conversation)
    - server reads messages and `decrypts` them by using conversation encryption key
    - user can read messages ordered by creation time
    
### Application design
Below layers are implemented 

    - **Models**: services and relation between models are defined in this layer (src/Models folder)
    - **Controllers**: navigating/pre-processing the http requests (src/Controllers foder)
    - **Transformers**: data decorators, which are best place to modify Model's data before returning it to user (src/Transformers/ folder)
    - **Rouers**: define applications routes (routes/ folder )
    - **Migrations**: tables are created by these functionalities (database/migration/ folder) 
    
### Application configuration
This app is using `.env` file to define it's configurations. you can find it in root of the project 
which by default has below values. 
```bash
APP_KEY= # extra key to encrypt messages, if not provided, default value will be used 
APP_URI=localhost:8000 # used to run integration tests suite, this should be same for application
DB_CONNECTION=sqlite 
DB_HOST= # sqlite host. by default is localhost
DB_DATABASE= # absolute path to sqlite file, default value is:  ./database/database.sqlite.chat
```
## Run Application `(With Docker)`
make sure you have Docker installed. all configurations (dockerfile, nginx) are located inside of `phpdocker/` folder.
also you can find `docker-composer.yaml` in root of the project 
Run below commands 
- `docker-compose build && docker-compose up -d` 
- `docker exec -it chat-php-fpm bash -c "composer install && composer dump-autoload -o && composer migrate"` 

#### Run tests `(integration and unit)`
if you are **using** `Docker` to run application, first make sure you have ran above commands, and then run
` vendor/bin/phpunit tests` which runs both `unit` and `integration` tests


if you **dont** use docker, please keep in mind **integration tests** need an active instance of application
(you can define a host name in your web-server or simply in root of the project run `$(which php) -S localhost:8000`)

but despite from your environment configurations you always can run `vendor/bin/phpunit tests/Units` to run unit tests

#### End points 
Base url by default is : `localhost:8000`. if you you want to change it check `.docker-compose.yaml` and `phpdocker/nginx` and 
`phpdocker/php-fpm` folder

NOTICE: 
All endpoints except user creation endpoint need `Authorization` header field that MUST be 
passed in header. `Authorization` is simply id of the user or user uuid 

#### User Endpoints

1.1 Create User

- Response: `user` object is returned
~~~~
POST /users/?name=azhi&uuid=220
Host: localhost:8000

curl example:
curl --location --request POST 'localhost:8000/users/?name=azhi&uuid=220'

Response codes: 201 ,
 create object is returned
query strings **are not mandatory**(in this case anonymous user is created)

Response example
{
    "user_id": 600,
    "user_name": "azhi",
    "user_uuid": "220",
    "created_at": "2020-01-20 03:12:52",
    "updated_at": "2020-01-20 03:12:52"
}

~~~~
1.2. Get user information

- Please note that `Authorization` is user id or user uuid, which MUST be sent in header
- Response: `user` object is returned

~~~~
GET /users
Host: localhost:8000
Content-Type: application/json
Authorization: 1   

// curl example: 
curl --location --request GET 'localhost:8000/users/' \
--header 'Content-Type: application/json' \
--header 'Authorization: 1'

Response codes: 200, 400 
Response example: 
{
    "user_id": 1,
    "user_name": "dadi",
    "user_uuid": "6789000",
    "created_at": "2020-01-18 03:48:42",
    "updated_at": "2020-01-18 03:48:42"
}
~~~~

#### Conversations Endpoints

2. Create conversation

- Please note that `Authorization` is user id or user uuid, which MUST be sent in header
- Response: `conversation id` is returned which users can use it to send message to each other
- Response: conversation id is returned

~~~~
POST /conversations
Host: localhost:8000
Authorization: 1  

curl example:
curl --location --request POST 'localhost:8000/conversations/' \
--header 'Authorization: 1'

- Response code: 200
response example 
{
    "conversation_id": 1,
    "created_at": "2020-01-20 03:14:20",
    "updated_at": "2020-01-20 03:14:20"
}
~~~~

2.2 Send message to a conversation

- Please note that `Authorization` is user id or user uuid, which MUST be sent in header
- Request url contains conversation id  ({ID} in `POST /conversations/{ID}/messages`)
- Request body contains message: 
```json
{
	"message":"message 1"
}
```
- Request body format is `json`


~~~~
POST /conversations/{ID}/messages 
Host: localhost:8000
Accept-Charset: application/json
Authorization: 1
Content-Type: application/json

Body
{
	"message":"message 1"
}

curl example: 
curl --location --request POST 'localhost:8000/conversations/2/messages' \
--header 'Accept-Charset: application/json' \
--header 'Authorization: 1' \
--header 'Content-Type: application/json' \
--data-raw '{
	"message":"message 1"
}'


Response codes: 200, 400
Response example:
{
    "user_id": 1,
    "user_name": "azhi",
    "conversation_id": 1,
    "message": "message 1",
    "created_at": "2020-01-20 03:26:56",
    "updated_at": "2020-01-20 03:26:56"
}
~~~~

2.3 Get messages from a specific conversation

- Please note that `Authorization` is user id or user uuid, which **MUST** be sent in header
- Request url contains `conversation id`  (ID in `POST /conversations/{ID}/messages`)
- Response is array of messages ordered by date
- Response codes: 200, 400

~~~~
GET /conversations/{ID}/messages 
Host: localhost:8000
Accept-Charset: application/json
Authorization: 3
Content-Type: application/json


curl --location --request GET 'localhost:8000/conversations/2/messages' \
--header 'Accept-Charset: application/json' \
--header 'Authorization: 1' \
--header 'Content-Type: application/json'

Response example:

[
    {
        "id": 1,
        "user_id": "2",
        "conversation_id": "1",
        "message": "message 1",
        "created_at": "2020-01-18 15:52:55",
        "updated_at": "2020-01-18 15:52:55",
        "user_name": "elahe"
    },
    {
        "id": 2,
        "user_id": "3",
        "conversation_id": "2",
        "message": "message 1",
        "created_at": "2020-01-18 15:53:01",
        "updated_at": "2020-01-18 15:53:01",
        "user_name": "azhi"
    }
]

~~~~

#### extra endpoints 

3.1  Get all conversations for a user
- this api returns collection of all conversation which user has grouped by conversation
- Please note that `Authorization` is user id or user uuid, which **MUST** be sent in header


~~~~
GET /users/conversations 
Host: localhost:8000
Accept-Charset: application/json
Content-Type: application/json
Authorization: 1

curl example: 
curl --location --request GET 'localhost:8000/users/conversations' \
--header 'Accept-Charset: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: 1'
~~~~


For more details check `tests/` folder 

