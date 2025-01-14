* Należy odpalić docker-compose.yml
* Worker powinien odpalić się automatycznie, a jeśli tak się nie stanie należy zrestartować kontener po instalacji
* Testy odpalamy przez: php ./vendor/bin/phpunit
* Dodanie wpisu: strona główna /
* Aby utworzyć admina należy odpalić: php bin/console doctrine:fixtures:load [admin@example.com / admin123]
* Logowanie: /login
* Lista wpisów: /admin/user-data
