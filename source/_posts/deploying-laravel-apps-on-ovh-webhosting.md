---
extends: _layouts.post
title: "Deploying Laravel apps on OVH WebHosting"
author: "Grzegorz Różycki"
date: 2022-05-08
---

# Deploying Laravel apps on OVH WebHosting

Sometimes you're faced with some limitations, that you must work around.
Today I'll be talking about some struggles that we encountered
when trying to deploy a Laravel 8 application on OVH WebHosting.

The solution we used uses Deployer 7 and NVM. Read on if you wish to see
how we configure d the deployment process.

## Table of contents

- [Restrictions and background](#restrictions-and-background)
- [Tools](#tools)
- [Deployer configuration](#deployer-configuration)
- [Summary](#summary)

<a name="restrictions-and-background"></a>

## Restrictions and background

As with any web hosting environment you don't have much control over it.
We do have access to an `SSH` connection, so that's a good start.
There is an old version of `node` (10.24.0) and no `npm` installed.
You can't install or update the binaries available globally,
but nothing restricts you from using local binaries that are newer.

But instead of installing node and npm binaries locally ourselves we'll
use NVM to do that.

<a name="tools"></a>

## Tools

- [Deployer](https://deployer.org/) is a deployment tool written in PHP.
  We'll be using version 7 which is currently the newest one, but it's still an RC version.
  Sadly at the moment the documentation is pretty sparse when it comes to v7.
- [NVM](https://github.com/nvm-sh/nvm) is a version manager for node.
  It has the benefit of installing the `node` and `npm` binaries locally.

For both tools you can follow the official documentation on how to install them.

But basically deployer needs to be added as a composer dev dependency by running `composer require --dev deployer/deployer:7.0.0-rc.8`.

You should install Deployer on your local machine whereas NVM should be installed on the web host.

Verify that you have the tools configured properly.
Deployer should be installed as a project dev dependency, so you should be able to run it like so:
`vendor/bin/deployer.phar`. This should list the available commands. We want to check
if the SSH connection is set up correctly. For that run `php vendor/bin/deployer.phar ssh [host-name]`.
You should connect to your web host. There verify that NVM works by typing `nvm --version`.

If everything went well you can continue with configuring the deployment.

<a name="deployer-configuration"></a>

## Deployer configuration

Deployer 7 added support for configuring deployments via YAML files. We'll use this feature.

Here's the full configuration file, we'll dissect it in a moment. 

```yaml
# deploy.yaml
import:
  - recipe/laravel.php
  - contrib/npm.php

config:
  application: 'replace-with-your-app-name'
  bin/php: '/usr/local/php8.0/bin/php'
  default_stage: 'dev'
  default_timeout: 300
  http_user: 'username'
  remote_user: 'username'
  repository: 'git@your.project/repository.git'
  writable_mode: 'chmod'

hosts:
  dev:
    hostname: 'replace_with_your_hostname.hosting.ovh.net'
    deploy_path: '/replace/with/your/project/path'

tasks:
  nvm:build:
    - run: 'nvm use stable'
    - cd: '{{release_or_current_path}}'
    - run: 'npx browserslist@latest --update-db'
    - run: 'npm install'
    - run: 'npm run prod'

  deploy:
    - 'deploy:prepare'
    - 'deploy:vendors'
    - 'artisan:storage:link'
    - 'artisan:view:cache'
    - 'artisan:config:cache'
    - 'artisan:migrate'
    - 'nvm:build'
    - 'deploy:publish'

after:
  deploy:failed: 'deploy:unlock'


```


### The `import` section

Here we're pulling in recipes required to deploy a laravel application.
Those recipes are included with deployer. In addition, you can write your own.

### The `config` section

It's configuration shared between all entries in `hosts`

### The `hosts` section

Here we define deployment targets. Those are configured virtual hosts, that serve your application.
Each entry in the hosts map is a separate target. There can be multiple targets configured
e.g. `dev`, `stage`, `prod`.

One note here: deployer creates a specific directory structure when deploying your application
for the first time.

```
lrwxrwxrwx   1 gxovlhu users 11 May 21 17:00 current -> releases/95
drwxr-xr-x+  3 gxovlhu users  8 May 21 17:02 .dep
drwxr-xr-x+ 12 gxovlhu users 12 May 21 17:04 releases
drwxr-xr-x+  3 gxovlhu users  4 Apr 23 09:07 shared
```

- `.dep` contains deployer metadata required to manage deployments. 
- `releases` contains deployed versions of your app. 
- `current` is a symbolic link to the last deployed application.
- `shared` contains files that should be shared between applications.
Those are for instance the contents of `storage` directory and `.env` file.

Because of this structure you should configure your virtual host web root
to point to the `current` directory.


<a name="summary"></a>

## Summary

As you saw it's not that difficult to configure `deployer`. We were able
to work around the limitations of OVH shared hosting.
It can take some trial and error, but the time invested will surely pay off.
