# Instrukcja Obsługi Aplikacji

## Uruchamianie

1. Uruchom plik `docker-compose.yml`:
   ```bash
   docker-compose up
   ```
2. Worker powinien uruchomić się automatycznie. Jeśli jednak tak się nie stanie, zrestartuj kontener po zakończeniu instalacji.

## Testowanie

Aby uruchomić testy, użyj poniższej komendy:
```bash
php ./vendor/bin/phpunit
```

## Kluczowe Ścieżki i Funkcjonalności

- **Dodanie wpisu**: Strona główna dostępna pod `/`.
- **Logowanie**: Formularz logowania znajduje się pod adresem `/login`.
- **Lista wpisów**: Możesz przejrzeć listę danych użytkowników pod adresem `/admin/user-data`.

## Tworzenie Administratora

Aby utworzyć użytkownika o uprawnieniach administratora, wykonaj następującą komendę:
```bash
php bin/console doctrine:fixtures:load
```

Domyślne dane logowania:
- Email: `admin@example.com`
- Hasło: `admin123`
