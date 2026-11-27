-- Run this once on your Render PostgreSQL database.
CREATE EXTENSION IF NOT EXISTS postgis;

CREATE TABLE IF NOT EXISTS incidents (
    id               SERIAL PRIMARY KEY,
    emergency_type   VARCHAR(30)  NOT NULL,
    description      TEXT         NOT NULL,
    persons_affected INTEGER      NOT NULL DEFAULT 0 CHECK (persons_affected >= 0),
    gps_accuracy     DOUBLE PRECISION,
    latitude         DOUBLE PRECISION NOT NULL,
    longitude        DOUBLE PRECISION NOT NULL,
    geom             GEOGRAPHY(Point, 4326) NOT NULL,
    photo_mime       VARCHAR(30),
    photo_data       BYTEA,
    status           VARCHAR(20)  NOT NULL DEFAULT 'new',
    created_at       TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS incidents_geom_idx ON incidents USING GIST (geom);
CREATE INDEX IF NOT EXISTS incidents_created_idx ON incidents (created_at DESC);
