# Shinobi Node MVP Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make Shinobi a runnable local node that discovers App Scrolls and Config Scrolls, routes HTTP requests to the correct application through Swoole, and exposes its node/app/generator lifecycle through the Codejitsu Codex-driven CLI.

**Architecture:** Codejitsu supplies the reusable CLI runtime, Symfony Console driver, Codex, Scroll discovery, and generator/stub primitives. Shinobi supplies node-specific App/Config Scrolls and command Scrolls. The node owns logical routing and Swoole execution; optional edge proxies remain outside the MVP.

**Tech Stack:** PHP 8.4+, Codejitsu, OpenSwoole, Symfony Console, Nette NEON, PHPUnit.

**Spec:** `.context/decisions/ADR-001-shinobi-node-and-cli-architecture.md`

## Global Constraints

- PHP floor: `>=8.4`.
- Public classes are behavioral contracts; use composition and factories/strategies instead of inheritance-heavy framework plumbing.
- Scrolls are data resources and are discovered/indexed through existing Codejitsu source/Codex mechanisms.
- Attributes are for executable PHP discovery; do not invent attribute discovery for data Scrolls.
- Config Scrolls describe deployment; App Scrolls describe applications; do not couple either to Nginx/Caddy/Traefik.
- Swoole is the first Shinobi node listener/runtime; edge proxies are optional and out of MVP scope.
- New behavior is driven by failing behavioral tests before implementation.
- Existing green behavior must remain green.

---

### Task 1: Align Codejitsu CLI with the reusable driver/Codex model

**Files:**
- Modify: `shinobiphp/codejitsu/src/Apps/Cli.php`
- Modify/create: `shinobiphp/codejitsu/src/Console/*` for the CLI driver and Symfony Console adapter
- Modify/create: `shinobiphp/codejitsu/src/Commands/*` for command registration contracts
- Modify/create: `shinobiphp/codejitsu/tests/*` for CLI discovery, namespace rendering, and execution behavior
- Modify: `shinobiphp/codejitsu/composer.json` if the public project skeleton/bin contract requires metadata changes

**Interfaces:**
- Consumes: existing `Kernel`, `ScrollCodex`, `Scrolls\Types\Command`, and CLI intent pipeline.
- Produces: a driver-neutral CLI application contract and a Symfony Console-backed driver; command Scrolls discovered through the Codex are registered without hard-coded command lists.

- [ ] Write failing tests proving a CLI with no arguments renders all discovered root commands and namespace children.
- [ ] Write failing tests proving a discovered command executes through the existing intent/pipeline path.
- [ ] Write failing tests proving the Symfony Console driver can be replaced by another driver implementation at the CLI boundary.
- [ ] Implement the smallest driver contract and Symfony adapter around the existing CLI behavior; preserve current command resolution semantics.
- [ ] Run the focused Codejitsu CLI tests and then the complete `composer test` suite.
- [ ] Commit the Codejitsu CLI slice as an isolated change.

### Task 2: Add Codejitsu project skeleton, project-local executable, and shared stubs

**Files:**
- Modify: `shinobiphp/codejitsu/bin/codejitsu` only if the source bootstrap must become template-safe
- Create/modify: project skeleton resources used by `composer create-project`
- Create: default `stubs/` resources for app/command/config generation
- Modify/create: Codejitsu generator/stub resolver classes and tests
- Modify: Codejitsu documentation covering `create-project`, `bin/$project_name`, and `--stub`

**Interfaces:**
- Consumes: Task 1 CLI, Codex, and command Scroll discovery.
- Produces: `composer create-project shinobiphp/codejitsu my-app` project shape with `bin/my-app`, `scrolls/`, `stubs/`, `src/`, and `tests/`; shared stub resolution with CLI override.

- [ ] Write failing tests for the default project layout and project-local executable naming.
- [ ] Write failing tests for stub precedence: CLI override, project, package, Codejitsu default.
- [ ] Write failing tests for `--stub=<name>` and the supported explicit local path form.
- [ ] Implement shared stub resolution and generation using focused contracts; do not duplicate lookup logic across `make:*` commands.
- [ ] Implement the project skeleton/template substitution so the binary is named from the created project name.
- [ ] Run Codejitsu tests and verify a disposable `composer create-project` smoke project has the expected executable and directories.
- [ ] Commit the project skeleton/generator slice.

### Task 3: Define Shinobi App Scroll loading/composition boundary

**Files:**
- Modify: `src/Application.php` if needed to represent resolved application composition without deployment concerns
- Modify: `src/ApplicationResolver.php`
- Modify: `src/FilesystemApplicationRepository.php`
- Create/modify: App Scroll fixtures under `apps/` or `scrolls/apps/` according to the existing repository convention
- Test: `tests/ApplicationTest.php`
- Test: `tests/ApplicationResolverTest.php`
- Test: `tests/FilesystemApplicationRepositoryTest.php`

**Interfaces:**
- Consumes: existing application repository/resolver and Codejitsu resource identity.
- Produces: deterministic resolution of `app://name#version` to an executable application definition, including the MVP inheritance/composition path.

- [ ] Add a failing behavioral test for loading a concrete App Scroll by logical identity.
- [ ] Add a failing behavioral test for an App Scroll extending a base App Scroll and inheriting its defined behavior/state.
- [ ] Add a failing test for invalid or cyclic inheritance so the resolver fails deterministically.
- [ ] Implement composition using the existing repository/resource boundaries; do not put host/port/listener state into `Application`.
- [ ] Run all application-focused tests and the complete Shinobi suite.
- [ ] Commit the App Scroll slice.

### Task 4: Normalize Config Scroll deployment model into listeners and routes

