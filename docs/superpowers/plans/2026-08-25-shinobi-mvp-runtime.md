# Shinobi MVP Runtime Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the first Shinobi vertical slice that resolves `app://shinobi`, validates authoritative `config://shinobi`, starts an OpenSwoole runtime, and accepts one transport-driven invocation through Codejitsu.

**Architecture:** Shinobi is an application runtime above Codejitsu. `app://` is the composition root; application manifests reference configuration, capabilities, and commands. The runtime is transport-agnostic, with OpenSwoole as the initial long-running substrate and NATS as the first transport target.

**Tech Stack:** PHP 8.4+, Codejitsu, OpenSwoole, PHPUnit/Pest as established by the repository, NEON Scrolls, Composer.

**Spec:** `.context/architecture/overview.md`, `.context/architecture/applications.md`, `.context/architecture/configuration.md`, `.context/architecture/runtime.md`, `.context/architecture/transports.md`.

## Global Constraints

- `declare(strict_types=1);` in every PHP file.
- Public classes are behavioral contracts; internal interfaces are for internal wiring only.
- Do not introduce `Interface`, `Helper`, or `Manager` suffixes without a concrete reason.
- Prefer factories, strategies, composition, and immutable DTOs over inheritance-heavy designs.
- Codejitsu remains the generic resource/discovery/execution substrate.
- `app://` is the Shinobi application composition root.
- `config://shinobi` is authoritative desired node configuration.
- Environment values provide interpolation/secrets, not structural overrides.
- CLI controls lifecycle/inspection, not a second configuration system.
- Transports adapt external protocols into a common invocation/event boundary.
- OpenSwoole is the initial runtime substrate, not a protocol.
- Runtime startup validates the application graph and configuration before starting listeners/workers.

---

### Task 1: Establish the Shinobi application manifest contract

**Files:**
- Create: `scrolls/apps/shinobi.app`
- Create: `scrolls/configs/shinobi.config`
- Create: `scrolls/schemas/shinobi.schema`
- Test: `tests/Application/ApplicationScrollTest.php`

**Interfaces:**
- Consumes: Codejitsu Scroll discovery and URI resolution.
- Produces: `app://shinobi#1.0` referencing `config://shinobi#1.0` and the base capability/command resources.

- [ ] **Step 1: Write the failing test**

```php
it('discovers the Shinobi application manifest', function (): void {
    $app = $this->codex->get('app://shinobi#1.0');

    expect($app->uri()->toString())->toBe('app://shinobi#1.0');
    expect($app->references())->toContain('config://shinobi#1.0');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `composer test -- --filter ApplicationScrollTest`
Expected: FAIL because the Shinobi application Scroll does not yet exist.

- [ ] **Step 3: Write the minimal Scroll resources**

```neon
# app://shinobi#1.0
name: shinobi
version: 1.0
config:
    - config://shinobi#1.0
```

Add the smallest schema needed to validate the application manifest.

- [ ] **Step 4: Run the focused test**

Run: `composer test -- --filter ApplicationScrollTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add scrolls tests/Application/ApplicationScrollTest.php
git commit -m "feat: add Shinobi application manifest"
```

---

### Task 2: Implement resolved application composition

**Files:**
- Create: `src/Application/ResolvedApplication.php`
- Create: `src/Application/ApplicationResolver.php`
- Test: `tests/Application/ApplicationResolverTest.php`

**Interfaces:**
- Consumes: `app://...` Scrolls from Codejitsu.
- Produces: `ResolvedApplication` containing deterministic resource references.

- [ ] **Step 1: Write failing tests for extension composition**

```php
it('resolves a child application through its parent', function (): void {
    $resolved = $this->resolver->resolve('app://archiq#1.0');

    expect($resolved->applications())->toContain('app://shinobi#1.0');
    expect($resolved->resources())->toContain('config://shinobi#1.0');
    expect($resolved->resources())->toContain('config://archiq#1.0');
});

it('rejects circular application inheritance', function (): void {
    expect(fn () => $this->resolver->resolve('app://cycle-a#1.0'))
        ->toThrow(LogicException::class);
});
```

- [ ] **Step 2: Run focused tests and verify failure**

Run: `composer test -- --filter ApplicationResolverTest`
Expected: FAIL because the resolver does not exist.

- [ ] **Step 3: Implement deterministic graph resolution**

Resolve parents recursively, maintain a visited URI set, reject cycles, and preserve declaration order. Do not perform arbitrary deep merges; resource-type-specific merge rules are explicit.

