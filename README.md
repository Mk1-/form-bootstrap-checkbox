# form-bootstrap-checkbox

Zawiera:
* strony /login i /logout
* listę rekordów z bazy danych wybieranych po zakresie dat
* dodawanie nowego rekordu
* usuwanie rekordów zaznaczonych za pomocą checkbox

Po skonfigurowaniu połączenia do bazy danych (w pliku .env) należy wykonać polecenia:

**php bin/console doctrine:migrations:migrate**\
**php bin/console doctrine:fixtures:load**

Do aplikacji można zalogować się na użytkownika `user@nomail.com` z hasłem `user`.
