# Test Case

## Ограничения
 - так как validts это int, примем, что все действия происходят в одном часовом поясе и не будем учитывать часовые пояса
 - если email confirmed, то не вызываем проверку check_email. Не учитываем, что можно зарегистрироваться на временную почту или удалить свой почтовый аккаунт
 - допустим, что все железо и БД у нас суперстабильные, поэтому перезапуск зафейленых команд оставим вне контекста задачи

## Инициализация
```bash
$ make init
$ make database_init
```

## Контакты

- [Dmitrii Denisov][link-author]

## Лицензия

"Test case" is software licensed under the [Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)](LICENSE).

[link-author]: https://github.com/dda58
