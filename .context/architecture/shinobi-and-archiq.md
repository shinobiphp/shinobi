# Shinobi and ArchIQ Direction

## Platform Boundary

Codejitsu is the generic resource and execution substrate. Shinobi is the long-running application runtime and platform built above it.

```text
Codejitsu
  ├── Scrolls
  ├── Codex
  ├── URI / Identity
  ├── Discovery
  ├── Resolution
  ├── Validation
  └── Execution
        ↓
Shinobi
  ├── application composition
  ├── runtime lifecycle
  ├── transports
  ├── service/resource nodes
  ├── orchestration
  ├── security/governance
  └── distributed execution
```

Shinobi should add platform behavior without turning Codejitsu's generic resources into an application-specific framework.

## Applications

The application itself is a Scroll resource:

```text
app://shinobi#1.0
```

Its manifest composes the resources required to run the application:

```text
app://shinobi
  ├── config://shinobi
  ├── capability://shinobi
  └── cmd://shinobi
```

Applications built on Shinobi extend the application graph:

```text
app://archiq#1.0
    extends app://shinobi#1.0
```

ArchIQ therefore inherits the Shinobi runtime contract without Shinobi containing ArchIQ-specific runtime logic.

## ArchIQ

ArchIQ is the architecture/code intelligence application that analyzes repositories and produces actionable understanding of structure, dependencies, risks, and modernization paths.

Its future application graph can add:

```text
app://archiq
  ├── config://archiq
  ├── capability://archiq
  └── cmd://archiq
```

ArchIQ can consume and produce machine-addressable resources through Codejitsu, allowing analysis findings to participate in the same Codex/resource graph as source configuration and runtime capabilities.

## Self-Maintaining Goal

The end state is a system whose architecture, source, resource definitions, analysis, and operational context can all be discovered and reasoned about through the same substrate.

Future agents should be able to:

- resolve the application graph
- inspect configuration and capabilities
- analyze source with ArchIQ
- propose or execute changes through capabilities and commands
- validate changes against schemas and tests
- record architectural decisions back into context

The important boundary is that Shinobi knows how to run applications, not how to run individual products such as ArchIQ.
