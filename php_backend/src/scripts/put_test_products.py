

import sys

import requests



if len(sys.argv) != 2:
    prog = sys.argv[0]
    raise SystemExit(f"usage: {prog} <url>")

url = sys.argv[1]


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
        "image_url": p["images"][0],
    }, products)
)]


try:
    r = requests.put(f"{url}/products.php", json=products)
except requests.exceptions.MissingSchema as e:
    raise SystemExit(f"error: {str(e)}") from e
except requests.exceptions.RequestException as e:
    raise SystemExit(f"error: {str(e)}") from e


status_code = r.status_code
if status_code != 200:
    text = r.text
    raise SystemExit(f"error: http status code {status_code}: {text}")

resp_json = r.json()

api_status = resp_json["status"]

if api_status != 0:
    err_msg = resp_json["error_msg"]
    raise SystemExit(f"error: api error code {status_code}: {err_msg}")


print("successful", file=sys.stderr)
