-- Performance indexes
ALTER TABLE photos 
    ADD INDEX idx_user_reg_date (user_id, reg_date),
    ADD INDEX idx_filename (filename);

ALTER TABLE urls 
    ADD UNIQUE INDEX idx_short_code (short_code),
    ADD INDEX idx_created (created_at);

ALTER TABLE users
    ADD UNIQUE INDEX idx_email (email_address);

-- Analyze for planner
ANALYZE TABLE photos, urls, users;
