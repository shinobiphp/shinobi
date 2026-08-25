# Current Roadmap

This roadmap records the current implementation order. It is deliberately biased toward a small, working Shinobi vertical slice before distributed/platform expansion.

## Now — Shinobi MVP Runtime

### 1. Application Scroll

Establish `app://shinobi#1.0` as the base application composition root.

Requirements:

- reference configuration, capabilities, and commands as Scrolls
- resolve the application graph before runtime startup
- support child applications through `extends`
- reject missing parents, cycles, unsupported versions, and ambiguous merges

### 2. Authoritative Node Configuration

Establish `config://shinobi#1.0` as the authoritative desired state for a node.

Requirements:

- versioned schema
- runtime configuration
- transport configuration
- worker/policy configuration
- environment interpolation for values/secrets
- no silent structural overrides from environment or CLI
- validate before startup

### 3. Runtime Boundary

Build the long-running runtime abstraction with OpenSwoole as the initial substrate.

Requirements:

- runtime owns process/event-loop lifecycle
- transport lifecycle is separate
- startup occurs only after application/config validation
- shutdown is explicit
- no HTTP assumption in the core runtime

### 4. Transport Boundary

Build transport adapters behind configuration.

First target: NATS.

Requirements:

- enable/disable from `config://shinobi`
- normalize incoming payloads into immutable invocation/event messages
- resolve capabilities through Codejitsu
- publish normalized results
- keep protocol logic out of the Kernel

### 5. Kernel Vertical Slice

Implement:

```text
boot
  -> resolve app://...
  -> compose
  -> configure
  -> run
```

The first end-to-end proof is:

```text
NATS
  ↓
Invocation
  ↓
Shinobi Kernel
  ↓
Codejitsu capability
  ↓
Result
  ↓
NATS
```

### 6. Operational CLI

Provide minimal lifecycle/inspection commands:

```text
shinobi validate app://shinobi#1.0
shinobi run app://shinobi#1.0
```

The CLI is an operational interface, not an alternate configuration authority.

## Next

- richer application resource graph inspection
- source-aware application/config composition
- context Scroll integration
- signed resource provenance
- durable runtime state and execution tracing
- outbox/retry/compensation infrastructure
- additional transports such as HTTP and gRPC
- worker supervision and isolation

## Later

- distributed/service-node discovery
- cluster orchestration
- cognition/Satori integration
- ArchIQ application integration
- contextual UI
- self-analysis and self-maintenance workflows
- autonomous application evolution

## Relationship to Codejitsu

Codejitsu remains independently usable and owns generic resource/discovery/execution primitives. Shinobi consumes those primitives and owns application composition, long-running runtime lifecycle, transports, and platform orchestration.

## End State

Shinobi is an application runtime where applications are declaratively composed from Scroll resources, can extend one another through deterministic resource graphs, and can run through configured long-lived runtimes and transports without hard-coded product-specific wiring.
