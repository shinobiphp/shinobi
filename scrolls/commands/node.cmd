name: node
type: command
description: Manage the Shinobi node.
usage: 'node:<subcommand> [arguments] [options]'
commands:
    start:
        description: 'Start the Shinobi node.'
        usage: 'node:start'
        target: Shinobi\\Commands\\NodeStart
    info:
        description: 'Show Shinobi node deployment information.'
        usage: 'node:info'
        target: Shinobi\\Commands\\NodeInfo
