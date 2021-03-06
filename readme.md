# ![monkey-toy-64-118729](https://cloud.githubusercontent.com/assets/33978/26260020/4ca62144-3c91-11e7-893b-faac97966f65.png) MiniShift

Inspired by [OpenShift](https://www.openshift.com/)

## WIP

This is meant as a tiny setup to provide a staging server infrastructure.

Web sites are managed as git repositories and published to a web directory on every "git push".

NOT meant for any production like environments!

## Installation

* Setup your server to serve git repositories for ssh users as explained in [The Book](https://git-scm.com/book/en/v2/Git-on-the-Server-Setting-Up-the-Server) (only setup the git user - we will do the rest)
* Install [Composer](https://getcomposer.org) and [Yarn](https://yarnpkg.com)
* Clone this repository "somewhere" to your server.<br />
It is assumed here that you clone to the directory `/git` at your servers root.<br />
Otherwise change `/git` to your chosen path.
* `cd /git`
* `composer install`
* `yarn install`
* `./bin/console doctrine:database:create`
* `./bin/console doctrine:schema:update --force`
* `./bin/console doctrine:fixtures:load`
* `yarn run encore production`
* You **may** create a symlink like<br />
  `ln -s /git/bin/console /usr/bin/minishift`
* Set The servers web root to `/git/web`.<br />
 We don't want to do anything else with this server ;)

If you don't have to take that much care about security, you might also want to change your web servers user to the git user... or fiddle with permissions.

Here is an [example](https://gist.github.com/elkuku/9b1137e9b817e79454e3dbd2f1bd4373) of what I did to my (debian flavour) server.

## Usage

Create a new project on your server<br />
`# /git/bin/console new testOne`

On your local machine do<br />
`$ git clone git@{IP_OR_SERVER}:/git/repo/testOne.git`

Add and change files then<br />
`$ git push`

Your site should be published at<br /> 
`http:/{IP_OR_SERVER}/testOne/`

Add and change more files then<br />
`$ git push`

Rinse and repeat...

### Commands
It is assumed that you created a symlink from `/git/bin/console` to be used as `minishift`

* Create Project<br />
`minishift new {project}`
* Delete Project<br />
`minishift rm {project}`
* List Projects<br />
`minishift info`

## Directory structure
```
├── bin
│   └── Helper scripts
├── repo
│   └── The bare git repositories
├── tpl
│   └── Template files
├── web
│   └── The web root
└── work
    └── The working copies
```

## Extras

### Web root

The following locations will be scanned for usage as web root in the following order:

1. `/web`
1. `/www`
1. `/public`
1. `/` - The project root (for "old school style projects like Joomla!...)"

### Composer

If a `composer.json` file is found at the project root,`composer install` will be executed.

`@todo` Add a flag to avoid this.

### Bower

If a `bower.json` file is found at the project root,`bower install` will be executed.

`@todo` Add a flag to avoid this.

### Yarn

If a `yarn.lock` file is found at the project root,`yarn install` will be executed.

`@todo` Add a flag to avoid this.

## Limitations

* Only PHP projects (yet) - `@todo` add more
* Lots of other features - as seen on [OpenShift](https://www.openshift.com/)...

## Credits

* Icon: http://www.freefavicon.com

----

hF,<br />
`=;)`
