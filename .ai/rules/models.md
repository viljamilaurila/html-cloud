---
paths:
  - 'app/Models/**'
---

# Models

## Production is Postgres; local is MySQL and tests are SQLite
Laravel Cloud runs this app on pgsql, while the Herd dev site uses mysql and phpunit.xml uses sqlite. Raw SQL that passes locally can still fail in production. Known trap: in an upsert, always qualify columns in DB::raw update expressions with the table name (e.g. `daily_stats.uploads + 1`), because Postgres also exposes the incoming row as `excluded` and a bare column name is ambiguous. Prefer portable, unquoted `table.column` so all three dialects accept it.
