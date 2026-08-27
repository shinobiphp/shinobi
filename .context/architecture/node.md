# Shinobi Node Architecture

## Purpose

A Shinobi node hosts composable App Scrolls and exposes them through deployment-defined listeners and routes. The node runtime is independent of optional edge infrastructure.

## Runtime

```text
Config Scrolls
    |
    +-- listeners
    +-- routes
    |
    v
Shinobi Node
    |
    +-- Swoole HTTP listener
    +-- Binding Resolver
    +-- Application Resolver
    |
    v
App Scroll
    |
    v
Codejitsu runtime
```

The MVP runs Swoole directly. Nginx, Caddy, Traefik, Cloudflare, or another edge may later sit in front of the node for TLS termination, authentication, WAF, rate limiting, tenant routing, or container isolation.

## Deployment

Listeners describe where the node accepts traffic. Routes describe how requests are mapped to App Scrolls.

```neon
listeners:
    - transport: http
      address: 127.0.0.1
      port: 9501

routes:
    - transport: http
      hosts:
          - shinobiforge.local
          - www.shinobiforge.local
          - auth.shinobiforge.local
      paths:
          - /
      app: app://forge#1.0
```

The same App Scroll may be targeted by any number of routes. A route may match multiple hosts where the deployment semantics require it.

## Application composition

App Scrolls are logical resources. Deployment never becomes part of the App Scroll identity.

```text
app://shinobi#1.0
       |
       +--> app://archiq#1.0
       |
       +--> app://kage#1.0
                  |
                  v
            app://sensei#1.0
```

The MVP must use deterministic composition and reject cyclic inheritance. If multiple inheritance cannot be represented without ambiguous precedence, use explicit composition or a deterministic ordered parent model rather than implicit conflict resolution.

## CLI

The CLI is supplied by Codejitsu. Shinobi contributes Command Scrolls.

```text
bin/$project_name
    -> Codejitsu CLI
       -> Codex
          -> Command Scrolls
             -> Shinobi node/application services
```

The node command surface includes lifecycle, inspection, application management, and generators. No command should duplicate domain/runtime behavior that belongs to the node or application services.

## Edge boundary

The edge is optional and is not part of the node's core request model:

```text
Internet
   |
   v
[optional edge]
   |
   v
Shinobi Node :9501
   |
   v
App Scroll
```

The edge may be provider-swappable. Provider-specific configuration belongs to deployment infrastructure, not App Scrolls.
