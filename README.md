Flight Api Usage:

- to register and login user, you need to set headers `Content-Type: application/json, XRequestedWith: XMLHttpRequest`
- register user by posting something like this to `http://flight-api.local/api/auth/signup`:
```
{
	"name": "Kittyhawk",
	"email": "kittyhawk@gmail.com",
	"password": "secret",
	"password_confirmation": "secret"
}
```
- login user to get token
```
{
	"email": "kittyhawk@gmail.com",
	"password": "secret",
	"remember_me": true
}
```
- get token
```
{
    "access_token": "eyJ0eXAiOiJKV1QiLC...",
    "token_type": "Bearer",
    "expires_at": "2019-08-09 01:49:42"
}
```
- use token on all requests by setting header `Authorization` to something like `Bearer eyJ0eXAiOiJKV1QiLC...`

- post flight data to `http://flight-api.local/api/v1/flightdata`
- retrieve flights list at `http://flight-api.local/api/v1/list/flights`
- retrieve flights list flight by uuid at url similar to `http://flight-api.local/api/v1/list/flights/1540663938919-B00E2`
- retrieve flights detail at `http://flight-api.local/api/v1/detail/flights`
- retrieve flights detail flight by uuid at url similar to `http://flight-api.local/api/v1/detail/flights/1540663938919-B00E2`
