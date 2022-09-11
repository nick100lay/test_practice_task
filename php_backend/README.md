# PHP Backend

## Настройка

Для работы нужно скопировать ```htaccess_example``` в ```.htaccess``` в директории бекенда.
Далее нужно определить следующие переменные окружения:
```
# Environments

# For PDO
SetEnv DB_DSN "" # database dsn
SetEnv DB_USERNAME "" # database user
SetEnv DB_PASSWORD "" # database user password (optional)
```

В директории src/ нужно создать ```.htaccess```, чтобы не давать доступ к файлам кода:
```
<Files "*">
 	Order Deny,Allow
  	Deny from all
</Files>
```

Для инициализации БД можно воспользоваться питон скриптами из src/scripts/.
```sh
# Инициализировать БД
# Пример: python init.py http://localhost
python init.py <domain>

python  put_test_products.py <domain> #  Создать тестовые товары
python  test_api.py <domain> # Протестировать API

```

## Методы API

### Ошибки

В случае ошибки возвращается код и сообщение:
```json
{
	"status": "int",
	"errorMsg": "string"
}
```
Если ошибок нет, то JSON будет иметь статус 0:
```json
{
	"status": 0,
	...
}
```
Коды ошибок:
```php
const ERROR_NONE = 0;

// Failed to decode input json
const ERROR_WRONG_INPUT = 1;

// Invalid JSON values
const ERROR_INVALID_JSON_VALUES = 2;

// Environment variable
// DB_DSN or DB_USERNAME is not set
const ERROR_NO_DB = 3;

// Failed to create pdo
const ERROR_PDO_FAILED = 4;

// pdo driver is unsupported
const ERROR_PDO_DRIVER_UNSUPPORTED = 5;

// Fail with DB
const ERROR_DB_FAIL = 6;
```

###POST /init.php
Инизиализировать БД.
Используется только для развертывания бекенда.
Ничего не принимает.
Возвращает JSON со статусом 0.

###GET /payment_methods.php
Получить все поддерживаемые способы оплаты.
Ничего не принимает. Возвращает JSON следующего вида:
```json
{
	"status": 0,
	"paymentMethods": [
		{
			"id": "string",
			"name": "string"
		}
		...
	]
}
```

###GET /products.php
Получить товары по запросу, либо получить товары с большим рейтингом.
Принимает JSON следующих видов:
```json
{"id": "string"} // Получить по id
{"query": "string"} // Получить по текстовому запросу
{} // Получить товары с большим рейтингом
```
Возвращает JSON следующего вида (возвращаемые товары отсортированы по убыванию рейтингу):
```json
[
	"status": 0,
	"products": [
		{
			"id": "string",
			"name": "string",
			"price": "string",
			"rating": "string",
			"description": "string|null",
			"imageURL": "string|null"
		}
		...
	]
]
```

###PUT /products.php
Создать товары.
Принимает JSON следующего вида:
```json
[
	{
		"id": "string",
		"name": "string",
		"price": "string",
		"rating": "string",
		"description": "string optional",
        "imageURL": "string optional"
	}
	...
]
```
Возвращает JSON со статусом 0.

###GET /orders.php
Получить заказы по номеру телефона или номера договора.
Принимает JSON следующих видов:
```json
// Номер телефона должен быть в виде '+7 xxx xxx-xx-xx'.
// Получить по номеру телефона
{"phone_number": "string"}

{"contract_number": "string"} // Получить по номеру договора
```
Возвращает JSON следующего вида (возвращаемые товары отсортированы по убыванию рейтингу):
```json
[
	"status": 0,
	"orders": [
		{
			"id": "string",
			"productId": "string",
			"productName": "string",
			"productPrice": "string",
			"productImageURL": "string|null"
			"paymentMethodId": "string",
			"paymentMethodName": "string",

			// Номер телефона виде '+7 xxx xxx-xx-xx'.
			"phoneNumber": "string",

			"firstName": "string",
			"secondName": "string",
			"isEntity": "boolean",

			// Всегда указан при "is_entity" == true
			"contractNumber": "string|null",

			"createdAt": "string",
		}
		...
	]
]
```

###PUT /orders.php
Создать заказы.
Принимает JSON следующего вида:
```json
[
	{
			"productId": "string",
			"paymentMethodId": "string",

			// Номер телефона должен быть в виде '+7 xxx xxx-xx-xx'.
			"phoneNumber": "string",

			"firstName": "string",
			"secondName": "string",

			"isEntity": "boolean",
			// Всегда должен указываться при "is_entity" == true
			"contractNumber": "string optional"
	}
	...
]
```
Возвращает JSON со статусом 0.
