#!/bin/bash

PORT=8118
export http_proxy=http://127.0.0.1:$PORT
export HTTP_PROXY=http://127.0.0.1:$PORT
export http_proxys=http://127.0.0.1:$PORT
export HTTP_PROXYS=http://127.0.0.1:$PORT

exec "$@"
