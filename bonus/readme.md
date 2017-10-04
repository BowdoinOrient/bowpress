# The Bowdoin Orient
## B.O.N.U.S. 2.0

The Orient website has been online since sometime in the late 1990s, with earliest extant content dating back to September 2000. It previously underwent major revisions in 2001, 2004, and 2009. In 2004, it switched from static HTML to a PHP/MySQL web app. In 2009, a cosmetic revision was done; in 2012, for the first time in 8 years, it was rewritten from scratch. The code base was open sourced in late 2013.

## Setup
The recommended development environment is OSX 10.8 or 10.9. To get started:

* Install [Homebrew](http://mxcl.github.io/homebrew/), then run `brew doctor && brew update`.
* Install Git from Homebrew: `brew install git`
* Install MySQL from Homebrew: `brew install mysql` (MariaDB is also supported.)
* Check your PHP version with `php --version`. If it's less than 5.3, install PHP from Homebrew with `brew install php54`.
* Install the `sass` rubygem with `gem install sass`. 
* `cd` to the directory you want the BONUS folder to live in. The setup scripts are written assuming you will use `~/code/`, but this is easy to change.)
* [Fork this repository.](https://github.com/BowdoinOrient/bonus/fork)
* Clone your fork: `git clone https://github.com/your_user_name/bonus.git`
* `cd bonus && ./setup.sh`
* Choose whether or not to overwrite your httpd/php confs. If you don't, be aware you may need to manually enable PHP short tags, zlib output compression, etc. The only reason to not do this is if you have another PHP project already running locally on your computer.
* Provide your password for some `sudo`ing behind-the-scenes.
* If you chose a project root other than `~/code/`, edit `/etc/apache2/httpd.conf` (you will need `sudo`) and specify the location of the BONUS code.
* Acquire an SQL dump and populate your local database. Brian can help you with this. 
	* Or, if you're not an Orient developer and aren't interested in our data, import the database schema from `setup-files/` using Sequel Pro, etc.
* Email [@bjacobel](mailto:bjacobel@gmail.com) so he can give you some other [useful pointers](http://xkcd.com/138/)
* Make sure Apache is running: `sudo apachectl -k restart`
* Visit [bowdoinorient.dev](http://bowdoinorient.dev)
* Start writing code

##Gotchas
- **BONUS now uses Sass** for its stylesheets. If you don't know SASS, you can still write CSS (the language is backwards-compatible), but it is **VERY IMPORTANT** that you write it in the `*.scss` files in the `scss/` directory, **not** in the `css` directory. After you make a change, run `sass --update scss:css` from the BONUS root directory, and your Sass changes will be compiled to vanilla CSS and placed in the correct folder. Or, run `sass` with `--watch` rather than `--update` in a terminal while you edit Sass files and they will be compiled automatically.



##Contributing
The Orient welcomes bug reports and pull requests. If submitting a bug, please do so [through GitHub](https://github.com/BowdoinOrient/bonus/issues/new). Include your platform (OS and browser major version) and active extensions if you believe they may be involved. Please note that we will not address bugs replicable solely in IE <= 8. If submitting a pull request, please allow some time for an Orient developer to test and provide feedback on your work.

## License
BONUS is licensed under the terms of the [GNU Public License, v3](https://github.com/BowdoinOrient/bonus/blob/master/LICENSE.md). Fork us.