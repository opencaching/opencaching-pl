## Developer documentation of the database update system

### What is a database update?

A database update is a script in the [src/Utils/Database/Updates](https://github.com/opencaching/opencaching-pl/tree/master/src/Utils/Database/Updates)
directory, which will change database structure or content. Database updates
are maintained on the *Admin.DbUpdate* page, which is linked in the *sys* menu.

If you want to do any database change which is not for temporary testing,
but may be deployed later to other OC installations, please create a DB
update for that in your feature branch and run it via *Admin.DbUpdate* page.
This is the most failsafe way.

DB updates are numbered. If multiple updates are deployed at the same time
(e.g. on a site which has not been updated for a while, like a developer VM),
the numbers determine the run order. It is safe to have multiple updates with
the same number (e.g. because two developers wrote new updates in parallel);
however the run order of those updates will be undefinied.

### How to create a new database update

- Go to http://local.opencaching.pl/Admin.DbUpdate.
- Hit the *Create new update* button. A new DB update will appear at the top of the list.
- Click the *[rename]* link to give some descriptive name to the new DB update.
    You may also change its number, if there is any reason to do that.
- Edit the new update script file, which is located in *src/Utils/Database/Updates*.
    Follow the instructions given there.
- Run/test it.
- Commit the new DB update with your code.
- Indicate in your Pull Request that it contains a DB update.

In the update script you may either use special OcDb methods like
`createIndexIfNotExists()`, or run plain SQL code via `simpleQuery()` etc.
Whatever fits your purpose. For the special OcDb methods, there are usage examples
in [100_init.php](https://github.com/opencaching/opencaching-pl/blob/master/src/Utils/Database/Updates/100_init.php).

IMPORTANT: You can output diangostic information from your update by "echo",
"print", "printf" etc. This information will by shown when running updates
manually. As the update views are PUBLIC
([why?](https://github.com/opencaching/opencaching-pl/pull/1923)), you MUST NOT
output any sensitive data.

### Types of database updates

There are three types, which can be set in the `run` variable of the update's
`getProperties()` method:

- **auto** (default) - The update will be run exactly once on each OC site after
    code deployment. (On your developer VM, you may run it multiple times manually.)

- **manual** - This update can only be run manually, using the *[run]* link on the
    *Admin.DbUpdate* page. This is intended for test-only updates, or for updates that
    need to be synchronized with some local system configuration change.

- **always** - This update will run on all OC sites after each code deployment.
    You may use this e.g. to ensure DB consistency, or to nail some static
    DB contents to its defaults.

### Numbers

The update numbers are assigned like this:

- 001–099: Tests, special-purpose *manual* updates, updates that *always* run before
    all regular updates. These updates are allowed to run multiple times also on
    production sites.

- 100-899: Regular updates, which will run once on each production site.

- 900-999: Updates that run *always* after all other updates.

### Admin.DbUpdate actions

Buttons at the top:

- **Reload list** - The same as opening a new *Admin.DbUpdate* page. This is different
  from a page reload in the web browser: After a *run* or *rollback* action, page
  reload will repeat that action, while *Reload list* will not.

- **Run updates** - Only available if there are *auto* updates that did not run yet,
  or if there are *always* updates. Will run those updates in numeric order
  (from bottom to top of list).

- **Create new update** - See above.

- **Help** - Shows this documentation.

Links for each update, some of them only available on developer sites:

- (Click on update name) - Shows the PHP source.
- **run** - Runs a update that did not run yet or was rolled back.
- **run again** - Re-runs an update that did already run.
- **rollback** - Reverts an update back that was run.
    This is only available if the update has a `rollback()` method.
- **try rollback** - Runs the rollback method of an update that did not run yet or was
    rolled back.
- **rename** - Renames an update. It is safe to change the update's number, as long as
    it is safe to run the updates in the resulting order (see "Merging DB updates" below).
- **delete** - Deletes an update. This is not available for updates that have been
    merged to Git master, or have been run and have a `rollback()` method.
    In the latter case, do a *rollback* first.

Generally, you should do ALL this update maintenance on the *Admin.DbUpdate* page,
not manually in the file system or database. This is most failsafe.

### Re-running DB updates

On a developer site, you can run any update as often as you like.
On production sites, *auto* and *manual* updates will run only once. The
[run] option disappears after the update has run (without throwing an
Exception). This prevents database damage by accientially re-running an
update which is not safe to re-run.

However, if you know what you are doing - i.e. you have verified, tested
and are 100% sure that it is safe to re-run an update now - there are two
ways to accomplish that:

* As a developer: Change the UUID of the update (use some UUID generator).
It then will re-run on all sites. You can also use this to add code to an
existing update, instead of creating a new update for some small change.

* As SysAdmin:
Request http://opencaching.XX/Admin.DbUpdate/run/UUID&override=1
with the update's UUID as "UUID" argument. Please DO NOT do this if
the update did not perform properly due to a bug. Fix the bug instead.

### Merging DB updates

Every DB update should be tested by someone else before merge.

Also, please verify before merge that update numbers are in a valid order.
E.g. if updates 120 and 121 were already merged, and now you merge update 119,
then it must be safe to run those updates in all of these orders:

- 120, 121, 119
- 120, 119, 121
- 119, 120, 121

### DB routines (triggers, procedures and functions)

All routine definitions are in
[resources/sql/routines](https://github.com/opencaching/opencaching-pl/tree/master/resources/sql/routines).
When one of these files has changed, it will automatically be executed on the
production sites with next code deployment. On developer sites, use the
*Run updates* button on the [Admin.DbUpdate](http://local.opencaching.pl/Admin.DbUpdate) page.

The routine updates will run AFTER all other database updates (Why? See
[UpdateController](https://github.com/opencaching/opencaching-pl/tree/master/src/Controllers/UpdateController.php)::runOcDatabaseUpdate).
So if some new routine is needed to run a new DB update, you additionally have
to define the routine inside the DB update.

To **delete** a routine, remove the "CREATE ..." statement from the .sql file,
but keep the "DROP ..." statement. If lots of deletions have accumulated in the
.sql files, this can be cleaned up by moving the DROPs to a new DB update.
