# Opencaching.PL

This is the source code of [Opencaching.PL](https://opencaching.pl) and several other national Opencaching sites.

More documentation is available in [our Wiki](https://github.com/opencaching/opencaching-pl/wiki).

## Local development

You can run OCPL locally with DDEV, see its [installation guide](https://docs.ddev.com/en/stable/users/install/ddev-installation/). Alternatively, you can use the [development virtual machine](https://github.com/opencaching/opencaching-pl/wiki/vm_installation_and_usage_en_brief) or set up the environment manually on [Linux](https://github.com/opencaching/opencaching-pl/wiki/nonvm_linux) or [Windows](https://github.com/opencaching/opencaching-pl/wiki/nonvm_windows).

```sh
ddev start
ddev ocpl-init
```

`ddev ocpl-init` downloads a development database dump, imports it and runs OC and OKAPI database updates. Run it again whenever you want to reset the database.

- The site runs at https://ocpl.ddev.site, the mobile site at https://m.ocpl.ddev.site and OKAPI at https://ocpl.ddev.site/okapi/.
- You can log in as any user with the password `haslo`.
- Emails are caught by Mailpit, open it with `ddev mailpit`.
- Use `ddev mysql` for a database shell, and `ddev describe` for ports to connect other database clients.
- `lib/settings.inc.php` and `config/*.local.php` are created from templates in `.ddev/ocpl` on the first start.
- Cron jobs run every 5 minutes as on production, except for the jobs disabled in `config/cronjobs.local.php` and `config/okapiConfig.local.php`. Their output goes to `/tmp/ocpl-cron.log` (`ddev exec tail /tmp/ocpl-cron.log`).
