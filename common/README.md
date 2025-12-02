Tailwind setup for course-project-itcs333-group23-sec4

Build steps (local):

1. Install dependencies:

```bash
npm install
```

2. Build Tailwind CSS to `common/dist/styles.css`:

```bash
npm run build:css
```

3. To develop with automatic rebuilds:

```bash
npm run watch:css
```

Notes:
- I added `common/theme.js` which exposes a Tailwind theme extension used by `tailwind.config.cjs`.
- `common/tailwind.css` is the Tailwind entry file. After running the build the full CSS will be in `common/dist/styles.css`.
- If you prefer a no-build approach, you can temporarily use the Tailwind CDN in each HTML file: https://cdn.tailwindcss.com and inject theme config via `tailwind.config` in a script tag (not implemented here).
