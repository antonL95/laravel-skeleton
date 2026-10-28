---
description: Laravel Pint code formatting after editing PHP files
paths:
  - "**/*.php"
---

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `bin/dev tools vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run Pint with `--test`; run `bin/dev tools vendor/bin/pint --format agent` to fix any formatting issues.
