# Developing in the Bowpress Repository

The Bowpress repository (this repository) uses [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/), two open source [containerization](https://aws.amazon.com/what-is/containerization/) tools, to set up a development environment on your personal computer that is fully-featured, easily spun up, and isolated from the rest of your computer.

To begin, install [Docker Desktop](https://www.docker.com/products/docker-desktop/), a desktop application used to manage the Docker Engine.

When Docker Desktop is installed, run the `dev.sh` script in the root of this repository:

```sh
$ docker compose up
```

> Never used the Terminal before? Check out Professor Sean Barker's [Command Line Crash Course](https://tildesites.bowdoin.edu/~sbarker/unix/) to learn more!

After running the script, you'll see output about "pulling layers", and then you'll see a lot of output as the different Docker containers start up. This repository starts up three containers:

- A PHP server that runs the Wordpress application
- MariaDB, an open source fork of MySQL
- PHPMyAdmin, a web-based administration tool for the MariaDB database

Once the terminal output slows, all the containers have loaded and are ready to accept requests. You can visit the WordPress application at **<http://localhost:8080>**, and PHPMyAdmin at **<http://localhost:8180>**.

## Setting up WordPress

When the container starts, the database is empty. While it's not difficult to set up the database using the WordPress admin interface, there are a few steps that would get tedious. Instead of running through those steps, open another Terminal window and run the following command to set up the database:

```sh
$ docker exec db sh -c "mariadb --user root --password=password wordpress < /root/dev.sql"
```

This applies the bootstrap development database (in this repository at `dev.sql`) to the database in the `db` docker container.

You only need to run that command once. Even after you exit the `./dev.sh` script, Docker will remember your database contents.

## Using PHPMyAdmin

The PHPMyAdmin username is `root` and the password is `password`.
