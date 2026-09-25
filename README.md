# Shinobi

Shinobi runs App Scrolls on a long-lived OpenSwoole node. Codejitsu owns
application composition, inheritance, resource resolution, and Spec conformance.
Shinobi owns deployment bindings and HTTP execution.

## Local POC setup

Use PHP 8.5+ with the OpenSwoole extension. Keep Codejitsu beside this checkout:

```text
workspace/
  codejitsu/  # main, including M1 app/spec composition (17737b3 or later)
  shinobi/
```

Composer uses local path repositories for Codejitsu and its packages. The lockfile
pins third-party dependencies, but the symlinked Codejitsu checkout must match the
expected revision; this is a development workspace, not a standalone distribution.

```bash
composer install
composer test
php bin/shinobi validate app://shinobi
php bin/shinobi show app://shinobi
php bin/shinobi run app://shinobi
```

In another terminal:

```bash
curl -i http://localhost:9501/
```

Expect HTTP 200 and JSON containing `name: shinobi`, `status: ok`, method and path.
Stop the server with Ctrl-C before restarting after Scroll or PHP changes.

## Deployment and routing

`scrolls/configs/deployment.config` maps HTTP host/port endpoints to App Scrolls.
`address` is the local listening interface, while `host` matches the HTTP Host
header. Routing uses the listener port, independent of a port in the Host header.
`SHINOBI_HOST` overrides the listening interface; `SHINOBI_PORT` overrides HTTP
binding ports. With no overrides, deployment values are preserved.

The POC opens the first configured listener (the first port if it is a list).
Bindings support multiple host/port values for matching, but multiple simultaneous
listeners and non-HTTP transports are not implemented yet. `run app://name`
validates that app before starting; routing still follows deployment bindings.
To serve a child application, mount its URI in the deployment bindings as well.

The Shinobi spec is `scrolls/specs/shinobi/app.spec`. A child app can extend
`app://shinobi#1.0.0`, inherit its spec and runtime capability, and declare an
invokable PHP handler accepting `Shinobi\HttpRequest` and returning
`Shinobi\HttpResponse`. Unquoted NEON class names use single backslashes.

`composer test` includes isolated HTTP subprocess tests on temporary ports and
cleans up its processes and fixtures. OpenSwoole must be enabled to run that
coverage; without it those tests are skipped. The ArchIQ fixture proves child-app
mounting and execution, not the ArchIQ product implementation.
