# Shinobi Platform

## Project Overview

Shinobi is a next-generation enterprise platform built with PHP. It allows businesses to assemble, scale, and enhance their digital capabilities instantly. It’s a modular, resilient, and secure foundation for building smarter services, faster—without the headaches of managing complex infrastructure.

The platform is built on a modular architecture, with a core kernel that orchestrates various packages responsible for specific functionalities. These packages include:

*   **Core:** The foundation of Shinobi, responsible for the boot chain, dependency injection, and lifecycle management.
*   **CSR (Contextual State Repository):** Stores domain models and contextual state.
*   **Discovery:** Automatically discovers commands, routes, handlers, events, and components.
*   **Cognition:** Provides AI-driven perception and reasoning modules.
*   **Orchestration:** Manages workflows, sagas, and contextual task orchestration.

## Building and Running

### Prerequisites

*   PHP
*   Composer

### Installation

1.  Clone the repository.
2.  Install the dependencies using Composer:

    ```bash
    composer install
    ```

### Running

The project is run using the `bin/shinobi` command-line interface. To see a list of available commands, run:

```bash
./bin/shinobi
```

## Specification

A formal specification for the Shinobi platform is available in [docs/SPEC.md](./docs/SPEC.md).

## Development Conventions

### Coding Style

The project follows the PSR-12 coding style guide.

### Testing

*TODO: Add instructions on how to run the test suite.*
