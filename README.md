# Shinobi Platform

Shinobi is a next-generation enterprise platform that allows businesses to assemble, scale, and enhance their digital capabilities instantly. It’s a modular, resilient, and secure foundation for building smarter services, faster—without the headaches of managing complex infrastructure.

**Shinobi evolves as it operates.** It dynamically creates, adds, removes, and combines capabilities in real time, enabling organizations to solve problems, optimize operations, and achieve outcomes that traditional software can’t. Every action is autonomous, self-correcting, and context-aware.

---

## How Shinobi Provides Value

### For Technical Decision-Makers

- Accelerates development with prebuilt, composable business capabilities.
- Reduces operational complexity with federated service nodes and orchestration.
- Enables integration with existing systems via APIs, CLI, or OpenSwoole servers.

### For Engineers

- Modular architecture using ECS, DDD, EDD, and BDD patterns.
- Attribute-based discovery system to auto-register routes, commands, packages, and subsystems.
- Stackable execution layers: synchronous, asynchronous, and federated.

### For Business Stakeholders

- Self-evolving platform that adapts to operational context.
- Zero-trust security by design, auditing and monitoring every operation.
- Contextual, dynamic UI tailored to operator tasks and intent.

---

## Key Features

| Feature                           | Description                                           | Links                                                    |
| --------------------------------- | ----------------------------------------------------- | -------------------------------------------------------- |
| Core Kernel                       | Orchestrates systems, workflows, and communications   | [Core README](packages/core/README.md)                   |
| Contextual State Repository (CSR) | Semantic state storage and querying                   | [CSR README](packages/csr/README.md)                     |
| Capabilities                      | Prebuilt business logic modules                       | [Capabilities README](packages/capabilities/README.md)   |
| Cognition                         | AI-driven perception & reasoning modules              | [Cognition README](packages/cognition/README.md)         |
| Discovery                         | Automatic detection of packages, commands, subsystems | [Discovery README](packages/discovery/README.md)         |
| UI                                | Adaptive, dynamic interface components                | [UI README](packages/ui/README.md)                       |
| Security                          | Zero-trust, monitoring, WAF, policies                 | [Security README](packages/security/README.md)           |
| Orchestration                     | Workflow & lifecycle management                       | [Orchestration README](packages/orchestration/README.md) |
| Messaging                         | Context-aware routing, async & event messages         | [Messaging README](packages/messaging/README.md)         |
| DNA                               | System knowledge, migrations, entities, DTOs          | [DNA README](packages/dna/README.md)                     |

---

## 🧩 Shinobi Packages and Features

| Package                                                | Description                                                  | Core Features / Responsibilities                                                                                                                                                                  |
| ------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [`core`](../packages/core/README.md)                   | Kernel and runtime core — the foundation of Shinobi.         | Boot chain (`boot()->discover()->load()->run()`), dependency injection, environment management, OpenSwoole runtime, lifecycle management, service discovery registration, context initialization. |
| [`csr`](../packages/csr/README.md)                     | Contextual State Repository — the semantic model of Shinobi. | Stores domain models and contextual state, validates system rules, provides identity + authorization context, handles idempotency and replay protection.                                          |
| [`capabilities`](../packages/capabilities/README.md)   | Business capability layer — modular functional units.        | Defines and registers composable domain logic modules; versioned; handles `create`, `update`, `delete`, `get`, `commit`; supports capability federation and self-registration.                    |
| [`cognition`](../packages/cognition/README.md)         | Cognitive subsystem — adaptive decision and learning layer.  | Event-driven reasoning, LLM and inference adapters, self-evolving heuristics, feedback loops, system reflection (introspective self-model).                                                       |
| [`discovery`](../packages/discovery/README.md)         | Attribute- and reflection-based discovery system.            | Auto-discovers commands, routes, handlers, events, and components; provides metadata graph; supports distributed discovery via gRPC.                                                              |
| [`dna`](../packages/dna/README.md)                     | System schema and message definition layer.                  | Proto definitions, entity structures, DTOs, message contracts; acts as single source of truth for structure and semantics.                                                                        |
| [`messaging`](../packages/messaging/README.md)         | Message routing and inter-component communication layer.     | Handles synchronous, asynchronous, and event-based messages; includes message signing, retries, queueing, and circuit-breaking; built over gRPC + OpenSwoole tasks.                               |
| [`orchestration`](../packages/orchestration/README.md) | Process management and temporal workflow engine.             | Defines workflows, sagas, compensations, retries, time-based triggers, and contextual task orchestration across nodes.                                                                            |
| [`security`](../packages/security/README.md)           | Zero-trust security and policy enforcement.                  | Request signing, message verification, identity isolation, permissions graph, and zero-trust boundary enforcement.                                                                                |
| [`ui`](../packages/ui/README.md)                       | Dynamic contextual user interface generation.                | Real-time adaptive UIs, contextual form rendering, rule-based component visibility, semantic layouts, multi-device rendering.                                                                     |
| [`development`](../packages/development/README.md)     | Dev utilities, testing, and automation tooling.              | Developer CLI tools, scaffolding, CI/CD pipelines, integration test harnesses, debug servers.                                                                                                     |
| [`ai`](../packages/ai/README.md)                       | Artificial intelligence integration and reasoning.           | LLM adapters, prompt orchestration, codegen integration (Gemini/Ollama/OpenAI), autonomous improvement of components.                                                                             |
| [`dojo`](../packages/dojo/README.md)                   | Admin control panel and visual management layer.             | UI for nodes, components, and messages; health checks, logs, metrics, and system introspection.                                                                                                   |
| [`shogun`](../packages/shogun/README.md)               | Cluster orchestrator and lifecycle controller.               | Manages federated service nodes, registration, scaling, load distribution, and fault isolation.                                                                                                   |
| [`jonin`](../packages/jonin/README.md)                 | CLI and operational runtime interface.                       | CLI commands for managing nodes, capabilities, and messages; package installation and discovery (`pkg:list`, `pkg:install`).                                                                      |
| [`graphql`](../packages/graphql/README.md)             | GraphQL gateway and federation layer.                        | Auto-generates schema from DNA definitions, routes queries via Message Moderator, supports introspection and federation.                                                                          |
| [`kensho`](../packages/kensho/README.md)               | Observability and analytics.                                 | Telemetry, logging, metrics, tracing, anomaly detection, dashboard generation.                                                                                                                    |
| [`evolution`](../packages/evolution/README.md)         | Self-improvement and autonomous adaptation.                  | Monitors system efficiency, proposes optimizations, generates and tests new capabilities, manages semantic drift.                                                                                 |
| [`forge`](../packages/forge/README.md)                 | Code generation and scaffolding tools.                       | Builds new components, capabilities, and service templates; integrates Gemini CLI for machine-collaborative coding.                                                                               |
| [`graphql`](../packages/graphql/README.md)             | Unified API gateway.                                         | Schema federation, introspection, query execution across federated nodes.                                                                                                                         |
| [`ui`](../packages/ui/README.md)                       | Frontend rendering engine.                                   | Context-aware rendering, SSR via Astro, adaptive layouts via Alpine.js or Livewire.                                                                                                               |