**Files:**
- Modify: `src/Deployment.php`
- Modify: `src/DeploymentLoader.php`
- Modify: `src/BindingResolver.php`
- Create if required: focused listener/route value objects under `src/`
- Modify: `scrolls/` deployment/config fixture(s)
- Test: `tests/DeploymentLoaderTest.php`
- Test: `tests/BindingResolverTest.php`

**Interfaces:**
- Consumes: existing deployment scroll loading and specificity-based binding resolution.
- Produces: deployment data with listeners and routes; a request tuple such as transport/host/port/path resolves to an App Scroll identity. A route can match multiple hosts and multiple applicable ports without duplicating applications.

- [ ] Add a failing test for one route matching multiple hosts and resolving to one app.
- [ ] Add a failing test for listener/route separation.
- [ ] Add a failing test for route specificity where a specific host/path wins over a wildcard/general route.
- [ ] Implement the minimal normalized deployment representation and preserve current compatibility where possible.
- [ ] Run deployment/binding tests and the complete Shinobi suite.
- [ ] Commit the Config Scroll routing slice.

### Task 5: Implement the Shinobi node runtime over Swoole

**Files:**
- Create: `src/Node.php` or the smallest focused node orchestration contract indicated by the existing architecture
- Create: Swoole HTTP listener/runtime adapter under `src/`
- Create: node lifecycle state handling required by start/stop/reload/info
- Create/modify: `bin/shinobi` or project-local executable bootstrap according to the project naming convention
- Test: focused node/runtime tests, including non-network orchestration tests

**Interfaces:**
- Consumes: deployment loader, binding resolver, application resolver, Codejitsu runtime/application execution boundary.
- Produces: node lifecycle operations and an HTTP request adapter that resolves host/path/port to an App Scroll and returns its response.

- [ ] Add failing tests for node construction from deployment/application dependencies.
- [ ] Add failing tests for lifecycle state transitions and idempotent start/stop behavior.
- [ ] Add a failing integration test using an ephemeral/local port proving an HTTP request with a configured Host reaches the configured App Scroll.
- [ ] Implement the node lifecycle and Swoole adapter without putting deployment parsing into the request handler.
- [ ] Implement `node:start`, `node:stop`, `node:restart`, `node:reload`, and `node:info` against the same node contract.
- [ ] Run the focused node integration tests and the complete Shinobi suite.
- [ ] Commit the runnable node slice.

### Task 6: Register Shinobi CLI commands through Command Scrolls

**Files:**
- Create: Shinobi command Scroll resources under the existing Scroll source layout
- Create: PHP handlers/capabilities only where command behavior needs executable code
- Create/modify: `tests/` CLI command tests
- Modify: `README.md` with the actual command surface

**Interfaces:**
- Consumes: Codejitsu CLI/Codex from Tasks 1-2 and Shinobi node/application/generator services from Tasks 3-5.
- Produces: discovered commands `node:start`, `node:stop`, `node:restart`, `node:reload`, `node:info`, `nodes`, `apps:list`, `apps:add`, `apps:remove`, `apps:info`, `make:app`, and `make:config`.

- [ ] Add failing tests proving all root namespaces/commands are discoverable through the Codex.
- [ ] Add failing tests for each lifecycle command delegating to the node contract rather than reimplementing lifecycle behavior.
- [ ] Add failing tests for `apps:list` and `apps:info` resolving App Scrolls through the repository.
- [ ] Add failing tests for `make:app` and `make:config` accepting `--stub` and using the shared generator machinery.
- [ ] Implement the command Scrolls and minimal handlers.
- [ ] Verify `bin/<project>` with no arguments renders the complete discovered command tree and that namespace help is useful.
- [ ] Run the complete Shinobi suite and CLI smoke tests.
- [ ] Commit the Shinobi CLI slice.

### Task 7: Create the canonical Shinobi App Scroll and node deployment fixture

**Files:**
- Create/modify: `apps/shinobi.app` or the established App Scroll path
- Create/modify: `apps/forge.app` fixture demonstrating extension
- Modify/create: canonical deployment Config Scroll under `scrolls/`
- Modify: `.context/current-state.ctx`
- Modify: `.context/todo.md`
- Add: `.context/decisions/ADR-001-shinobi-node-and-cli-architecture.md`
- Test: end-to-end node/application/deployment acceptance test

**Interfaces:**
- Consumes: all prior tasks.
- Produces: a repository-level demonstration of `app://shinobi#1.0` as a base App Scroll, a derived application, and a deployment that maps multiple hosts to the derived application.

- [ ] Add a failing end-to-end test that loads the canonical deployment and derived App Scroll.
- [ ] Add a failing request test for `shinobiforge.local` and `auth.shinobiforge.local` both resolving to the same App Scroll.
- [ ] Implement the canonical fixture resources.
- [ ] Update context state to record the implemented node MVP and remaining edge-provider work.
- [ ] Run the complete suite and the local node smoke test.
- [ ] Commit the canonical MVP fixture/context update.

### Task 8: Verification and handoff

**Files:**
- Modify only files required by verification findings.

- [ ] Run Codejitsu `composer test` on its CLI branch.
- [ ] Run Shinobi `composer test` on `feat/shinobi-node-mvp` after the Codejitsu branch is available through the local path repository.
- [ ] Run a clean-install smoke test from the resulting project skeleton.
- [ ] Start a local Shinobi node and make requests with multiple Host values.
- [ ] Verify no edge provider is required for the MVP.
- [ ] Verify `.context` reflects the actual implementation rather than proposed future work.
- [ ] Review diffs for unrelated changes before opening PRs.
