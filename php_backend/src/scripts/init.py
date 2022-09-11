

import sys

import requests



if len(sys.argv) != 2:
    prog = sys.argv[0]
    raise SystemExit(f"usage: {prog} <url>")

base_url = sys.argv[1]

try:
    r = requests.post(f"{base_url}/init.php")
except requests.exceptions.MissingSchema as e:
    raise SystemExit(f"error: {str(e)}") from e
except requests.exceptions.RequestException as e:
    raise SystemExit(f"error: {str(e)}") from e


status_code = r.status_code
text = r.text
if status_code not in range(200, 300):
    raise SystemExit(f"error: http status code {status_code}:\n{text}")


print(status_code, "\n", text, file=sys.stderr)
