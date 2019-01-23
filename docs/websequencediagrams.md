# https://www.websequencediagrams.com/

```txt
title Schemat przepływu niemutowalnego zapytania HTTP

Klient->API: Zapytanie HTTP
API->Baza danych: Zapytanie SQL
note right of API: Szukanie rekordu w Bazie danych
Baza danych->API: Rekord z bazy danych
API->Klient: Odpowiedź HTTP
```

```txt
title Schemat przepływu mutowalnego zapytania HTTP

Klient->API: Zapytanie HTTP
API->Kolejka: Wysłka wiadomości
API->Klient: Potwierdzenie przyjęcia zapytania HTTP
Pracownik 1->Kolejka: Pobranie wiadomości
Pracownik 1->Pracownik 1: Przetworzenie wiadomości
Pracownik 1->Baza danych: Rozpoczęcie transakcji
Pracownik 1->Baza danych: Aktualizacja stanu aplikacji
Baza danych->Baza danych: Utrwalenie zmian
Pracownik 1->Baza danych: Zakończenie transakcji
Pracownik 1->Kolejka: Emisja zdarzenia
Pracownik 2->Kolejka: Pobranie zdarzenia
Pracownik 2->Pracownik 2: Przetworzenie zdarzenia
Pracownik 2->Klient: Powiadomienie o zdarzeniu (Server Sent-Event)
opt Gdy po wystąpieniu danego zdarzenia są wymagane działania
    Pracownik 2->Kolejka: Zlecenie wykonania kolejnych operacji
end

```

```txt
title Schemat przepływu niemutowalnego zapytania HTTP przez Caching Reverse Proxy

Klient->Caching Reverse Proxy: Zapytanie HTTP
alt Odpowiedź HTTP znajduję się w Cache
    Caching Reverse Proxy->Klient: Zwrócenie odpowiedzi HTTP z Cache
else Odpowiedź HTTP nie znajduję się w Cache
    Caching Reverse Proxy->API: Przepisanie zapytania HTTP
    API->Baza danych: Zapytanie SQL
    note right of API: Szukanie rekordu w Bazie danych
    Baza danych->API: Rekord z bazy danych
    API->Caching Reverse Proxy: Odpowiedź HTTP
    Caching Reverse Proxy->Caching Reverse Proxy: Zapis odpowiedzi HTTP w Cache
    Caching Reverse Proxy->Klient: Przepisanie odpowiedzi HTTP
end

```
