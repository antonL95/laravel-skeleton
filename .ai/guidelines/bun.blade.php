<bun-guidelines>
Bun is the JavaScript runtime and package manager for this project. Always use Bun. Never use npm, npx, yarn, or pnpm.

- Install dependencies with `bun install` (the lockfile is `bun.lock`; there is no `package-lock.json`).
- Add or remove packages with `bun add` / `bun add -d` / `bun remove`.
- Run package.json scripts with `bun run <script>` (e.g. `bun run dev`, `bun run build`, `bun run build:ssr`, `bun run types`, `bun run lint`, `bun run format`).
- Run one-off binaries with `bunx <tool>` instead of `npx <tool>`.

Linting and formatting use the Oxc toolchain, not ESLint or Prettier:
- `bun run lint` runs `oxlint`.
- `bun run format` runs `oxfmt` (and `bun run format:check` runs `oxfmt --check`).
</bun-guidelines>
