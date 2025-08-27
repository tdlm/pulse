#!/usr/bin/env bash

set -e

npx wp-env run cli --env-cwd=wp-content/plugins/pulse composer \
  "${@:--help}"