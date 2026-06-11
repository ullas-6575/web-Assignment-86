CREATE TABLE IF NOT EXISTS users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  username VARCHAR(255) NOT NULL UNIQUE,
  email    VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role     ENUM('member','moderator','president') NOT NULL DEFAULT 'member',
  created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Discussions (idea pitches)
CREATE TABLE IF NOT EXISTS discussions (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  user_id  INT NOT NULL,
  title    VARCHAR(255) NOT NULL,
  body     TEXT NOT NULL,
  created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Replies on discussions
CREATE TABLE IF NOT EXISTS replies (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  discussion_id  INT NOT NULL,
  user_id        INT NOT NULL,
  body           TEXT NOT NULL,
  created        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)       REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS events (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  event_date  DATETIME NOT NULL,
  created_by  INT NULL,
  created     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

UPDATE users SET role = 'president' WHERE username = 'ullas';
UPDATE users SET role = 'moderator' WHERE username = 'shontu';
