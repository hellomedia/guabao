#!/bin/bash
# Apply the egress allowlist firewall before anything else, then run the
# requested command (normally `claude`). Fails closed: if the firewall can't
# be applied (e.g. NET_ADMIN missing in docker-compose.yaml), the container
# refuses to start rather than running unprotected.
set -euo pipefail
sudo /usr/local/bin/init-firewall.sh

# User-level instructions shared by all projects: link ~/.claude/CLAUDE.md to the
# shared folder, unless a real (non-link) CLAUDE.md is already there.
config_dir="${CLAUDE_CONFIG_DIR:-$HOME/.claude}"
if [ -d /home/node/shared ] && { [ -L "$config_dir/CLAUDE.md" ] || [ ! -e "$config_dir/CLAUDE.md" ]; }; then
    ln -sfn /home/node/shared/CLAUDE.md "$config_dir/CLAUDE.md"
fi

exec "$@"
