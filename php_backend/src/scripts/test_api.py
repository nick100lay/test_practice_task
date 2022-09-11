


import sys

import requests



if len(sys.argv) != 2:
    prog = sys.argv[0]
    raise SystemExit(f"usage: {prog} <url>")

base_url = sys.argv[1]


tests = (
        ("get", "products.php", "query text search", {"query": "serum"}),
        ("get", "products.php", "id search", {"id": "1"}),
        ("get", "products.php", "top of rating", {}),
        ("post", "orders.php", "post order", [{
            "productId": "1",
            "paymentMethodId": "1",
            "phoneNumber": "+7 000 100-00-00",
            "firstName": "Имя",
            "secondName": "Фамилия",
            "isEntity": False,
            }]),
        ("post", "orders.php", "post order by entity", [{
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
        f = getattr(requests, http_method)
        url = f"{base_url}/{api_method}"
        if http_method == "get":
            r = f(url, params=json)
        else:
            r = f(url, json=json)
    except requests.exceptions.MissingSchema as e:
        print(f"error: {str(e)}", file=sys.stderr)
        continue
    except requests.exceptions.RequestException as e:
        print(f"error: {str(e)}", file=sys.stderr)
        continue


    status_code = r.status_code
    text = r.text
    if status_code not in range(200, 300):
        print(f"error: http status code {status_code}:\n{text}", file=sys.stderr)
        continue

    print(status_code, "\n", text, file=sys.stderr)



print("successful", file=sys.stderr)