---

## 🧠 Future / Planned Packages

| Package        | Purpose                                        | Key Goals                                                                                     |
| -------------- | ---------------------------------------------- | --------------------------------------------------------------------------------------------- |
| `plugins`      | Third-party extensions and add-ons.            | Allows external developers to extend Shinobi with new capabilities and UI widgets.            |
| `templates`    | Application and component templates.           | Prebuilt boilerplates for fast bootstrapping of new contexts or capabilities.                 |
| `integrations` | Connectors to external APIs and SaaS systems.  | Bridges to CRMs, ERPs, payment gateways, AI services, etc.                                    |
| `connectors`   | System-level communication bridges.            | Handles federated message passing, node sync, and data replication.                           |
| `temporal`     | Temporal / time-based workflow subsystem.      | Scheduled jobs, delayed tasks, timers, and durable state workflows (inspired by Temporal.io). |
| `memory`       | In-memory coordination and caching.            | Local+distributed memory grid; component-level short-term storage.                            |
| `archives`     | Legacy and deprecated modules.                 | Compatibility layer for old versions and retired components.                                  |
| `sandbox`      | Experimental development and AI prototyping.   | LLM-based R&D, speculative features, testbeds for self-evolution.                             |
| `agent`        | Runtime agent and node AI.                     | Handles local decision-making, goal tracking, and event prioritization.                       |
| `nexus`        | Inter-node API and federation router.          | Routes messages between clusters, supports cross-domain authentication.                       |
| `marketplace`  | Capability and module exchange hub.            | Enables publishing, discovery, and monetization of third-party capabilities.                  |
| `temple`       | Knowledge repository and documentation system. | Semantic documentation, versioned knowledge graph, system provenance tracking.                |

---

## 🔗 Cross-Package Feature Map

| Capability              | Provided By                    | Description                                                         |
| ----------------------- | ------------------------------ | ------------------------------------------------------------------- |
| Modular architecture    | `core`, `discovery`, `csr`     | Every subsystem is a self-contained module with explicit contracts. |
| Dynamic UI generation   | `ui`, `csr`, `capabilities`    | Renders contextual interfaces from schema and behavior models.      |
| Temporal workflows      | `orchestration`, `temporal`    | Schedules and manages long-running processes with retries.          |
| Zero-trust security     | `security`, `csr`              | All messages and capabilities signed, verified, and traceable.      |
| Adaptive cognition      | `cognition`, `ai`, `evolution` | Continuous learning, adaptation, and self-improvement.              |
| Distributed messaging   | `messaging`, `shogun`, `nexus` | Context-aware routing across federated nodes.                       |
| Observability & insight | `kensho`, `dojo`               | Logs, metrics, health, and real-time analytics.                     |
| AI co-development       | `forge`, `ai`, `evolution`     | Machine-assisted coding and self-maintaining components.            |
| Declarative discovery   | `discovery`, `core`            | Attribute-based registration of routes, modules, and handlers.      |
| Cluster orchestration   | `shogun`, `jonin`, `csr`       | Auto-scaling, node discovery, and distributed task management.      |

---

**See also:**

- [Human-readable platform spec](./docs/README.md)
- [Formal platform specification](./docs/SPEC.md)
- [Machine-readable spec (Gemini.md)](../Gemini.md)
