# ADR-001: Shinobi Node and CLI Architecture

## Status

Accepted — 2026-08-27

## Decision

Shinobi is a composable node runtime built on Codejitsu. A Shinobi node hosts App Scrolls; Config Scrolls describe listeners and routes that bind external endpoints to applications; Swoole is the first node runtime/listener implementation.

Codejitsu owns the reusable CLI runtime. Its command model is Codex/Scroll driven and uses Symfony Console behind a swappable driver boundary. Shinobi extends the CLI by contributing command Scrolls; it does not fork or subclass the CLI runtime.

A project created from Codejitsu receives a project-local `bin/$project_name` executable, `scrolls/`, and generator stub resources. Composer dependency installation exposes package executables through Composer's normal `vendor/bin` mechanism; project-local binaries are created by the project skeleton, not copied into consuming projects by a normal dependency install.

## Node model

```text
Optional Edge
    |
    v
Shinobi Node
    |
    +-- Swoole listener
    |
    +-- Config Scrolls
    |      |
    |      +-- listeners
    |      +-- routes
    |      +-- bindings
    |
    +-- App Scrolls
           |
           +-- inheritance/composition
           +-- Codejitsu runtime
```

The first MVP does not require an edge proxy. Nginx, Caddy, Traefik, or another edge provider may later terminate TLS, enforce authentication/WAF/rate limits, identify tenants, or route isolated containers before traffic reaches the Shinobi node. Edge infrastructure is optional and must not become part of the App Scroll contract.

## Routing model

A deployment separates listeners from routes.

```neon
listeners:
    - transport: http
      address: 127.0.0.1
      port: 9501

routes:
    - hosts:
          - shinobiforge.online
          - www.shinobiforge.online
          - auth.shinobiforge.online
      paths:
          - /
      app: app://forge#1.0
```

One route may match multiple hosts/ports where the transport semantics permit it, and multiple routes may target the same App Scroll. Route matching is independent of application identity.

The MVP request path is:

```text
HTTP request
  -> listener
  -> route resolver
  -> app://... resource
  -> App Scroll composition
  -> Codejitsu execution
  -> response
```

## App Scrolls

App Scrolls are logical application resources. They are not synonymous with PHP classes and must remain independent of deployment infrastructure.

An App Scroll may inherit/compose another App Scroll. The MVP must establish deterministic composition and preserve the existing application/resource identity model. Multiple inheritance is permitted only if the existing Scroll model can represent deterministic ordering; otherwise use a single inheritance chain plus explicit composition rather than inventing ambiguous precedence.

The intended ecosystem progression is:

```text
Codejitsu
   -> Shinobi
      -> ArchIQ and/or Kage
         -> Sensei
```

ArchIQ and Kage should remain independently useful. Sensei may compose capabilities from both without requiring either application to know about Sensei.

## Config Scrolls

Deployment/config resources are separate from App Scrolls. They own environment-specific concerns such as:

- listeners
- transports
- routes
- hostnames
- ports
- external bindings
- optional edge-provider configuration

Config changes must not require changing App Scroll definitions.

## CLI

The Codejitsu CLI is a reusable runtime with a driver boundary. Symfony Console is the first driver.

Conceptual flow:

```text
bin/$project_name
    -> Codejitsu CLI
       -> Codex
          -> Command Scrolls
             -> CLI driver
```

With no arguments, the CLI discovers command Scrolls and renders the discovered command tree/help. Namespace commands are rendered from discovered children.

Shinobi contributes commands such as:

```text
node:start
node:stop
node:restart
node:reload
node:info
nodes
apps:list
apps:add
apps:remove
apps:info
make:app
make:config
```

Command behavior remains in executable PHP handlers/capabilities where appropriate; the Scroll supplies discoverable command metadata and routing. Do not force every command implementation into a declarative DSL.

## Project creation and stubs

`composer create-project shinobiphp/codejitsu my-app` produces a runnable project with:

```text
my-app/
├── bin/
│   └── my-app
├── scrolls/
├── stubs/
├── src/
└── tests/
```

`composer create-project shinobiphp/shinobi my-node` follows the same project-local executable rule using `bin/my-node`.

Generator stubs are resources that may be supplied by the project, a package, or Codejitsu. Resolution precedence is:

```text
CLI --stub override
    -> project stub
       -> package stub
          -> Codejitsu default stub
```

`--stub` may select a named discovered stub or an explicit local path where the generator contract permits it. Generator machinery is shared; `make:*` commands should not each invent their own stub lookup implementation.

## Non-goals for the first node MVP

- Nginx/Caddy/Traefik integration
- TLS/certificate management
- distributed node clustering
- NATS transport
- tenant isolation beyond route/application mapping
- container orchestration
- full multi-inheritance conflict resolution if the current resource model cannot support it cleanly

The first proof is local HTTP: a Config Scroll maps a host/path to an App Scroll, the Shinobi node listens through Swoole, and the correct application is executed.
