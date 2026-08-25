# Shinobi Configuration

## Authority

The configuration Scroll is the authoritative desired state for a Shinobi node.

For the base application:

```text
config://shinobi#1.0
```

The application manifest references this resource; it does not replace it.

## Sources of Input

Shinobi has three distinct input classes:

1. **Scroll configuration** — authoritative structure and desired state.
2. **Environment** — interpolation, secrets, and deployment-specific values.
3. **CLI** — lifecycle, validation, inspection, and other operational commands.

Environment variables and CLI flags must not silently override structural configuration.

## Example

```neon
runtime:
    engine: openswoole

transports:
    nats:
        enabled: true
        servers:
            - ${NATS_URL}
        subject: shinobi.invoke

workers:
    count: 4

policies:
    execution_timeout: 30000
```

This is illustrative; the versioned schema for `config://shinobi` is part of the MVP implementation.

## Startup

Configuration is resolved and validated before any runtime component starts:

```text
app://shinobi
    ↓
config://shinobi
    ↓
validate against schema
    ↓
resolve environment values
    ↓
construct runtime
    ↓
construct enabled transports
    ↓
start
```

Missing required values, invalid enum values, invalid endpoints, or contradictory settings must fail startup rather than produce a partially initialized node.

## Cascade

Configuration continues to use Codejitsu's source-aware URI model. The logical identity remains `config://shinobi`; source selection controls where the definition is resolved from.

The source model may support patterns such as:

```text
config://shinobi
config://shinobi@tenant.global
config://shinobi@global.tenant
```

The cascade rules belong to Codejitsu's resource resolution layer. Shinobi consumes the resolved configuration and does not reimplement cascade semantics.
