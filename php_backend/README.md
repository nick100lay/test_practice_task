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

В случае ошибки возвращается  http статус код и сообщение:
```
400 - Bad GET
400 - Bad JSON
500 - <Ошикби, связанные с БД>
```

### POST /init.php
Инизиализировать БД.
Используется только для развертывания бекенда.
Ничего не принимает.
Возвращает сообщение об успешной инициализации БД.

### GET /payment_methods.php
Получить все поддерживаемые способы оплаты.
Ничего не принимает. Возвращает JSON следующего вида:
```json
[
	{
		"id": "string",
		"name": "string"
	}
	...
]
```

### GET /products.php
Получить товары по запросу, либо получить товары с большим рейтингом.
Принимает URL следующих видов:
```
/products.php?id=<string> - Получить товар по id
/products.php?query=<string> - Получить товар по текстовому запросу
/products.php - Получить товары с большим рейтингом
```
Возвращает JSON следующего вида (возвращаемые товары отсортированы по убыванию рейтингу):
```json
[
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
```

### POST /products.php
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
Возвращает сообщение об успешном создании товаров.

### GET /orders.php
Получить заказы по номеру телефона или номера договора.
Принимает URL следующих видов:
```
/orders.php?phoneNumber=<string> - Получить заказ по номеру телефона
/orders.php?contractNumber=<string> - Получить заказ по номеру договора
```
Возвращает JSON следующего вида:
```json
[
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
```

### POST /orders.php
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
Возвращает сообщение об успешном создании заказов.
