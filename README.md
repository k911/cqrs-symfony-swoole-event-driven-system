# Projekt Inżynierski - Dokumentacja deweloperska
Autor: Konrad Obal

## Wymagania

- Docker CE
- docker-compose

## Uruchamianie

```bash
docker-compose up -d redis mysql pma rabbitmq mercure
bin/reset-env
docker-compose build
docker-compose up -d
```

## Dostęp

### API

- Dokumentacja: http://localhost/api/docs
- Dane uwierzytelniające:
  - **Email**: `super@admin.pl`
  - **Password**: `password`
- Autoryzacja zapytań:

    1. Należy uzyskać token autoryzacyjny (logowanie)

        ```bash
        curl -X POST -H "Content-Type: application/json" http://localhost/api/token -d '{"email":"super@admin.pl","password":"password"}'

        # Przykładowa odpowiedź:
        # {"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NDcwMzc4MjksImV4cCI6MTU0NzA0MTQyOSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InN1cGVyQGFkbWluLnBsIn0.sm0mTo2EF3wKAKHT13EufFhULBXe82KVKM2lHwVYKj6bGU96Ty9aKU6CeOjwBp-M0na3UxJHmTQL7QNOgTH1gI82wy-9UuEUqs1Blxqu8xgVe51RxW3FLtnooW2rBj9O7404Fy9LyOtOsVZrJdGb9yZmn_yLjO2wo_OZTji0Tx1_CxUbzBn6wa3loOWmTExg_-vPgpTJq0wVdCnWZQGHGKnPMv3hukRT_cxWhC7_bAyUmomFleS1an5fJ35sbb0zX722aZpcPTSMmvIEfKCD5Zmv2h6oPJ825F-8_KAnQm-bbc2wGGgT3LHNUk9r5mNIQmZJEKqacnUI4bOzzKEVjOyHFSsiUE4FEt_m4uMFlzkkJMMeRda4Slx4dzWh_nvFSK7y-7njj5qKQ_gZEPwqvBN3pzO2pW_GgbXjmyphkP560iX73PixszG3PMLuDpiO8yP3YYVU3AKT0V54jhFQtuA_fO5ic8jYr-POAKARv6bUMdrlLOg-lQAw1H4hjf2lL5ZvjUCtHnxdSabhw4_yvsEkhB9VukwR_wKVIdm3gWNMq0pI-kXyVnb1RnU0_8BVoD5wqMVyyxWJSziKZVm9pvREMI5dmWsYk4cTF6ARX2JntAK8wZcPPosrZq7WvkH_v9LPBVYyEzruA_rybrp0EKC0ufZtwkpu575-RBAHQKs"}
        ```

    2. Wartość z pod klucza `token` otrzymanego obiektu JSON, należy skopiować.
    3. Na stronie z dokumentacją API należy, nacisnąć przycisk `Authorize`
    4. W polu `Value for the authorization header` należy wkleić token w następującej formie: `Bearer WARTOŚĆ_TOKENU`
    *Przykład*: `Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NDcwMzc4MjksImV4cCI6MTU0NzA0MTQyOSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InN1cGVyQGFkbWluLnBsIn0.sm0mTo2EF3wKAKHT13EufFhULBXe82KVKM2lHwVYKj6bGU96Ty9aKU6CeOjwBp-M0na3UxJHmTQL7QNOgTH1gI82wy-9UuEUqs1Blxqu8xgVe51RxW3FLtnooW2rBj9O7404Fy9LyOtOsVZrJdGb9yZmn_yLjO2wo_OZTji0Tx1_CxUbzBn6wa3loOWmTExg_-vPgpTJq0wVdCnWZQGHGKnPMv3hukRT_cxWhC7_bAyUmomFleS1an5fJ35sbb0zX722aZpcPTSMmvIEfKCD5Zmv2h6oPJ825F-8_KAnQm-bbc2wGGgT3LHNUk9r5mNIQmZJEKqacnUI4bOzzKEVjOyHFSsiUE4FEt_m4uMFlzkkJMMeRda4Slx4dzWh_nvFSK7y-7njj5qKQ_gZEPwqvBN3pzO2pW_GgbXjmyphkP560iX73PixszG3PMLuDpiO8yP3YYVU3AKT0V54jhFQtuA_fO5ic8jYr-POAKARv6bUMdrlLOg-lQAw1H4hjf2lL5ZvjUCtHnxdSabhw4_yvsEkhB9VukwR_wKVIdm3gWNMq0pI-kXyVnb1RnU0_8BVoD5wqMVyyxWJSziKZVm9pvREMI5dmWsYk4cTF6ARX2JntAK8wZcPPosrZq7WvkH_v9LPBVYyEzruA_rybrp0EKC0ufZtwkpu575-RBAHQKs`
    5. Następnie należy nacisnąć przycisk `Authorize` i zamknąć okno.
    6. Od teraz zapytania wykonywane z poziomu dokumentacji, będą zautoryzowane jako administrator systemu.

### System kolejek (RabbitMQ)

- Strona logowania: http://localhost:15672
- Dane uwierzytelniające:
  - **Username**: `user`
  - **Password**: `password`

### Baza danych (MySQL, PHPMyAdmin)

#### MySQL

- Dane uwierzytelniające:
  - **Username**: `root`
  - **Password**: `Passw0rd`

#### PHPMyAdmin

- Dostęp do preautoryzowanego panelu: http://localhost:8080
- Nazwa bazy danych: `database`

### System do obsługi Cache aplikacji (Redis)

- Dostęp: http://localhost:6379
- System nie wymaga autoryzacji

### System do obsługi Server-Sent Events (Mercure)

- Dostęp: http://localhost:3333
- System nie wymaga autoryzacji
- W polu `Subscribe`, należy wprowadzić następujące dane, aby nasłuchiwać na wszystkie rodzaje zdarzeń wyemitowane przez aplikacje:

    ```
    http://localhost/api/reviews
    http://localhost/api/users
    ```




