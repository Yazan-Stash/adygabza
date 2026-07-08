#!/usr/bin/env bash # dev.sh — start/attach the adygabza dev session
#
# Usage:
#   ./dev.sh        start session (or attach if already running)
#   ./dev.sh stop   kill the session and all running commands

SESSION="adygabza"
DIR="$HOME/personal/Code/adygabza/"

# ── stop ──────────────────────────────────────────────────────────────────────
if [[ "$1" == "stop" ]]; then
  tmux kill-session -t "$SESSION" 2>/dev/null && echo "Session '$SESSION' stopped." || echo "No session '$SESSION' running."
  exit 0
fi

# ── attach if already running ─────────────────────────────────────────────────
if tmux has-session -t "$SESSION" 2>/dev/null; then
  echo "Attaching to existing session '$SESSION'..."
  tmux attach-session -t "$SESSION"
  exit 0
fi

# ── create session ────────────────────────────────────────────────────────────
# Window 1: editor
tmux new-session  -d -s "$SESSION" -n "editor"  -c "$DIR"
tmux send-keys    -t "$SESSION:editor"  "vim ." Enter

# Window 2: opencode
tmux new-window   -t "$SESSION" -n "opencode" -c "$DIR"
tmux send-keys    -t "$SESSION:opencode" "opencode" Enter

# Window 3: terminal
tmux new-window   -t "$SESSION" -n "terminal" -c "$DIR"

# Window 4: lazygit
tmux new-window   -t "$SESSION" -n "lazygit" -c "$DIR"
tmux send-keys    -t "$SESSION:lazygit" "lazygit" Enter

# Window 5: dev server
tmux new-window   -t "$SESSION" -n "server" -c "$DIR"
tmux send-keys    -t "$SESSION:server"  "npm run dev" Enter

# Window 6: logs
tmux new-window   -t "$SESSION" -n "logs" -c "$DIR"
tmux send-keys    -t "$SESSION:logs" "tail -f storage/logs/laravel.log" Enter

# Focus the editor window on attach
tmux select-window -t "$SESSION:editor"

# Attach
tmux attach-session -t "$SESSION"
