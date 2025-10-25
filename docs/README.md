# Shinobi Project Documentation

## Overview

Shinobi is a modular, composable enterprise platform that accelerates software development by providing production-ready capabilities, orchestration, and autonomous systems. The top-level project (`shinobi/`) bootstraps the platform, installs `core` and other packages, and exposes the CLI (`bin/shinobi`) for managing packages and commands.

## Specification

The formal specification for the Shinobi platform can be found in [SPEC.md](./SPEC.md).

## Repository Structure

- **packages/core**: The Core platform (kernel, discovery, orchestration, tenancy)
- **packages/ai**: AI capabilities and cognition interfaces
- **packages/capabilities**: Business capabilities that can be reused or sold
- **packages/cognition**: Contextual reasoning, state, and memory
- **packages/dna**: Declarative system definitions and metadata
- **packages/csr**: Composable service runtime, federated nodes
- **packages/development**: Developer tools, testing, code quality
- **packages/discovery**: Discovery system for commands, packages, subsystems
- **packages/docs**: Documentation and Gemini-readable specs
- **packages/dojo**: Admin / control panel / orchestration UIs
- **packages/evolution**: Self-evolving components & autonomous upgrades
- **packages/forge**: Code generation and automated scaffolding
- **packages/graphql**: GraphQL API bindings
- **packages/jonin**: Access control, identity, and permissions
- **packages/kensho**: Analytics, KPI monitoring, and logging
- **packages/messaging**: Messaging infrastructure
- **packages/orchestration**: Workflow and process orchestration
- **packages/security**: Security, authentication, and zero-trust enforcement
- **packages/sensei**: CLI commands, developer tooling
- **packages/shogun**: Instance orchestration and management
- **packages/ui**: Web interface components

## Installation

1. Clone the top-level repo:

   ```bash
   git clone git@github.com:shinobiphp/shinobi.git
   cd shinobi
   ```

2. Install dependencies:

   ```bash
    composer install
   ```

3. Run CLI:
   ```bash
   ./bin/shinobi
   ```

## Usage

- ./bin/shinobi pkg:list – List available packages
- ./bin/shinobi pkg:install <package> – Install a package
- ./bin/shinobi pkg:update <package> – Update a package
- ./bin/shinobi pkg:remove <package> – Remove a package

## Contribution Guidelines

See packages/development/docs/README.md for testing, coding standards, and conventions.

## Notes for Gemini

- Each package has a [Gemini.md](../Gemini.md) file
- Use top-level docs to discover package dependencies and relationships
- CLI is the primary entry point for orchestration
