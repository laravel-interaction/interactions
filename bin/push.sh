#!/usr/bin/env bash

set -e
set -x

for PACKAGE in subscribe support vote bookmark like; do
  git subtree push --prefix=packages/$PACKAGE git@github.com:laravel-interaction/$PACKAGE.git master
done
