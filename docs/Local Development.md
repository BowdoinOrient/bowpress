# Local Development

If you're changing any of the code on the site, you likely want to run the application *locally* (on your computer) so you can see the changes you make before you publish them. This doc describes how to do so.

> [!NOTE]
> Before continuing, make sure you've gone through the [Local Development - Setup](Local%20Development%20-%20Setup.md) instructions to get your computer ready for development.

To start up the Orient site so that you can work on it:

1. Open the project in your code editor
2. Open the Docker Desktop application
3. Open a terminal window and run `docker compose up`
4. Open **<http://localhost:8080>** to view the website.

If you want to log into the WordPress backend, the username is `admin` and the password is `admin`.

If you want to look at the contents of the database, you can:

1. Open **<http://localhost:8180>** to go to PHPMyAdmin, a web interface for interacting with database software
2. Enter the username `root` and the password `password`

### Working with Git

Before starting any work, you should [create a new branch](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-and-deleting-branches-within-your-repository). All the work you do to accomplish a specific task should be committed on that branch. 

Once you're ready, you should [deploy your changes to the Staging environment](Deploying%20to%20Staging.md) to test them.

If your changes all look good on the staging environment, you should [submit a pull request](Submitting%20a%20Pull%20Request.md) to get your code merged into the master branch, then [deploy your changes to Production](Deploying%20to%20Production.md).