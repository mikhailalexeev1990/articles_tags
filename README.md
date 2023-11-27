## How to run local
### Dev mode
To start local development (local folder binds to container):  
`docker-compose up -d`

## Browse site
### local:
http://localhost:8001

## Healthcheck:
### backend:
Request: `curl http://localhost:8001/healthcheck`

Response:
```
HTTP/1.1 200 OK
Cache-Control: no-cache, private
Content-Type: application/json
Date: Sat, 22 Jan 2022 09:48:29 GMT
X-Robots-Tag: noindex
Content-Length: 15

{"status":"ok"}
```
