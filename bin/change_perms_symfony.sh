#!/bin/bash

WEB_USER=pi
GIT_USER=git

sudo setfacl -R -m u:${WEB_USER}:rwX -m u:${GIT_USER}:rwX var
sudo setfacl -dR -m u:${WEB_USER}:rwX -m u:${GIT_USER}:rwX var

