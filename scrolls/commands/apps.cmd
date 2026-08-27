name: apps
type: command
description: Manage Shinobi applications.
usage: 'apps:<subcommand> [arguments] [options]'
commands:
    list:
        description: 'List installed App Scrolls.'
        usage: 'apps:list'
        target: Shinobi\\Commands\\AppsList
