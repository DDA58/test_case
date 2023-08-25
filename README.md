# Test Case App

Show Test Case [document](TESTCASE.md)

## Conditions
 - Since validts is an int, we will assume that all actions take place in the same time zone and will not take into account time zones
 - If email is confirmed, then we do not call the check_email. We do not take into account that user can register for temporary mail or delete mail account
 - Let's say that all the hardware and the database are super stable, so we will leave the restart of the failed commands out of the context of the task

## Инициализация
```bash
$ make init
...Wait until database container exactly up...
$ make database_init
```

These commands run app container completely with running cron process and metrics

You can run command immediately
```bash
$ docker-compose exec app bash
and
$ php bin/console fill_commands_queue:before_expiration --days_before_expiration=1
or
$ php bin/console fill_commands_queue:after_expiration
```

These commands fill commands queue in database and run consuming worker to send emails through 50 processes

## Credits

- [Dmitrii Denisov][link-author]

## License

"Test case" is software licensed under the [Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)](LICENSE).

[link-author]: https://github.com/dda58
