# Terminal Commands

To work with Git, Node, Docker, or other tools, you need to have a familiarity with the Terminal. The terminal is a text-based interface that lets you perform commands and see the response.

> [!NOTE]
> A lot of this content is cribbed from Professor Barker's [Unix Crash Course.](https://tildesites.bowdoin.edu/~sbarker/unix/)

## Opening the Terminal 

If you're on a Mac, you already have a Terminal application ready to go. On a Mac, the Terminal application is located at `Macintosh HD/Applications/Utilities/Terminal`.

If you're on a Windows computer, you'll need to install the [Windows Subsystem for Linux](https://learn.microsoft.com/en-us/windows/wsl/install) and interact with it using PowerShell.

## The Command Prompt

You'll be greeted by a window with something that looks like this:

```sh
username@computer $
```

The command prompt waits for you to enter a Unix command, which is then executed by pressing enter. Any output that the command produces will be shown below the command prompt. After the command has been executed, another command prompt is displayed so that you can enter another command, and so forth.

Most commands that I've written in this documentation start with a dollar sign, like so:

```
$ pwd
```

When following the instructions, do not copy the dollar sign when entering the command! That dollar sign is just there to indicate that this is a command you should type into the command line. If you were following that instruction, you'd type

```
pwd
```

at a command prompt, press enter, and see the output.

## Navigating the Filesystem

When using the terminal, at any given point your terminal session "exists" within a specified directory. This is called your **working directory**. The output of many commands depends on your working directory. Initially, your working directory is your **home directory**, which is typically where all of your files are stored.

To see your current working directory, execute the `pwd` command (print working directory):

```sh
jlittle@my-computer$ pwd
/Users/jlittle
```

To see the files that are in your current directory, use the `ls` command (list files):

```sh
jlittle@my-computer$ ls
```

This will output all the files and folders that are in your current directory.

```
Applications  Code  Creative Cloud Files  Desktop  Developer
Documents  Downloads  Library  Movies  Music  Pictures  Public
```

To change from one place to another, use `cd` (change directory):

```sh
jlittle@my-computer$ cd Code/bowpress
jlittle@my-computer$ pwd
/Users/jlittle/Code/bowpress
```

