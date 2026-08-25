# Shinobi Transports

## Principle

A transport is a protocol adapter, not a runtime or application boundary.

All transports translate external input into the same internal invocation/event model and translate results back into the protocol-specific response.

```text
NATS ──┐
HTTP ──┤
gRPC ──┼──> Message / Invocation ──> Kernel ──> Codejitsu
CLI ───┤
... ───┘
```

Transport implementations must not resolve application-specific capabilities directly.

## Configuration

Transports are enabled and configured by `config://shinobi`.

```neon
transports:
    nats:
        enabled: true
        servers:
            - ${NATS_URL}
        subject: shinobi.invoke

    http:
        enabled: false
        host: 0.0.0.0
        port: 9501

    grpc:
        enabled: false
        host: 0.0.0.0
        port: 9502
```

The schema and actual supported transports are implementation details of the current Shinobi version. The configuration model must not require all transports to exist.

## NATS First

NATS is the first transport target because Shinobi's initial service-node model is event-driven and message-oriented. The MVP should prove:

```text
start Shinobi
    ↓
connect NATS
    ↓
receive invocation
    ↓
resolve capability
    ↓
execute through Codejitsu
    ↓
publish result
```

HTTP and gRPC should remain future adapters unless the application configuration explicitly enables them.

## Lifecycle

Transports are constructed only after application resolution and configuration validation. Runtime shutdown must stop transports before releasing the underlying event-loop/runtime resources.

Transport startup failures are fatal unless the configuration explicitly marks that transport optional in a future schema. Silent fallback to another transport is forbidden.

## Message Boundary

Transport payloads should be normalized into immutable messages before entering capability execution. The normalized message carries the invocation identity, target resource URI, arguments, trace information, and relevant policy/context data.

Results and failures are normalized before being encoded by the transport.
