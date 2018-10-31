<h1 align="center">Prathletics</h1> <br>
<p align="center">
    <img alt="prathletics logo" title="prathletics logo" src="logo-prathletics.png" width="300">
</p>
<div align="center">
  <strong>Athletics training plan app</strong>
</div>
<div align="center">
  Web app built with <a href="https://github.com/craftcms/cms">Craft CMS</a> and <a href="https://github.com/vuejs/vue">Vue.js</a> for create and transmit athletics training plan.
</div>

<div align="center">
  <h3>
    <a href="https://github.com/michaelravedoni/prathletics#documentation">Documentation</a>
    <span> | </span>
    <a href="https://ravedoni.com/prathletics">Demo</a>
    <span> | </span>
    <a href="#contributing">
      Contributing
    </a>
  </h3>
</div>

<div align="center">
  <sub>Built with ❤︎ by
  <a href="https://michael.ravedoni.com/en">Michael Ravedoni</a> and
  <a href="https://github.com/michaelravedoni/prathletics/contributors">
    contributors
  </a>
</div>

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Install](#install)
- [Deploy](#deploy)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [Release History](#release-history)
- [Authors and acknowledgment](#authors-and-acknowledgment)

## Introduction
[![Conventional Commits](https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg?style=flat-square)](https://conventionalcommits.org)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](https://github.com/michaelravedoni/prathletics/blob/master/LICENSE)

## Features

- Admin backend for content
- Read-only API
- Client web application (comming soon)

## Install

Follow this instructions to "install" Prathletics.

Clone this repository or [download it](https://github.com/michaelravedoni/prathletics/archive/master.zip) as an archive and decompress-it.

```bash
git clone git://github.com/michaelravedoni/prathletics.git
```

### Craft CMS (backend)

The Craft CMS is used as a headless CMS and is located in the `/cms` folder. This steps  are insired from the [Craft CMS Documentation](https://docs.craftcms.com/v3/installation.html). Read it if necessary.

#### Step 1: Create a Database

Next up, you need to create a database for your Craft project. Craft 3 supports both MySQL 5.5+ and PostgreSQL 9.5+. For example, name it `prathletics`.

#### Step 2: Modify the `.env` variables
Duplicate the `.env.example` and rename it `.env`. Enter your database connection information and others informations.

#### Step 3: Run the Setup Wizard

Finally, it’s time to run Craft’s Setup Wizard. You can either run that from your [terminal](#terminal-setup) or your [web browser](#web-browser-setup).

##### Option A: Terminal Setup

In your terminal, go to your project’s root directory (`/cms`) and run the following command to kick off the Setup Wizard:

```bash
cd cms
./craft setup
```

The command will ask you a few questions to learn how to connect to your database, and then kick off Craft’s installer. Once it’s done, you should be able to access your new Craft site from your web browser.

##### Option B: Web Browser Setup

In your web browser, go to `http://<Hostname>/cms/index.php?p=admin/install` (substituting `<Hostname>` with your web server’s host name pointing to the prathletics project). If you’ve done everything right so far, you should be greeted by Craft’s Setup Wizard.

1. Accept the [license agreement](https://craftcms.com/license);
2. Enter your database connection information;
3. Create an admin account. Don’t be one of _those people_ and be sure to pick a strong password.
4. Define your System Name, Base URL, and Language.
5. Click “Finish up” to complete the setup process.
6. In your web browser, go to `http://<Hostname>/cms/web/admin` and log in.

#### Fill the database

Comming…

### Vue.js (client app)

Install Vue CLI:
```bash
npm install -g @vue/cli
# OR
yarn global add @vue/cli
```

Then install all dependencies :
```bash
cd app
npm install
```

Then run the app:
```bash
npm run serve # for development
npm run build  # for production
```

If you want, you can run, manage and install the app with the Vue.js GUI:
```bash
vue ui # where you want
```
Then you have to duplicate the `.env.model` file in `.env.development` and `.env.production`. Open these files and fill the informations with your own:
```bash
VUE_APP_CRAFT_API_URL=http://example.com/prathletics/cms/web/
VUE_APP_CRAFT_ADMIN_URL=http://example.com/prathletics/cms/web/admin/
VUE_APP_CRAFT_BACKEND_URL=http://example.com/prathletics/cms/web/
VUE_APP_TRAINER_NAME="Trainer Name"
VUE_APP_URL="http://example.com/prathletics"
```

## Deploy
For deploying the app via FTP (RSync), follow this instructions. In the main project folder `/prathletics`, run :
```bash
npm install
```

Rename the `env-model.json` file in `env.json` and open-it. Then fill the `<user>`, `<host>` and `<path/>` with your FTP remote server informations. For example:
```bash
username_example@example.ftp.com:web/prathletics/
```

Then, to deploy the app, run :
```bash
npm run stage      #For testing on your test server
npm run stage-dry  #If you want to run a dry test

npm run deploy     #For the production server
npm run deploy-dry #If you want to run a dry test
```

## Documentation

Comming…

## Contributing

We’re really happy to accept contributions from the community, that’s the main reason why we open-sourced it! There are many ways to contribute, even if you’re not a technical person.

1. Fork it (<https://github.com/michaelravedoni/prathletics/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request

## History and changelog

You will find changelogs in the [CHANGELOG.md](https://github.com/michaelravedoni/prathletics/blob/master/CHANGELOG.md) file. For more detail, you can check the [HISTORY.md](https://github.com/michaelravedoni/prathletics/blob/master/HISTORY.md) file.

## Roadmap

- Localization
- Parameters
- Craft CMS module

## Authors and acknowledgment

* **Michael Ravedoni** - *Initial work* - [michaelravedoni](https://github.com/michaelravedoni)

See also the list of [contributors](https://github.com/michaelravedoni/prathletics/contributors) who participated in this project.

## License

[MIT License](https://opensource.org/licenses/MIT)
