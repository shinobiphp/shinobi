# Shinobi Architecture Overview

## Purpose

Shinobi is the long-running application runtime and platform built on Codejitsu. Codejitsu owns the generic resource substrate: Scrolls, Codex, URI identity, discovery, resolution, validation, and execution. Shinobi composes those primitives into runnable applications and service/resource nodes.

Shinobi is not a web framework with a Swoole server bolted on. It is an application runtime whose applications are declaratively composed from Scroll resources.

## Core Model

```text
app://shinobi
    ↓ resolve / compose
Application Graph
    ├── config://shinobi
    ├── capability://shinobi
    └── cmd://shinobi
    ↓
Shinobi Kernel
    ↓
configured Runtime
    ↓
configured Transports
    ↓
Codejitsu execution
```

The base application is itself a Scroll. Applications built on Shinobi extend or compose the base application rather than subclassing PHP runtime classes.

```text
app://shinobi#1.0
        ↑
        │ extends
        │
app://archiq#1.0
```

## Resource Model

Codejitsu currently provides resource types including:

- `app://` / `.app`
- `capability://` / `.capability`
- `cmd://` / `.cmd`
- `config://` / `.config`
- `kata://` / `.kata`
- `schema://` / `.schema`
- `skill://` / `.skill`

Shinobi adds semantics around those resources rather than creating a parallel configuration or dependency system.

## Application Composition

An Application Scroll declares the resources that make up an application. `extends` means resource-graph composition, not PHP inheritance.

```text
app://shinobi
  ├── config://shinobi
  ├── capability://shinobi
  └── cmd://shinobi

app://archiq extends app://shinobi
  ├── config://archiq
  ├── capability://archiq
  └── cmd://archiq
```

Resolution produces a concrete application graph before runtime startup. The graph is validated before any long-running transport or worker is started.

## Configuration Authority

The application configuration Scroll is authoritative for desired runtime state. For the Shinobi base application this is `config://shinobi`.

Environment variables may provide deployment-specific values and secrets through interpolation, but do not silently override structural configuration. CLI arguments are operational controls, not an alternate configuration authority.

```text
Scroll configuration
    ↓ authoritative desired state
Environment
    ↓ values / secrets
CLI
    ↓ lifecycle / inspection / operational control
Runtime
```

## Runtime Boundary

The runtime is independent of transport. OpenSwoole is the initial long-running process/event-loop substrate, not the application protocol.

```text
                    ┌── NATS
                    ├── HTTP
Shinobi Runtime ────┼── gRPC
                    ├── CLI
                    └── future transports
                           │
                           ▼
                    Kernel / Invocation
                           │
                           ▼
                     Codejitsu
```

All transports converge on the same internal invocation model. A transport must not contain capability-specific business logic.

## Startup Boundary

The intended lifecycle is:

```text
boot
  ↓
resolve app://...
  ↓
compose application graph
  ↓
resolve config
  ↓
validate configuration
  ↓
construct runtime
  ↓
construct enabled transports
  ↓
start long-running process
```

Failure during application resolution or configuration validation prevents partial startup.

## Design Constraints

- Codejitsu remains independently usable.
- Shinobi owns application/runtime orchestration, not generic Scroll primitives.
- `app://` is the composition root for runnable applications.
- `config://` is authoritative desired state.
- Transports are adapters into the runtime, never the runtime itself.
- OpenSwoole is a substrate selected by runtime configuration, not hard-coded into application definitions.
- Runtime startup is explicit, traceable, and zero-trust by default.
- Long-running state must not leak into short-lived Codejitsu assumptions.
