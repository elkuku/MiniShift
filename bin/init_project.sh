#!/bin/bash

# Modify
PROJECT=CHANGE_ME_PROJECT
GIT_BASE=/git

# Modify NOT (yet)
GIT_DIR=${GIT_BASE}/repo/${PROJECT}.git
WORK_TREE=${GIT_BASE}/root
PROJECT_DIR=${WORK_TREE}/${PROJECT}

if [ -d "${GIT_DIR}" ]; then
  echo "ERROR: Project already exists!"
  exit 1
else
  echo "Creating project ${PROJECT} in ${GIT_DIR}"
  mkdir ${GIT_DIR}
  pushd ${GIT_DIR} >/dev/null 2>&1
  git --bare init
  cp ${GIT_BASE}/tpl/hooks/post-receive ${GIT_DIR}/hooks/

  # ...

  popd >/dev/null 2>&1
fi

