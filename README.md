# Zadania Rekrutacyjne

## O aplikacji

REST API zbudowane w Laravel, łączące funkcje portalu społecznościowego z systemem zarządzania zadaniami. Użytkownicy mogą się rejestrować, obserwować innych, tworzyć posty oraz reagować na nie emoji. Aplikacja umożliwia również tworzenie list zadań (todos) z podzadaniami (tasks) i śledzeniem statusu wykonania. Uwierzytelnianie realizowane jest za pomocą tokenów Laravel Sanctum.

---

W repozytorium znajduje się kolekcja Postmana oraz plik Swaggera.

## Lista zadań

1. Sklonuj repozytorium
2. Zainstaluj i uruchom repozytorium (mozesz postawic lokalnie lub uzyc dockera `docker compose up`)
   - 2.1 Popraw błędy migracji
3. Do response pobierania todos dodaj ilość wykonanych tasków (tasks ze status: 1)
4. Wykonaj zapytanie PUT pojedynczego taska (np. zmiana statusu na wykonany)
   - 4.1 Dlaczego zapytanie nie zadziałało? Czy umiesz je wykonać, aby zadziałało? Lub poprawić?
5. Reakcje są zwracane jako lista reakcji - zmień, żeby przekazywane były jako tablica `emoji -> ilość`
6. Do requestu feed dowolnym sposobem dodaj paginację
7. Dodałem posta, czemu nie widze go w endpoincie feed? mozesz poprawic?
8. Zablokuj możliwość komentowania komentarzy dalej niż 2 poziomy