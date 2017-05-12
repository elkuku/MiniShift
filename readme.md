# MiniShift

Inspired by [OpenShift](https://www.openshift.com/)

## WIP

## Installation

Setup your server to serve git repositories (e.g. [The Book](https://git-scm.com/book/en/v2/Git-on-the-Server-Setting-Up-the-Server))

Clone this repository "somewhere" to your server.
It is assumed here that you clone to the directory `/git` at your servers root and set The servers web root to `/git/web`.

Create a new project on your server
`# /git/bin/init_project testOne`

On your local machine do
`$ git clone git@{IP_OR_SERVER}:/git/repo/testOne.git`

Add and change files then
`$ git push`

Your site should be published at 
`http:/{IP_OR_SERVER}/testOne/`

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

hF
`=;)`
