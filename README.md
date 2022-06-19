## Table of contents
  - [About](#about)
  - [How to run](#how-to-run)
  - [Populating database](#populating-database)
  - [How to log in](#how-to-log-in)
  - [Functionalities](#functionalities)

## About

This is a web app that is supposed to resemble and have some basic functionalities of the popular social media app *Twitter*. The project is creatively named **SocialNetwork**. Core technologies are *Laravel 8*, *Vue 2* and *MySQL*.

## How to run

The app could be easily run with **Docker**. It is enough to run `docker-compose up` in the root directory of the project. Each subsequent run should be successful. The web server will be available on port **8000** and the db server on port **3306** on the host machine. In case of a conflict, this could be changed by executing the `docker-compose up` command "with parameters" e.g. `DB_EXPOSED_PORT=13306 APP_EXPOSED_PORT=18000 docker-compose up`. 

## Populating database

After the app is up and running, only *super* admin user will exist. Additional users with posts and other activities could be added by running script `./seed.sh`. The script should be executed only once.

## How to log in

As a consequence of db seeding, three fixed users will be available: **sadmin** (initially), **jade** and **cade** with respectively *super* admin, admin and *regular* user rights. A couple of other regular users will be also created. Each user's password and username match e.g. a user with username *xyz* can log in with password *xyz*.

## Functionalities

The main features of the application are:
- ***posting*** - Each user can make textual posts. Posts are created and displayed under the *Posts* section on the user's profile page.  
- ***replies*** - A user can reply to posts and replies themselves are considered to be posts. Replies could be found under *Posts & Replies* on the user's profile page.
- ***hashtags*** - Hashtags could be used in posts. 
- ***mentions*** - Users could be mentioned in posts.
- ***notifications*** - Every mention notifies the user in question.
- ***search*** - Users and hashtags could be searched for (hastag searches must start with **#**).
- ***likes*** - Posts could be liked and as such appear in the *Likes* section on the user's profile page. The number of likes is displayed under each post. 
- ***sharing*** - Posts could be shared as quotes or as reposts (reposts have their own texts). The number of shares is displayed under each post.
- ***following*** - Users can follow each other. Posts of followed users are displayed on the user's home page. The number of followers and followed users can be seen on the user's profile page.
- ***admins*** - Admin panel is available for admin users. Admins can (un)block users and delete their posts. Super admin also manages other admins.
- ***profile editing*** - Each user can edit his/her profile which includes setting a custom avatar. 
- ...
  
The ***forgotten password*** feature is also available. In order for this feature to work the mail server properties in the `./docker-compose.yml` file must be configured.
