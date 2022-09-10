

import sys

import requests



if len(sys.argv) != 2:
    prog = sys.argv[0]
    raise SystemExit(f"usage: {prog} <url>")

url = sys.argv[1]

try:
    r = requests.post(f"{url}/init.php")
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
