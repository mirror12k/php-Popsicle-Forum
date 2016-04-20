# Popsicle forum framework
A fully featured forum built on php5.5 and mysql in a MVC style. An expiremental project that I did in a week as practice.

## setup
- ensure php5.5 and mysql are working
- create a new mysql database for the popsicle forum
- go to config.php and change the pathBase (if necessary) and the database config
- visit the /setup page which will initializes the database and creates the necessary classes and admin user (if failed, drop the database and re-create it for safety)
- now you can login with admin/password, change your password, and begin creating your forum!

## features:
- forums
- threads by forums
- posts by threads
- bbcode support
- user registration and logins
- secure password storage (salted sha256)
- secure CSRF token usage everywhere (none-reusable, securely generated)
- users list
- view posts by user
- keyword search global posts (using mysql FTS)
- pretty urls
- class based privileges such as creating forums, threads, posts, etc.
- [un]muting/banning users
- [un]stickying/locking threads
- adminstrative creation/modification of user classes
- easy database, classes, and admin account setup by just visiting /setup

## example forum:
an example forum is setup for review at http://testpopsicle.x10host.com

## etc.
Free to use/modify/enjoy. The styling of pages is very poor because i didn't really work on the css that much, and instead focused all of my work on the backend logic.
