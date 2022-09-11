

import sys

import requests



if len(sys.argv) != 2:
    prog = sys.argv[0]
    raise SystemExit(f"usage: {prog} <url>")

base_url = sys.argv[1]


test_products_url = "https://dummyjson.com/products"

try:
    r = requests.get(test_products_url)
except requests.exceptions.RequestException as e:
    raise SystemExit(f"error: {str(e)}") from e

products = r.json()["products"]
products = [*(
    map(lambda p: {
        "name": p["title"],
        "price": str(p["price"]),
        "rating": str(p["rating"]),
        "description": p["description"],
        "imageURL": p["images"][0],
    }, products)
)]


try:
    r = requests.post(f"{base_url}/products.php", json=products)
except requests.exceptions.MissingSchema as e:
    raise SystemExit(f"error: {str(e)}") from e
except requests.exceptions.RequestException as e:
    raise SystemExit(f"error: {str(e)}") from e


status_code = r.status_code
text = r.text
if status_code not in range(200, 300):
    raise SystemExit(f"error: http status code {status_code}:\n{text}")

print(status_code, "\n", text, file=sys.stderr)
