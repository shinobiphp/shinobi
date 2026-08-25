# Application Scrolls

## Application as Composition Root

A Shinobi application is identified by an `app://` Scroll. The application manifest declares the resource graph required to run it.

```neon
extends: app://shinobi#1.0

name: archiq

config:
    - config://archiq#1.0

capabilities:
    - capability://archiq#1.0

commands:
    - cmd://archiq#1.0
```

`extends` is composition, not PHP inheritance. Resolution recursively loads the parent application graph, detects cycles, then merges declarations using deterministic precedence.

## Base Application

`app://shinobi#1.0` is the base application contract for the Shinobi runtime. Its graph establishes the resources needed for a normal Shinobi node.

```text
app://shinobi
  ├── config://shinobi
  ├── capability://shinobi
  └── cmd://shinobi
```

Applications may add resources and explicitly override supported declarations. They must not mutate the parent's resource definitions in place.

## Resolution

Application resolution produces a resolved graph before runtime startup:

```text
app://archiq
    ↓
resolve parent
    ↓
app://shinobi
    ↓
merge resource declarations
    ↓
ResolvedApplication
    ↓
validate references
    ↓
construct runtime
```

A graph must reject:

- missing parent applications
- circular `extends` chains
- duplicate declarations with ambiguous precedence
- unsupported resource types
- invalid resource versions

## Precedence

Child declarations override parent declarations only where the resource type defines an explicit merge rule. Otherwise declarations are additive.

This is intentionally stricter than PHP inheritance: application composition must remain inspectable and deterministic.

## Relationship to Configuration

The application manifest identifies configuration resources; it does not embed instance configuration directly.

```text
app://shinobi
    ↓ references
config://shinobi
    ↓ authoritative desired state
runtime
```

This keeps application identity/composition separate from deployment-specific configuration.
