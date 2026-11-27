# mdrrmo-api

PHP REST API for the MDRRMO Constituents Website. It receives emergency reports,
validates them, and stores them in PostgreSQL/PostGIS. Deployed on Render with Docker.

## Endpoints
| Method | Path | Purpose |
|---|---|---|
| POST | `/api/incidents/create.php` | Submit a report |
| GET | `/api/incidents/list.php?limit=20&offset=0` | List reports |
| GET | `/api/incidents/view.php?id=4` | View one report |

### Create request
Send JSON or `multipart/form-data` (needed for a photo) with:
`emergency_type` (fire, flood, landslide, earthquake, typhoon, medical, road_accident, other),
`description` (10 to 2000 characters), `persons_affected` (0 or more), `latitude`, `longitude`,
`gps_accuracy` (optional, meters), `photo` (optional JPG/PNG/WebP, up to 3 MB).

Success: `201 {"success":true,"message":"Your report has been saved. Reference #4.","incident_id":4}`
Error: `422 {"success":false,"message":"Please select a valid emergency type.","errors":{...}}`

## Project structure
```
public/api/incidents/   create.php, list.php, view.php
src/                    database.php, db_connection.php
schema.sql              database tables (run once)
test-db.php             connection test (run from the Render Shell)
Dockerfile
```

## Deploy on Render
1. Render > New > **PostgreSQL**. Copy its **Internal Database URL**.
2. Open the database's shell or any SQL client and run `schema.sql` once.
3. Render > New > **Web Service** > connect this GitHub repo. Runtime: **Docker**.
4. Add environment variables:
   - `DATABASE_URL` = the Internal Database URL
   - `ALLOWED_ORIGIN` = `https://mdrrmo-constituents-sn92.hatchable.site`
   - `ADMIN_API_KEY` = a long random value (protects list and view)
5. Deploy, then run `php test-db.php` in the service's Shell tab.

## Security
- No passwords are stored in the code. Use environment variables only.
- All queries use prepared statements.
- Use fictional data when testing.
