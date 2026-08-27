# ADR-002: Shinobi Node owns transport execution

## Status

Accepted

## Decision

A Shinobi node owns the internal application transport runtime. The MVP uses OpenSwoole HTTP for listening and request execution.

Deployment/configuration scrolls describe endpoint bindings independently of application composition. A binding resolves a transport, host, port, and other endpoint attributes to an App Scroll URI.

An App Scroll may declare an HTTP handler. Handler execution is resolved after endpoint routing and after App Scroll inheritance is composed. The handler receives a Shinobi HTTP request value object and returns a Shinobi HTTP response value object.

External edge software such as Nginx, Caddy, or Traefik is optional node infrastructure. It may terminate TLS, authenticate, enforce policy, or isolate tenants before forwarding to the Shinobi listener, but it is not required by the application model.

## Consequences

- Multiple hosts and ports may resolve to the same App Scroll.
- App Scrolls remain independent of DNS, ports, TLS, and edge-provider configuration.
- The node runtime can be tested without opening a socket by executing requests directly against the node.
- Edge providers can be added later without changing App Scrolls.
