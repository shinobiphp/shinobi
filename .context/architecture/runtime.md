# Shinobi Runtime Architecture

## Runtime vs Transport

Shinobi is a long-running application runtime. A runtime owns process lifecycle, workers, event-loop execution, application state, and transport lifecycle. A transport is only an ingress/egress adapter.

OpenSwoole is the initial runtime substrate. It provides the long-lived process and event-loop mechanics; it does not define the application protocol.

```text
Application Graph
      ↓
Kernel
      ↓
Runtime
      ↓
Transport(s)
      ↓
Invocation / Event
      ↓
Codejitsu
```

## Lifecycle

The target lifecycle is:

```text
Kernel::boot()
    -> resolve(app://...)
    -> compose()
    -> configure()
    -> run()
```

The concrete implementation may use focused collaborators internally, but the public lifecycle should remain small and behavioral.

Startup must resolve and validate the complete application configuration before starting any listener or worker. A malformed application graph must fail before partial runtime startup.

## Application Selection

The runtime should be able to select an application explicitly:

```text
app://shinobi#1.0
app://archiq#1.0
```

`app://archiq` can extend `app://shinobi`; Shinobi does not need special-case knowledge of ArchIQ.

## Configuration

`config://shinobi` is the authoritative desired state for a Shinobi node.

Environment input is limited to value interpolation, secrets, and deployment-specific material. It does not silently replace structural configuration. CLI commands control lifecycle and inspection rather than becoming a second configuration system.

Example shape:

```neon
runtime:
    engine: openswoole

transports:
    nats:
        enabled: true
        servers:
            - ${NATS_URL}
```

The exact schema is an implementation target and must be versioned as a Scroll.

## Transports

Transports are discovered/configured independently of the Kernel. The runtime should be able to enable multiple transports without changing application execution semantics.

Conceptually:

```text
NATS ──┐
HTTP ──┤
gRPC ──┼──> Invocation / Event ──> Kernel ──> Capability
CLI ───┤
... ───┘
```

NATS is the first likely transport for the MVP because it matches the service-node/event-driven direction. HTTP and gRPC remain future adapters, not assumptions in the core runtime.

## Execution Semantics

Transport-specific payloads must be translated into the internal invocation/event model before capability resolution. Capabilities remain Codejitsu resources and are resolved through the Codex.

Commands are asynchronous by default, queries are synchronous, and events are asynchronous. Retries, isolation, compensation, and durable outbox behavior are later runtime concerns and must not be baked into transport implementations.

## Long-Running State

Shinobi is designed for persistent process state. Runtime components must be explicit about lifecycle and reset boundaries. Request/message handling must not assume PHP-FPM-style process isolation.

The runtime should favor immutable messages, scoped execution context, idempotent handlers, and traceable execution metadata.

## Security

Runtime defaults are zero-trust:

- listeners are explicitly enabled
- capabilities are explicitly resolved
- configuration is validated before startup
- transport credentials come from secure value providers
- execution is traceable
- failures do not silently fall through to another capability or transport

Signed resource provenance and auditable execution events are future extensions of this boundary.
