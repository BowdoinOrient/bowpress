# Exercise: Update the Volume and Issue in the Nameplate

This is an end-to-end tutorial to help you get familiar with:

1. Editing the source code of the Bowdoin Orient site
2. Testing your changes
3. Submitting a pull request
4. Deploying your changes to production

In this tutorial, we'll be updating the volume and issue numbers underneath the nameplate:

![](images/Screenshot%202023-12-26T19.55.30.png)

## How is the nameplate set up?

There are two parts to the nameplate that we'll be modifying today: the volume number and the issue number.

The issue number is dynamic: there is some code that maps issue numbers to the date when that issue is published. When the web page is requested, the code figures out the current date and returns the issue number associated with the most recent date.

The volume number is hardcoded into the source code: there is some HTML somewhere that simply says `<span>Volume 153</span>`.

In this tutorial, we'll make two changes to the source code to the site:

1. First, we'll update the hardcoded volume number
2. Second, we'll update the map of issue numbers so that the issue number correctly updates throughout the year.

## Local Development Setup

Follow the [Local Development](Local%20Development.md) instructions to get set up with a copy of the source code that you can edit. 

Once you're done, you should be able to open <http://localhost:8080> and see a version of the site with no content. You should also have the source code on your computer and should be able to edit it in your favorite text editor (I'm using Visual Studio Code).

![](images/Screenshot%202023-12-27T15.20.39.png)
## Starting a new task

Before we write any code, the first thing we need to do is [create a new branch](https://docs.github.com/en/get-started/quickstart/hello-world#creating-a-branch). This will split off our work into its own version history and will let us create a pull request later.
## Updating the Volume Number

The volume number is located in `src/orient-theme/header.php` on [line 246](https://github.com/BowdoinOrient/bowpress/blob/master/src/orient-theme/header.php#L246) as of this writing. The code looks like this:

```php
<span class="issue">Volume 153, Issue
	<?php $ci = current_issue();
	echo $ci["issue_num"]; ?>
</span>
```

Update the volume number, save the file, and refresh `localhost:8080` in your web browser. You should see the volume number update.

If you see the new volume number, [commit your changes](https://docs.github.com/en/get-started/quickstart/hello-world#making-and-committing-changes).

## Updating the Issue Number

In the code snippet above, you can see the `current_issue()` function called, and its return value "echoed" (printed) to the output. We are going to edit this function to set it up for the new set of published issues.

`current_issue` is defined in `src/orient-theme/functions.php` on [line 284](https://github.com/BowdoinOrient/bowpress/blob/master/src/orient-theme/functions.php#L284) as of this writing.

Within this function is an "associative array" (also known as a dictionary, object, or map) with the variable name `$vol153_issues`. The keys in this array are dates, and the values are the issue numbers published on that date.

Update all the dates and issues in this map, making sure to stick to the following format:

```php
"2023-12-27" => "12",
```

Notably:

* The key should be a double-quoted string with the issue's publish date in `YYYY-MM-DD` format
* The arrow should be an equals sign and a rightwards-facing bracket
* The value should be a double quoted string with the issue number
* Each line needs to end with a comma

You might also want to change the variable name. As of this writing, the variable name is `$vol153_issues`, but you might want to update it for your new volume (note that all variable names in PHP need to start with the dollar sign). Make sure to find and replace `$vol153_issues` throughout the rest of the function body.

Once you've updated all the dates and issue numbers, save the file and refresh `localhost:8080` in your web browser. You should see the issue number update based on the current date.

## Test your changes on staging

Your changes look like they're working on your own computer, but before we push them live, let's make sure they work in the staging environment by [Deploying to Staging](Deploying%20to%20Staging.md).

From the root of the repository, run the following [Terminal Command](Terminal%20Command.md):

```sh
$ npm run upload
```

Follow the onscreen prompts to enter your SFTP credentials and select what to upload. We've only changed `orient-theme` so you only need to upload `orient-theme`, but nothing bad will happen if you upload more bundles.

Once your files have been uploaded, you can see the published staging site at **<https://bowdoinoriedev.wpengine.com>

## Submit a pull request

If everything looks good on the staging site, you can follow the directions at [Submitting a Pull Request](Submitting%20a%20Pull%20Request.md). This will push your branch to Github and get your changes merged into master.