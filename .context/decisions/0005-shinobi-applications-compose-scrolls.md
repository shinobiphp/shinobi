# Decision 0005: Shinobi Applications Compose Scroll Resources

## Status

Accepted

## Decision

A Shinobi application is an `app://` Scroll that composes configuration, capability, command, and other supported resource Scrolls. Applications may extend other application Scrolls using `extends`.

`extends` is resource-graph composition, not PHP inheritance.

## Rationale

The application manifest becomes a stable composition root without making the Kernel aware of individual products. `app://archiq` can therefore extend `app://shinobi` without Shinobi containing ArchIQ-specific runtime logic.

The model preserves the existing Codejitsu resource/Codex architecture and makes the application graph inspectable before execution.

## Consequences

- `app://shinobi` is the base Shinobi application contract.
- `config://shinobi` remains the authoritative node configuration.
- Child applications inherit the Shinobi runtime contract through graph composition.
- Application resolution must detect cycles and ambiguous merges before startup.
- Resource-specific merge semantics must be explicit; arbitrary deep merging is not implied.
