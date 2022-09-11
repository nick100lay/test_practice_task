


import sys

import requests



if len(sys.argv) != 2:
    prog = sys.argv[0]
    raise SystemExit(f"usage: {prog} <url>")

url = sys.argv[1]


tests = (
        ("get", "products.php", "query text search", {"query": "serum"}),
        ("get", "products.php", "id search", {"id": "1"}),
        ("get", "products.php", "top of rating", {}),
        ("put", "orders.php", "put order", [{
            "productId": "1",
            "paymentMethodId": "1",
            "phoneNumber": "+7 000 100-00-00",
            "firstName": "Имя",
            "secondName": "Фамилия",
            "isEntity": False,
            }]),
        ("put", "orders.php", "put order by entity", [{
            "productId": "1",
            "paymentMethodId": "1",
            "phoneNumber": "+7 100 100-00-00",
            "firstName": "Имя",
            "secondName": "Фамилия",
            "isEntity": True,
            "contractNumber": "12",
            }]),
        ("get", "orders.php", "get order by phone", {"phoneNumber": "+7 100 100-00-00"}),
        ("get", "orders.php", "get order by contract", {"contractNumber": "12"}),
        ("get", "payment_methods.php", "get payment methods", {})
        )


for http_method, api_method, title, json in tests:
    print(title)
    try:
        r = getattr(requests, http_method)(f"{url}/{api_method}", json=json)
    except requests.exceptions.MissingSchema as e:
        print(f"error: {str(e)}", file=sys.stderr)
        continue
    except requests.exceptions.RequestException as e:
        print(f"error: {str(e)}", file=sys.stderr)
        continue


    status_code = r.status_code
    if status_code != 200:
        text = r.text
        print(f"error: http status code {status_code}: {text}", file=sys.stderr)
        continue

    print(r.text)



print("successful", file=sys.stderr)
