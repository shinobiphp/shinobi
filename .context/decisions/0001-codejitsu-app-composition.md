# 0001 Codejitsu-Owned Application Composition

Status: Accepted
Date: 2026-09-22

## Context

Shinobi previously implemented its own Application, ApplicationResolver, and filesystem application repository. Codejitsu M1 now owns generic App Scroll discovery, inheritance, Spec conformance, typed reference validation, and EffectiveApplication construction.

Keeping a second application model in Shinobi would duplicate semantics and allow the runtime to disagree with Codejitsu.

## Decision

Shinobi consumes Codejitsu's ApplicationResolver and EffectiveApplication directly.

Shinobi owns the long-running runtime boundary, OpenSwoole server lifecycle, and the Shinobi-specific App Spec/resources.

Runtime startup follows:

```text
load Scroll sources
  -> resolve/compose/validate app://...
  -> obtain EffectiveApplication
  -> configure runtime
  -> start OpenSwoole
```

No listener starts until application resolution and conformance succeed.

The initial POC exposes a minimal HTTP transport only to prove a long-lived server can boot from an App Scroll. HTTP transport semantics are not promoted into the generic Codejitsu application model.

## Consequences

The old Shinobi Application/ApplicationResolver/FilesystemApplicationRepository path is legacy and should no longer be used by the POC.

ArchIQ can later extend app://shinobi and be handed to the same runtime without product-specific branching.

NATS, worker orchestration, deployment bindings, and richer transport configuration remain later runtime work.
