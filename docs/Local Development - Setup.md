# Local Development Setup

You'll only need to do these steps once (per computer). If you've already done all the setup steps, you can skip to the ["Development" section](#development).

## Step 1: Download the Repository

The source code for the Orient site is stored on Github. We can interact with Github using Git, a version control system.

If you've never used Git or Github before, go through these setup steps from Github:

1. [Create a Github Account](https://docs.github.com/en/get-started/quickstart/creating-an-account-on-github)
2. [Get started with Github](https://docs.github.com/en/get-started/quickstart/hello-world)
3. [Set up Git](https://docs.github.com/en/get-started/quickstart/set-up-git)

Once you have a Github account and are familiar with the basics, join the `BowdoinOrient/members` team so you can collaborate on the code: https://github.com/orgs/BowdoinOrient/teams/members

Finally, clone the `bowpress` repository (located at <https://github.com/BowdoinOrient/bowpress/>) to download the source code to your computer. Follow the instructions at [Cloning a repository](https://docs.github.com/en/repositories/creating-and-managing-repositories/cloning-a-repository).
### Step 2: Install NodeJS

[This page](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm) has good documentation describing how to install NodeJS.

When you're done, you should be able to run the following [Terminal Command](Terminal%20Command.md):

```sh
$ node -v
```

It should output a value that looks like `v18.xx.yy` - any version after 18.0.0 is appropriate.

## Step 3: Install Docker

Install [Docker Desktop](https://www.docker.com/products/docker-desktop/), a desktop application used to manage the Docker Engine. This will install several components behind the scenes.

When you're done, you should be able to run

```sh
docker -v
```

and see some sort of version number.

## Step 4: Initialize the WordPress Docker Container

Run the following [Terminal Command](Terminal%20Command.md) within the `bowpress` folder:

```sh
$ docker compose up
```

You'll see output about "pulling layers", and then you'll see a lot of output as the different pieces of software start up.

Once the terminal output slows, all the containers have loaded and are ready to accept requests.

## 5: Initialize the Database

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
> * `wordpress < root/dev.sql` runs the SQL commands in dev.sql and puts them into the `wordpress` database.

See [[Updating the Local Development Database]] for more.