- [ ] **Step 4: Run focused tests**

Run: `composer test -- --filter ApplicationResolverTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add src/Application tests/Application/ApplicationResolverTest.php
 git commit -m "feat: resolve Shinobi application graphs"
```

---

### Task 3: Define and validate `config://shinobi`

**Files:**
- Modify: `scrolls/configs/shinobi.config`
- Modify: `scrolls/schemas/shinobi.schema`
- Create: `src/Configuration/ShinobiConfiguration.php`
- Test: `tests/Configuration/ShinobiConfigurationTest.php`

**Interfaces:**
- Consumes: resolved `config://shinobi#1.0` data.
- Produces: immutable validated runtime configuration consumed by the runtime factory.

- [ ] **Step 1: Write failing validation tests**

```php
it('requires a runtime engine', function (): void {
    expect(fn () => $this->configuration->from(['transports' => []]))
        ->toThrow(InvalidArgumentException::class);
});

it('accepts environment interpolation without changing structure', function (): void {
    $configuration = $this->configuration->from([
        'runtime' => ['engine' => 'openswoole'],
        'transports' => ['nats' => ['servers' => ['${NATS_URL}']]],
    ]);

    expect($configuration->runtime()->engine)->toBe('openswoole');
});
```

- [ ] **Step 2: Run focused tests**

Run: `composer test -- --filter ShinobiConfigurationTest`
Expected: FAIL.

- [ ] **Step 3: Implement minimal typed configuration**

Support runtime engine, worker count, and enabled transport definitions. Keep environment interpolation separate from structural validation.

- [ ] **Step 4: Run focused tests**

Run: `composer test -- --filter ShinobiConfigurationTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add scrolls src/Configuration tests/Configuration/ShinobiConfigurationTest.php
git commit -m "feat: add Shinobi node configuration"
```

---

### Task 4: Establish the runtime/transport boundary

**Files:**
- Create: `src/Runtime/Runtime.php`
- Create: `src/Runtime/OpenSwooleRuntime.php`
- Create: `src/Transport/Transport.php`
- Create: `src/Transport/TransportFactory.php`
- Test: `tests/Runtime/RuntimeTest.php`

**Interfaces:**
- Consumes: `ShinobiConfiguration`.
- Produces: a long-running runtime that owns transport lifecycle.

- [ ] **Step 1: Write failing lifecycle tests**

```php
it('does not start transports before configuration is valid', function (): void {
    $runtime = $this->runtime->from($this->invalidConfiguration);

    expect(fn () => $runtime->start())->toThrow(InvalidArgumentException::class);
});
```

- [ ] **Step 2: Run focused tests**

Run: `composer test -- --filter RuntimeTest`
Expected: FAIL.

- [ ] **Step 3: Implement the boundary**

Keep `Runtime` responsible for process/event-loop lifecycle and `Transport` responsible for protocol lifecycle. Do not put NATS-specific code in the runtime.

- [ ] **Step 4: Run focused tests**

Run: `composer test -- --filter RuntimeTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add src/Runtime src/Transport tests/Runtime/RuntimeTest.php
git commit -m "feat: establish Shinobi runtime boundary"
```

---

### Task 5: Add the first NATS transport

**Files:**
- Create: `src/Transport/NatsTransport.php`
- Create: `src/Message/Invocation.php`
- Create: `src/Message/Result.php`
- Test: `tests/Transport/NatsTransportTest.php`

**Interfaces:**
- Consumes: NATS messages and transport configuration.
- Produces: immutable invocation/result messages for the Kernel boundary.

- [ ] **Step 1: Write the failing message translation test**

```php
it('translates an invocation message into the internal envelope', function (): void {
    $invocation = $this->transport->decode('{"target":"capability://hello#1.0","arguments":{"name":"B"}}');

    expect($invocation->target()->toString())->toBe('capability://hello#1.0');
    expect($invocation->arguments())->toBe(['name' => 'B']);
});
```

- [ ] **Step 2: Run the focused test**

Run: `composer test -- --filter NatsTransportTest`
Expected: FAIL.

- [ ] **Step 3: Implement NATS translation and lifecycle**

Use the project's existing NATS dependency if present; otherwise add the smallest maintained client dependency. Keep connection, subscription, decode, encode, and shutdown concerns inside the transport.

- [ ] **Step 4: Run the focused test**

Run: `composer test -- --filter NatsTransportTest`
Expected: PASS. If an integration test requires a live NATS server, mark it separately and run it with an explicit integration environment.

