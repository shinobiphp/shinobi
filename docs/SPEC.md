# Shinobi Platform Specification

## 1. Introduction

Shinobi is a next-generation enterprise platform built with PHP. It provides a modular, resilient, and secure foundation for businesses to build and scale their digital capabilities. The platform is designed to enable rapid development and deployment of smart services without the complexities of managing underlying infrastructure.

## 2. Architecture

Shinobi is built on a modular architecture centered around a core kernel. The kernel is responsible for orchestrating various packages, each providing a specific set of functionalities. This modular design allows for flexibility, scalability, and maintainability.

The key architectural principles are:

*   **Modularity:** Each component is a self-contained package with a specific responsibility.
*   **Discoverability:** The platform can automatically discover and register components, such as commands, routes, and events.
*   **Extensibility:** New functionalities can be added by creating new packages.
*   **Configuration:** The platform uses a hierarchical configuration system to manage settings for different environments and tenants.

## 3. Core Components

The Shinobi platform is composed of several core packages:

*   **Core:** The heart of the platform, responsible for:
    *   **Bootstrapping:** Initializing the application and loading all necessary components.
    *   **Dependency Injection:** Managing object creation and dependencies.
    *   **Lifecycle Management:** Handling the application lifecycle, from startup to shutdown.

*   **CSR (Contextual State Repository):** A dedicated component for managing state, responsible for:
    *   **Storing Domain Models:** Persisting and retrieving domain-specific data.
    *   **Contextual State:** Maintaining the state of the application in a given context.

*   **Discovery:** This package provides automatic discovery of various components within the platform, including:
    *   Commands
    *   Routes
    *   Event Handlers
    *   Components

*   **Cognition:** Provides AI-driven capabilities, such as:
    *   **Perception:** Analyzing and understanding data from various sources.
    *   **Reasoning:** Making decisions and taking actions based on the analyzed data.

*   **Orchestration:** Manages complex workflows and processes, including:
    *   **Sagas:** Long-running, multi-step processes.
    *   **Task Orchestration:** Coordinating and managing tasks across different components.

## 4. Command Line Interface

The primary way to interact with a Shinobi application is through the `bin/shinobi` command-line interface (CLI).

To see a list of available commands, run:

```bash
./bin/shinobi
```

## 5. Development Conventions

### 5.1. Coding Style

The project adheres to the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style guide.

### 5.2. Testing

*TODO: Add instructions on how to run the test suite.*
