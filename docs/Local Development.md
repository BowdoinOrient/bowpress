If you're changing any of the code on the site, you likely want to run the application *locally* (on your computer) so you can see the changes you make before you publish them. This doc describes how to do so.
## Setup

You'll only need to do these steps once (per computer). Once you're set up, you can skip to the next section.

### Step 1: Install NodeJS

[This page](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm) has good documentation describing how to install NodeJS.

When you're done, you should be able to run the following [[Terminal Command]]:

```sh
$ node -v
```

It should output a value that looks like `v18.xx.yy` - any version after 18.0.0 is appropriate.

### Step 2: Install Docker

Install [Docker Desktop](https://www.docker.com/products/docker-desktop/), a desktop application used to manage the Docker Engine. This will install several components behind the scenes.

When you're done, you should be able to run

```sh
docker -v
```

and see some sort of version number.

### Step 3: Initialize the WordPress Docker Container

Run the following [[terminal command]] within the `bowpress` folder:

```sh
$ docker compose up
```

You'll see output about "pulling layers", and then you'll see a lot of output as the different pieces of software start up.

Once the terminal output slows, all the containers have loaded and are ready to accept requests.

### Step 4: Initialize the Database

When the container starts for the very first time, the database is empty. This project contains a "bootstrap database" that gets the database set up to mimic an empty Bowdoin Orient database.

Open another Terminal window and run the following command to set up the database:

```sh
$ docker exec db sh -c "mariadb --user root --password=password wordpress < /root/dev.sql"
```

> What's going on here?
> 
> * `docker exec db sh -c` denotes that everything in quotes is going to be run in the `db` Docker container (in the little virtual machine running the database on your computer).
> 
> * `mariadb --user root --password=password` logs into the database application.
>
> * `wordpress > root/dev.sql` runs the SQL commands in dev.sql and puts them into the `wordpress` database.

## Doing development

To start up the Orient site so that you can work on it:

1. Open the project in your code editor
2. Open the Docker Desktop application
3. Open a terminal window and run `docker compose up`
4. Open **<http://localhost:8080>** to view the website.

If you want to log into the WordPress backend, you can: the username is `admin` and the password is `admin`.

If you want to look at the contents of the database, you can:

1. Open **<http://localhost:8180>** to go to PHPMyAdmin, a web interface for interacting with database software
2. Enter the username `root` and the password `password`

From there, your next steps depend on what surface you want to work on. For example, you might want to work on the WordPress theme [[Orient Theme]] to control the look and feel of the site, or you might want to work on one of the plugins: [[orient-home-pages]], [[orient-taxonomies]], or [[orient-image-handling]].