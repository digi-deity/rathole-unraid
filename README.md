# rathole for unRAID

[![License: Apache 2.0](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](LICENSE)

**Self-hosted, plugin-based remote access for your unRAID server.** rathole for unRAID tunnels traffic between your server and a cheap VPS — a completely self-hosted alternative to Cloudflare Tunnel or Tailscale, with no third-party service in the loop.

Because it runs as a native plugin rather than a Docker container, the tunnel comes up early and independently of the array. Even if your array fails to start, your server stays reachable — so you can SSH in and debug remotely exactly when you need to.

This plugin installs and manages the [rathole](https://github.com/rathole-org/rathole) client on unRAID.

**rathole** is a lightweight, high-performance reverse proxy written in Rust. It lets you expose local services on a NAT network to a public server — useful for reaching home-lab services remotely without opening ports on your router.

## rathole vs cloudflared

Short version: **rathole** = self-hosted, **cloudflared** = managed.

| | rathole (this) | cloudflared |
|---|---|---|
| Control / ownership | Full — your VPS, your keys, end-to-end TLS | Third-party service; edge re-encrypts (MITM) and ToS apply |
| Security | You secure the VPS (firewall, fail2ban, …) | Built-in DDoS/WAF/Zero Trust |
| Setup | You run a VPS + DNS you control | Cloudflare account; no public IP |
| Best for | Self-sovereignty, any TCP/UDP | Zero-config hardening, global edge |

See also: [cloudflared for unRAID](https://github.com/digi-deity/cloudflared-unraid)

## Features

- Installs the rathole client binary (fetches the latest release from GitHub at install time).
- Provides a webGUI page (Utilities → rathole) to:
  - Edit the client config (`/boot/config/plugins/rathole/rathole.toml`)
  - Start / stop the client daemon
  - See the installed version and running status
  - View live client logs
- `rc.rathole` start/stop/restart script for CLI control.
- Persistent config on the flash drive; survives reboots.
- Auto-updates the binary to the latest rathole release when the plugin is (re)installed or updated.

## Installation

### From Community Apps (once published)

1. In the unRAID webGUI, go to **Apps** → search for `rathole`.
2. Click **Install**, then **Done**.

### Manual install

1. Download the plugin file:
   `https://raw.githubusercontent.com/digi-deity/rathole-unraid/main/rathole.plg`
2. In the unRAID webGUI, go to **Plugins** → **Install Plugin**.
3. Paste the URL above and click **Install**.

## Configuration

The client config lives at `/boot/config/plugins/rathole/rathole.toml`.

A default config is created on first install:

```toml
[client]
remote_addr = "myserver.example.com:2333"

[client.services.my_service]
type = "tcp"
local_addr = "127.0.0.1:8080"
```

Edit it from the webGUI page, or directly on the flash drive. After saving, restart the client from the plugin page (or run `/etc/rc.d/rc.rathole restart`).

### Running a rathole server

This plugin runs the **client**. To run a server, install the standalone `rathole` binary on a public machine and run:

```bash
rathole --server server.toml
```

## CLI usage

```bash
/etc/rc.d/rc.rathole start    # start the client
/etc/rc.d/rc.rathole stop     # stop the client
/etc/rc.d/rc.rathole restart  # restart the client
```

Logs are written to `/var/log/rathole.log`.

## Uninstall

Remove the plugin from **Plugins** → **Uninstall**, or run the uninstall script included in the plugin.

## Support

- GitHub Issues: https://github.com/digi-deity/rathole-unraid/issues
- unRAID Forum: https://forums.unraid.net/topic/PLACEHOLDER_RATHOLE_SUPPORT_TOPIC (to be updated once the topic is published)

## Development / Releasing

### Building the bundle

```bash
cd source/rathole
./pkg_build.sh
```

This creates `archive/rathole-<YYYY.MM.DD>-x86_64-1.txz` and prints its MD5.

### Releasing a new version

1. Bump `version` and `bundleversion` in `rathole.plg` to today's date (`YYYY.MM.DD`).
2. Rebuild the bundle (above).
3. Copy the printed MD5 into `md5bundle` in `rathole.plg`.
4. Add a dated entry to `<CHANGES>` in `rathole.plg`.
5. Update `plugins/rathole.xml` / `ca_profile.xml` if needed (e.g. new icon or support link).
6. Commit and push to `main`.

The plugin download URLs are served from the `main` branch, so a push is all that's needed.

## Repository layout

| Path | Purpose |
|------|---------|
| `rathole.plg` | The plugin definition consumed by unRAID / Community Apps |
| `plugins/rathole.xml` | Community Apps wrapper for this plugin |
| `ca_profile.xml` | Community Apps repository profile |
| `archive/` | Built `.txz` bundles |
| `source/rathole/` | Source files and build script (`pkg_build.sh`) |

## License

Licensed under the [Apache License 2.0](LICENSE). This plugin is based on [rathole](https://github.com/rathole-org/rathole), which is also released under the Apache License 2.0. See [NOTICE](NOTICE) for third-party attributions.