- [ ] **Step 5: Commit**

```bash
git add src/Transport src/Message tests/Transport/NatsTransportTest.php composer.json composer.lock
git commit -m "feat: add NATS transport"
```

---

### Task 6: Connect the Kernel to application/runtime startup

**Files:**
- Create: `src/Kernel.php`
- Create: `src/Shinobi.php`
- Test: `tests/KernelTest.php`

**Interfaces:**
- Consumes: application URI, `ApplicationResolver`, configuration, runtime factory.
- Produces: a bootable Shinobi runtime.

- [ ] **Step 1: Write the failing boot test**

```php
it('boots the Shinobi application before starting the runtime', function (): void {
    $shinobi = Shinobi::boot('app://shinobi#1.0');

    expect($shinobi->application()->uri()->toString())->toBe('app://shinobi#1.0');
});
```

- [ ] **Step 2: Run the focused test**

Run: `composer test -- --filter KernelTest`
Expected: FAIL.

- [ ] **Step 3: Implement the smallest lifecycle**

Provide application resolution, discovery/loading through Codejitsu, configuration validation, and runtime construction. `run()` starts the configured runtime; it does not hard-code HTTP or NATS behavior.

- [ ] **Step 4: Run focused tests**

Run: `composer test -- --filter KernelTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add src tests/KernelTest.php
 git commit -m "feat: add Shinobi kernel lifecycle"
```

---

### Task 7: Add the Shinobi CLI entry point

**Files:**
- Create: `bin/shinobi`
- Test: `tests/SmokeTest.php`

**Interfaces:**
- Consumes: `app://...` application URI and lifecycle commands.
- Produces: a process that can validate or start the configured application.

- [ ] **Step 1: Write the failing smoke test**

```php
it('validates the default Shinobi application', function (): void {
    $result = shell_exec(PHP_BINARY . ' bin/shinobi validate app://shinobi#1.0 2>&1');

    expect($result)->toContain('valid');
});
```

- [ ] **Step 2: Run it and verify failure**

Run: `composer test -- --filter SmokeTest`
Expected: FAIL because `bin/shinobi` does not exist.

- [ ] **Step 3: Implement `validate` and `run` commands**

`validate` resolves and validates without starting listeners. `run` performs the full lifecycle and starts the configured runtime.

- [ ] **Step 4: Run smoke and full suite**

Run: `composer test`
Expected: PASS.

Run: `./bin/shinobi validate app://shinobi#1.0`
Expected: successful validation.

- [ ] **Step 5: Commit**

```bash
git add bin tests/SmokeTest.php
 git commit -m "feat: add Shinobi runtime CLI"
```

---

### Task 8: Prove the end-to-end vertical slice

**Files:**
- Modify: `tests/SmokeTest.php`
- Create: `bin/smoke`

**Interfaces:**
- Consumes: configured Shinobi application and NATS transport.
- Produces: proof that an invocation reaches Codejitsu and returns a result.

- [ ] **Step 1: Write the failing integration assertion**

```php
it('executes a configured capability through the Shinobi runtime', function (): void {
    $result = $this->invokeThroughTransport('capability://shinobi/health#1.0');

    expect($result->success())->toBeTrue();
});
```

- [ ] **Step 2: Run the integration test**

Run: `composer test -- --filter SmokeTest`
Expected: FAIL until the full runtime/transport path is connected.

- [ ] **Step 3: Connect transport → invocation → Kernel → Codejitsu → result**

Keep every boundary explicit. Do not bypass the Codex or call capabilities directly from the NATS transport.

- [ ] **Step 4: Run all verification**

Run: `composer test`
Expected: PASS.

Run: `./bin/smoke`
Expected: all checks pass.

- [ ] **Step 5: Commit**

```bash
git add bin/smoke tests/SmokeTest.php
 git commit -m "test: prove Shinobi runtime vertical slice"
```

---

## Completion Criteria

The MVP is complete when all of the following are true:

- `app://shinobi#1.0` resolves from Scroll discovery.
- Application composition is deterministic and cycle-safe.
- `config://shinobi#1.0` is authoritative and schema-validated.
- OpenSwoole provides the long-running runtime substrate.
- Transport lifecycle is independent from runtime lifecycle.
- NATS can be enabled from configuration without changing Kernel code.
- A transport invocation reaches a Codejitsu capability through the common runtime boundary.
- `bin/shinobi validate app://shinobi#1.0` succeeds.
- `composer test` is green.
- `./bin/smoke` is green.
