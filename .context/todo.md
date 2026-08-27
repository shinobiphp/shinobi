# Shinobi TODO

> Living implementation checklist. `.context/` records architectural intent; code and tests are authoritative for implemented behavior.
>
> Updated: 2026-08-27

## Current Position

The repository has a green baseline with application resources, application resolution, filesystem application storage, deployment loading, and binding resolution. The next target is the first runnable Shinobi Node MVP.

## Node MVP

- [ ] Make Codejitsu CLI driver/Codex command registration complete.
- [ ] Make Codejitsu project creation produce `bin/$project_name`, `scrolls/`, `stubs/`, `src/`, and `tests/`.
- [ ] Add shared generator/stub resolution with CLI override.
- [ ] Make App Scroll loading and inheritance/composition deterministic and cycle-safe.
- [ ] Model Config Scroll deployment as listeners plus routes.
- [ ] Support multiple hosts/ports/endpoints resolving to one App Scroll.
- [ ] Implement Shinobi node lifecycle.
- [ ] Implement Swoole HTTP listener/request execution.
- [ ] Discover Shinobi commands as Command Scrolls.
- [ ] Add canonical `app://shinobi#1.0` and a derived application fixture.
- [ ] Prove host/path -> App Scroll -> response end to end.

## Shinobi CLI

- [ ] `node:start`
- [ ] `node:stop`
- [ ] `node:restart`
- [ ] `node:reload`
- [ ] `node:info`
- [ ] `nodes`
- [ ] `apps:list`
- [ ] `apps:add`
- [ ] `apps:remove`
- [ ] `apps:info`
- [ ] `make:app`
- [ ] `make:config`
- [ ] `--json` output for machine-consumable inspection commands where appropriate.

## App Scrolls

- [ ] Keep application identity independent of deployment.
- [ ] Support deterministic inheritance/composition.
- [ ] Reject cyclic inheritance.
- [ ] Preserve room for ArchIQ and Kage to extend Shinobi independently.
- [ ] Allow Sensei to compose capabilities from ArchIQ and Kage later.

## Config Scrolls / Deployment

- [ ] Keep listeners independent from routes.
- [ ] Resolve transport + host + port + path to an application.
- [ ] Preserve specificity ordering for general/wildcard/specific routes.
- [ ] Keep edge-provider configuration separate from application resources.

## Optional Edge

Post-MVP:

- [ ] Define an edge-provider boundary.
- [ ] Evaluate Nginx, Caddy, and Traefik providers.
- [ ] Support TLS termination and forwarding metadata.
- [ ] Support authentication/WAF/rate limiting before the core node where appropriate.
- [ ] Support tenant/container routing without changing App Scroll contracts.

## Ecosystem

Post-node MVP:

- [ ] Build `app://shinobi` as the canonical base application.
- [ ] Build ArchIQ on Shinobi.
- [ ] Build Kage on Shinobi where useful.
- [ ] Build Sensei by composing the proven ArchIQ/Kage capabilities.
- [ ] Dogfood the stack through Sensei only after the underlying contracts are stable.

## Context Integrity

- [x] Record node/CLI architecture decision in `.context/decisions/`.
- [x] Add node architecture documentation.
- [x] Keep `.context/current-state.ctx` aligned with Shinobi rather than inherited Codejitsu text.
- [x] Keep `.context/README.md` aligned with Shinobi.
- [x] Keep this TODO focused on Shinobi rather than Codejitsu's internal package roadmap.
- [ ] Update context in the same change whenever an architectural decision or durable runtime contract changes.

## Verification

- [ ] `composer test` remains green.
- [ ] Clean project creation smoke test succeeds.
- [ ] CLI with no arguments lists discovered commands.
- [ ] Node starts/stops/reloads deterministically.
- [ ] Local HTTP request resolves the correct App Scroll by Host/path.
- [ ] Multiple hosts can target one App Scroll.
- [ ] No edge provider is required for the MVP.
