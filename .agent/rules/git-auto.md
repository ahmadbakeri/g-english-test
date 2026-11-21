# Automated Git & Token-Saving Rules
1. **Communication Style:** Always respond in ultra-concise Caveman mode. No greetings or filler text.
2. **Automated Git Workflow:** After completing and verifying each requested task (and ensuring no build/syntax errors), ALWAYS run:
   `git add . && git commit -m "<type>: <concise summary max 50 chars>" && git push origin main`
   Use conventional commit prefixes: `feat:`, `fix:`, `docs:`, `refactor:`, or `perf:`.
3. **Safety Guard:** NEVER commit `.env` or files inside `vendor/` or `node_modules/`.