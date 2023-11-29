/**
 * This script uploads a specified bundle to the WP Engine server.
 */

import yargsInit from "yargs";
import inquirer from "inquirer";
import sftpClient from "ssh2-sftp-client";
import ora from "ora";

const bundles = {
  "orient-theme": {
    localPath: "./src/orient-theme",
    remotePath: "/wp-content/themes/orient-theme",
  },
  "orient-home-pages": {
    localPath: "./src/orient-home-pages",
    remotePath: "/wp-content/plugins/orient-home-pages",
  },
  "orient-image-handling": {
    localPath: "./src/orient-image-handling",
    remotePath: "/wp-content/plugins/orient-image-handling",
  },
  "orient-taxonomies": {
    localPath: "./src/orient-taxonomies",
    remotePath: "/wp-content/plugins/orient-taxonomies",
  },
};

const inputs = [
  {
    type: "input",
    name: "username",
    message: "SFTP Username",
    alias: "u",
  },
  {
    type: "password",
    name: "password",
    message: "SFTP Password",
    alias: "p",
  },
  {
    type: "checkbox",
    name: "bundle",
    message: "Bundles to upload",
    alias: "b",
    choices: Object.keys(bundles),
  },
];

const yargs = yargsInit(process.argv.slice(2));

const argv = yargs
  .options(
    Object.fromEntries(
      inputs.map(({ name, message, alias, choices }) => [
        name,
        {
          description: message,
          requiresArg: true,
          alias,
          choices,
        },
      ])
    )
  )
  .version(false)
  .help("help")
  .alias("help", "h").argv;

const promptForMissingArgs = async () =>
  inquirer.prompt(inputs.filter(({ name }) => !argv[name]));

// Use Yargs arguments and Inquirer prompts
const run = async () => {
  console.log("👋 This application uploads bundles to WPEngine.");
  const config = { ...argv, ...(await promptForMissingArgs()) };
  const sftp = new sftpClient();

  const sftpConfig = {
    host: "bowdoinoriedev.sftp.wpengine.com",
    port: 2222,
    username: config.username,
    password: config.password,
  };

  try {
    const spinner = ora("Connecting to the server").start();

    await sftp.connect(sftpConfig);

    let uploadedCount = 0;

    sftp.on("upload", (_info) => {
      uploadedCount++;
      spinner.text = `Uploading (${uploadedCount} files so far)`;
    });

    // Upload each selected bundle to the server
    await Promise.all(
      config.bundle.map(async (bundleKey) => {
        const bundle = bundles[bundleKey];
        await sftp.uploadDir(bundle.localPath, bundle.remotePath, {
          filter: (path, _isDirectory) => {
            return ["node_modules", ".git", "sass"]
              .map((exclusion) => path.indexOf(exclusion) === -1)
              .every((a) => a);
          },
        });
      })
    );

    // Disconnect from the SFTP server
    await sftp.end();
    spinner.succeed("Upload complete!");
  } catch (err) {
    console.error(`Error: ${err.message}, ${err}`);
    process.exit();
  }
};

// Run the CLI application
run();
